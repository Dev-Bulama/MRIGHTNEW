<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\AppDownload;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPortalController extends Controller
{
    const DECLARE_MISSING_FEE = 350000; // ₦3,500 in kobo (Paystack uses kobo)

    public function showLogin()
    {
        return view('receipt-portal.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\s\-]{7,20}$/'],
            'resale_pin'   => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $phone = preg_replace('/[\s\-]/', '', $validated['phone_number']);

        $receipt = Receipt::where('customer_phone', $phone)
            ->where('resale_pin', $validated['resale_pin'])
            ->where('status', 'active')
            ->with(['shop', 'parentReceipt', 'childReceipts'])
            ->first();

        if (!$receipt) {
            $altPhone = ltrim($phone, '+');
            $receipt = Receipt::whereIn('customer_phone', [
                    $phone, $altPhone,
                    '0' . substr($phone, -10),
                    '+234' . substr($phone, -10),
                ])
                ->where('resale_pin', $validated['resale_pin'])
                ->where('status', 'active')
                ->with(['shop', 'parentReceipt', 'childReceipts'])
                ->first();
        }

        if (!$receipt) {
            return back()->withInput()->withErrors([
                'phone_number' => 'Invalid phone number or PIN. Please check your Digital Receipt for your login credentials.',
            ]);
        }

        session(['receipt_portal_receipt_id' => $receipt->id, 'receipt_portal_logged_in' => true]);
        return redirect()->route('receipt.portal.dashboard');
    }

    public function dashboard()
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $downloads = AppDownload::active()->orderBy('platform')->get();

        $ownershipHistory = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
            ->with(['shop'])
            ->orderBy('created_at', 'asc')
            ->get();

        $declareMissingFee = self::DECLARE_MISSING_FEE / 100; // display in naira

        return view('receipt-portal.dashboard', compact('receipt', 'downloads', 'ownershipHistory', 'declareMissingFee'));
    }

    // ─── Declare Missing — initiate Paystack payment ───────────────────────────
    public function initiateMissingPayment(Request $request)
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $request->validate([
            'missing_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Store notes temporarily in session until payment is confirmed
        session(['portal_missing_notes' => $request->missing_notes]);

        $paystackKey = config('services.paystack.secret_key');
        $callbackUrl = route('receipt.portal.missing.payment.callback');
        $email       = $receipt->customer_email ?: 'noreply@mright.ng';
        $amount      = self::DECLARE_MISSING_FEE;

        try {
            $response = Http::withToken($paystackKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email'        => $email,
                    'amount'       => $amount,
                    'currency'     => 'NGN',
                    'reference'    => 'MISSING-' . $receipt->id . '-' . time(),
                    'callback_url' => $callbackUrl,
                    'metadata'     => [
                        'receipt_id'     => $receipt->id,
                        'receipt_number' => $receipt->receipt_number,
                        'purpose'        => 'declare_missing',
                    ],
                ]);

            if ($response->successful() && $response->json('status')) {
                $authUrl = $response->json('data.authorization_url');
                session(['portal_paystack_ref' => $response->json('data.reference')]);
                return redirect($authUrl);
            }

            return back()->with('error', 'Payment gateway error. Please try again.');
        } catch (\Exception $e) {
            Log::error('Paystack initiation failed: ' . $e->getMessage());
            return back()->with('error', 'Payment service unavailable. Please try again later.');
        }
    }

    // ─── Declare Missing — Paystack callback ───────────────────────────────────
    public function missingPaymentCallback(Request $request)
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $reference = $request->query('reference') ?: session('portal_paystack_ref');
        if (!$reference) {
            return redirect()->route('receipt.portal.dashboard')->with('error', 'Invalid payment reference.');
        }

        $paystackKey = config('services.paystack.secret_key');

        try {
            $response = Http::withToken($paystackKey)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $data   = $response->json('data');
            $status = $data['status'] ?? '';

            if ($status === 'success') {
                $missingNotes = session('portal_missing_notes');
                session()->forget(['portal_missing_notes', 'portal_paystack_ref']);

                $receipt->update([
                    'is_missing'          => true,
                    'missing_reported_at' => now(),
                    'missing_notes'       => $missingNotes,
                ]);

                $antiTheft = \App\Models\AntiTheftPhone::where('serial_number', $receipt->phone_serial_number)->first();
                if ($antiTheft) {
                    $antiTheft->updateStatus(
                        \App\Models\AntiTheftPhone::STATUS_REPORTED_STOLEN,
                        'Reported missing by owner via portal (payment confirmed)'
                    );
                }

                return redirect()->route('receipt.portal.dashboard')
                    ->with('success', 'Your phone has been reported as missing. All searchers will see an alert. Payment of ₦3,500 confirmed.');
            }

            return redirect()->route('receipt.portal.dashboard')
                ->with('error', 'Payment was not completed. Phone not reported missing.');
        } catch (\Exception $e) {
            Log::error('Paystack verify failed: ' . $e->getMessage());
            return redirect()->route('receipt.portal.dashboard')
                ->with('error', 'Could not verify payment. Please contact support.');
        }
    }

    public function reverseMissing(Request $request)
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $receipt->update(['is_missing' => false, 'missing_reported_at' => null, 'missing_notes' => null]);

        $antiTheft = \App\Models\AntiTheftPhone::where('serial_number', $receipt->phone_serial_number)->first();
        if ($antiTheft) {
            $antiTheft->updateStatus(
                \App\Models\AntiTheftPhone::STATUS_RECOVERED,
                'Owner confirmed phone recovered via portal'
            );
        }

        return back()->with('success', 'Phone status restored. It is no longer marked as missing.');
    }

    public function downloadPdf()
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $pdf = Pdf::loadView('receipt.pdf', ['receipt' => $receipt]);
        return $pdf->download("Phone-Anti-Theft-Receipt-{$receipt->receipt_number}.pdf");
    }

    public function logout()
    {
        session()->forget(['receipt_portal_receipt_id', 'receipt_portal_logged_in']);
        return redirect()->route('receipt.portal.login')->with('success', 'Logged out successfully.');
    }

    private function getPortalReceipt(): ?Receipt
    {
        if (!session('receipt_portal_logged_in')) return null;
        $id = session('receipt_portal_receipt_id');
        if (!$id) return null;
        return Receipt::with(['shop', 'parentReceipt', 'childReceipts'])->find($id);
    }
}
