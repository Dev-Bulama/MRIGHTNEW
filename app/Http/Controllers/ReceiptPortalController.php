<?php
namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\AppDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPortalController extends Controller
{
    public function showLogin()
    {
        return view('receipt-portal.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\s\-]{7,20}$/'],
            'resale_pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $phone = preg_replace('/[\s\-]/', '', $validated['phone_number']);

        // Find receipt by customer_phone and resale_pin
        $receipt = Receipt::where('customer_phone', $phone)
            ->where('resale_pin', $validated['resale_pin'])
            ->where('status', 'active')
            ->with(['shop', 'parentReceipt', 'childReceipts'])
            ->first();

        if (!$receipt) {
            // Also try with leading 0 stripped or 234 prefix
            $altPhone = ltrim($phone, '+');
            $receipt = Receipt::whereIn('customer_phone', [$phone, $altPhone, '0' . substr($phone, -10), '+234' . substr($phone, -10)])
                ->where('resale_pin', $validated['resale_pin'])
                ->where('status', 'active')
                ->with(['shop', 'parentReceipt', 'childReceipts'])
                ->first();
        }

        if (!$receipt) {
            return back()->withInput()->withErrors(['phone_number' => 'Invalid phone number or PIN. Please check your Digital Receipt for your login credentials.']);
        }

        // Store in session
        session(['receipt_portal_receipt_id' => $receipt->id, 'receipt_portal_logged_in' => true]);

        return redirect()->route('receipt.portal.dashboard');
    }

    public function dashboard()
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $downloads = AppDownload::active()->orderBy('platform')->get();

        // Get ownership history
        $ownershipHistory = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
            ->with(['shop'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('receipt-portal.dashboard', compact('receipt', 'downloads', 'ownershipHistory'));
    }

    public function declareMissing(Request $request)
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $validated = $request->validate([
            'confirm_missing' => ['required', 'accepted'],
            'missing_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $receipt->update([
            'is_missing' => true,
            'missing_reported_at' => now(),
            'missing_notes' => $validated['missing_notes'] ?? null,
        ]);

        // Also update AntiTheftPhone if exists
        $antiTheft = \App\Models\AntiTheftPhone::where('serial_number', $receipt->phone_serial_number)->first();
        if ($antiTheft) {
            $antiTheft->updateStatus(\App\Models\AntiTheftPhone::STATUS_REPORTED_STOLEN, 'Reported missing by owner via portal');
        }

        return back()->with('success', 'Your phone has been reported as missing. All searchers will be alerted.');
    }

    public function reverseMissing(Request $request)
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $receipt->update(['is_missing' => false, 'missing_reported_at' => null, 'missing_notes' => null]);

        $antiTheft = \App\Models\AntiTheftPhone::where('serial_number', $receipt->phone_serial_number)->first();
        if ($antiTheft) {
            $antiTheft->updateStatus(\App\Models\AntiTheftPhone::STATUS_RECOVERED, 'Owner confirmed phone recovered via portal');
        }

        return back()->with('success', 'Phone status has been restored. It is no longer marked as missing.');
    }

    public function downloadPdf()
    {
        $receipt = $this->getPortalReceipt();
        if (!$receipt) return redirect()->route('receipt.portal.login');

        $pdf = Pdf::loadView('receipts.pdf', ['receipt' => $receipt]);
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
