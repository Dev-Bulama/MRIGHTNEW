<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Display shop owner's payout requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied. Shop owner account required.');
        }

        $query = $user->payoutRequests()->with(['shop', 'approvedBy', 'rejectedBy', 'paidBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payoutRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get user's commission statistics
        $commissionStats = [
            'total_commission_earned' => $user->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'total_paid_out' => $user->payoutRequests()
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->sum('amount_approved') ?? 0,
            'available_balance' => $user->getAvailableCommissionBalance(),
            'pending_requests' => $user->pendingPayoutRequests()->count(),
            'total_receipts' => $user->receipts()->count(),
            'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
        ];

        // Get payout statistics
        $payoutStats = [
            'total_requests' => $user->payoutRequests()->count(),
            'pending_requests' => $user->payoutRequests()->pending()->count(),
            'approved_requests' => $user->payoutRequests()->approved()->count(),
            'paid_requests' => $user->payoutRequests()->paid()->count(),
            'rejected_requests' => $user->payoutRequests()->where('status', PayoutRequest::STATUS_REJECTED)->count(),
            'total_requested' => $user->payoutRequests()->sum('amount_requested'),
            'total_received' => $user->payoutRequests()->paid()->sum('amount_approved'),
        ];

        return view('shop.payouts.index', compact('payoutRequests', 'commissionStats', 'payoutStats'));
    }

    /**
     * Show create payout request form
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied. Shop owner account required.');
        }

        if (!$user->canRequestPayout()) {
            $reason = 'Unable to request payout. ';
            
            if (!$user->shop) {
                $reason .= 'No shop associated with your account.';
            } elseif ($user->getAvailableCommissionBalance() <= 0) {
                $reason .= 'No available commission balance.';
            } elseif ($user->pendingPayoutRequests()->count() > 0) {
                $reason .= 'You already have a pending payout request.';
            }

            return redirect()->route('shop.payouts.index')->with('error', $reason);
        }

        $availableBalance = $user->getAvailableCommissionBalance();
        $commissionStats = [
            'total_commission_earned' => $user->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'total_paid_out' => $user->payoutRequests()
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->sum('amount_approved') ?? 0,
            'available_balance' => $availableBalance,
            'pending_requests' => $user->pendingPayoutRequests()->count(),
        ];

        return view('shop.payouts.create', compact('availableBalance', 'commissionStats'));
    }

    /**
     * Store payout request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied. Shop owner account required.');
        }

        if (!$user->canRequestPayout()) {
            return back()->with('error', 'You cannot request a payout at this time.');
        }

        $availableBalance = $user->getAvailableCommissionBalance();

        $validated = $request->validate([
            'amount_requested' => "required|numeric|min:100|max:{$availableBalance}",
            'reason' => 'required|string|max:500',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20|regex:/^[0-9]+$/',
            'account_name' => 'required|string|max:255',
        ], [
            'amount_requested.min' => 'Minimum payout amount is ₦100.',
            'amount_requested.max' => 'Amount exceeds your available balance.',
            'account_number.regex' => 'Account number must contain only digits.',
        ]);

        try {
            DB::beginTransaction();

            // Double-check available balance
            $currentBalance = $user->getAvailableCommissionBalance();
            if ($validated['amount_requested'] > $currentBalance) {
                return back()->withInput()
                           ->with('error', 'Amount exceeds your current available balance.');
            }

            // Create payout request
            $payoutRequest = PayoutRequest::create([
                'user_id' => $user->id,
                'shop_id' => $user->shop->id,
                'amount_requested' => $validated['amount_requested'],
                'commission_balance' => $currentBalance,
                'reason' => $validated['reason'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => PayoutRequest::STATUS_PENDING,
            ]);

            DB::commit();

            // TODO: Send notification to admin
            // Mail::to(config('app.admin_email'))->send(new NewPayoutRequestMail($payoutRequest));

            return redirect()->route('shop.payouts.show', $payoutRequest)
                           ->with('success', 'Payout request submitted successfully! You will be notified when it is reviewed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to submit payout request. Please try again.');
        }
    }

    /**
     * Show payout request details
     */
    public function show(PayoutRequest $payoutRequest)
    {
        $user = Auth::user();

        // Check if user owns this payout request
        if ($payoutRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $payoutRequest->load(['shop', 'approvedBy', 'rejectedBy', 'paidBy']);

        // Get related data
        $commissionStats = [
            'total_commission_earned' => $user->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'total_paid_out' => $user->payoutRequests()
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->sum('amount_approved') ?? 0,
            'available_balance' => $user->getAvailableCommissionBalance(),
            'pending_requests' => $user->pendingPayoutRequests()->count(),
        ];

        return view('shop.payouts.show', compact('payoutRequest', 'commissionStats'));
    }

    /**
     * Get commission breakdown (AJAX)
     */
    public function getCommissionBreakdown()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            // Get receipts with commission
            $receipts = $user->receipts()
                ->where('payment_gateway_status', 'successful')
                ->where('commission_amount', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(['id', 'receipt_number', 'amount', 'commission_amount', 'created_at']);

            // Get payout history
            $payouts = $user->payoutRequests()
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['request_number', 'amount_approved', 'status', 'created_at']);

            $breakdown = [
                'total_commission' => $user->receipts()
                    ->where('payment_gateway_status', 'successful')
                    ->sum('commission_amount') ?? 0,
                'total_paid_out' => $user->payoutRequests()
                    ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                    ->sum('amount_approved') ?? 0,
                'available_balance' => $user->getAvailableCommissionBalance(),
                'recent_receipts' => $receipts,
                'recent_payouts' => $payouts,
            ];

            return response()->json([
                'success' => true,
                'breakdown' => $breakdown
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load commission breakdown.'
            ], 500);
        }
    }

    /**
     * Check if user can request payout (AJAX)
     */
    public function checkPayoutEligibility()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $canRequest = $user->canRequestPayout();
            $availableBalance = $user->getAvailableCommissionBalance();
            $pendingCount = $user->pendingPayoutRequests()->count();

            $eligibility = [
                'can_request' => $canRequest,
                'available_balance' => $availableBalance,
                'pending_requests' => $pendingCount,
                'has_shop' => $user->shop !== null,
                'minimum_amount' => 100, // ₦100 minimum
                'reasons' => []
            ];

            if (!$canRequest) {
                if (!$user->shop) {
                    $eligibility['reasons'][] = 'No shop associated with your account';
                }
                if ($availableBalance <= 0) {
                    $eligibility['reasons'][] = 'No available commission balance';
                }
                if ($pendingCount > 0) {
                    $eligibility['reasons'][] = 'You already have a pending payout request';
                }
            }

            return response()->json([
                'success' => true,
                'eligibility' => $eligibility
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check eligibility.'
            ], 500);
        }
    }

    /**
     * Get payout statistics for dashboard widget (AJAX)
     */
    public function getPayoutStats()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $stats = [
                'available_balance' => $user->getAvailableCommissionBalance(),
                'pending_requests' => $user->pendingPayoutRequests()->count(),
                'total_received' => $user->payoutRequests()->paid()->sum('amount_approved') ?? 0,
                'last_payout' => $user->payoutRequests()->paid()->latest()->first(),
                'next_eligible_amount' => max(100, $user->getAvailableCommissionBalance()),
                'can_request_now' => $user->canRequestPayout(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payout statistics.'
            ], 500);
        }
    }

    /**
     * Export user's payout history
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied.');
        }

        try {
            $payouts = $user->payoutRequests()
                ->with(['shop', 'approvedBy', 'rejectedBy', 'paidBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            $exportData = [];
            foreach ($payouts as $payout) {
                $exportData[] = [
                    'Request Number' => $payout->request_number,
                    'Amount Requested' => number_format($payout->amount_requested, 2),
                    'Amount Approved' => $payout->amount_approved ? number_format($payout->amount_approved, 2) : '',
                    'Status' => $payout->status_display,
                    'Bank Name' => $payout->bank_name,
                    'Account Number' => $payout->account_number,
                    'Account Name' => $payout->account_name,
                    'Reason' => $payout->reason,
                    'Rejection Reason' => $payout->rejection_reason ?? '',
                    'Payment Reference' => $payout->payment_reference ?? '',
                    'Request Date' => $payout->created_at->format('Y-m-d H:i:s'),
                    'Approved Date' => $payout->approved_at ? $payout->approved_at->format('Y-m-d H:i:s') : '',
                    'Paid Date' => $payout->paid_at ? $payout->paid_at->format('Y-m-d H:i:s') : '',
                ];
            }

            $filename = 'my-payout-history-' . now()->format('Y-m-d');
            return $this->exportToCsv($exportData, $filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed. Please try again.');
        }
    }

    /**
     * Helper method to export data to CSV
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}