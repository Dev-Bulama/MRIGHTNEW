<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Display listing of payout requests
     */
    public function index(Request $request)
    {
        $query = PayoutRequest::with(['user', 'shop', 'approvedBy', 'rejectedBy', 'paidBy']);

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

        // Filter by amount range
        if ($request->filled('amount_min')) {
            $query->where('amount_requested', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount_requested', '<=', $request->amount_max);
        }

        // Search by user name, email, or request number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('shop', function($shopQuery) use ($search) {
                      $shopQuery->where('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by priority (pending first, then by amount descending)
        $payoutRequests = $query->orderByRaw("
            CASE status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                WHEN 'paid' THEN 3 
                WHEN 'rejected' THEN 4 
            END
        ")->orderBy('amount_requested', 'desc')
          ->orderBy('created_at', 'desc')
          ->paginate(15);

        // Get statistics
        $stats = [
            'total_requests' => PayoutRequest::count(),
            'pending_requests' => PayoutRequest::pending()->count(),
            'approved_requests' => PayoutRequest::approved()->count(),
            'paid_requests' => PayoutRequest::paid()->count(),
            'total_pending_amount' => PayoutRequest::pending()->sum('amount_requested'),
            'total_approved_amount' => PayoutRequest::approved()->sum('amount_approved'),
            'total_paid_amount' => PayoutRequest::paid()->sum('amount_approved'),
            'recent_requests' => PayoutRequest::recent()->count(),
        ];

        return view('admin.payouts.index', compact('payoutRequests', 'stats'));
    }

    /**
     * Show payout request details
     */
    public function show(PayoutRequest $payoutRequest)
    {
        $payoutRequest->load([
            'user.shop', 
            'shop', 
            'approvedBy', 
            'rejectedBy', 
            'paidBy'
        ]);

        // Get user's receipt history and commission info
        $user = $payoutRequest->user;
        $commissionStats = [
            'total_receipts' => $user->receipts()->count(),
            'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
            'total_commission_earned' => $user->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'total_paid_out' => $user->payoutRequests()
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->sum('amount_approved') ?? 0,
            'available_balance' => $user->getAvailableCommissionBalance(),
            'pending_requests' => $user->pendingPayoutRequests()->count(),
        ];

        return view('admin.payouts.show', compact('payoutRequest', 'commissionStats'));
    }

    /**
     * Approve payout request
     */
    public function approve(Request $request, PayoutRequest $payoutRequest)
    {
        if (!$payoutRequest->canBeApproved()) {
            return back()->with('error', 'This payout request cannot be approved.');
        }

        $validated = $request->validate([
            'amount_approved' => 'nullable|numeric|min:0.01|max:' . $payoutRequest->amount_requested,
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $approvedAmount = $validated['amount_approved'] ?? $payoutRequest->amount_requested;
            $adminNotes = $validated['admin_notes'] ?? null;

            // Check if user still has sufficient balance
            $availableBalance = $payoutRequest->user->getAvailableCommissionBalance();
            if ($approvedAmount > $availableBalance) {
                return back()->with('error', 'Approved amount exceeds available commission balance.');
            }

            $payoutRequest->approve(auth()->user(), $approvedAmount, $adminNotes);

            DB::commit();

            // TODO: Send notification to shop owner
            // Mail::to($payoutRequest->user->email)->send(new PayoutApprovedMail($payoutRequest));

            return redirect()->route('admin.payouts.show', $payoutRequest)
                           ->with('success', 'Payout request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve payout request. Please try again.');
        }
    }

    /**
     * Reject payout request
     */
    public function reject(Request $request, PayoutRequest $payoutRequest)
    {
        if (!$payoutRequest->canBeRejected()) {
            return back()->with('error', 'This payout request cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            $payoutRequest->reject(auth()->user(), $validated['rejection_reason']);

            // TODO: Send notification to shop owner
            // Mail::to($payoutRequest->user->email)->send(new PayoutRejectedMail($payoutRequest));

            return redirect()->route('admin.payouts.show', $payoutRequest)
                           ->with('success', 'Payout request rejected successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject payout request. Please try again.');
        }
    }

    /**
     * Mark payout as paid
     */
    public function markPaid(Request $request, PayoutRequest $payoutRequest)
    {
        if (!$payoutRequest->canBeProcessed()) {
            return back()->with('error', 'This payout request cannot be marked as paid.');
        }

        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Update admin notes if provided
            if (isset($validated['admin_notes'])) {
                $payoutRequest->update(['admin_notes' => $validated['admin_notes']]);
            }

            $payoutRequest->markAsPaid(auth()->user(), $validated['payment_reference']);

            // TODO: Send notification to shop owner
            // Mail::to($payoutRequest->user->email)->send(new PayoutPaidMail($payoutRequest));

            return redirect()->route('admin.payouts.show', $payoutRequest)
                           ->with('success', 'Payout marked as paid successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark payout as paid. Please try again.');
        }
    }

    /**
     * Export payout requests
     */
    public function export(Request $request, $status = null)
    {
        try {
            $query = PayoutRequest::with(['user', 'shop', 'approvedBy', 'rejectedBy', 'paidBy']);

            if ($status && in_array($status, array_keys(PayoutRequest::statuses()))) {
                $query->where('status', $status);
            }

            // Apply same filters as index
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $payoutRequests = $query->orderBy('created_at', 'desc')->get();

            $exportData = [];
            foreach ($payoutRequests as $payout) {
                $exportData[] = [
                    'Request Number' => $payout->request_number,
                    'Shop Owner' => $payout->user->name,
                    'Shop Name' => $payout->shop ? $payout->shop->shop_name : 'N/A',
                    'Email' => $payout->user->email,
                    'Phone' => $payout->user->phone_number,
                    'Amount Requested' => number_format($payout->amount_requested, 2),
                    'Amount Approved' => $payout->amount_approved ? number_format($payout->amount_approved, 2) : '',
                    'Status' => $payout->status_display,
                    'Bank Name' => $payout->bank_name,
                    'Account Number' => $payout->account_number,
                    'Account Name' => $payout->account_name,
                    'Reason' => $payout->reason ?? '',
                    'Rejection Reason' => $payout->rejection_reason ?? '',
                    'Payment Reference' => $payout->payment_reference ?? '',
                    'Approved By' => $payout->approvedBy ? $payout->approvedBy->name : '',
                    'Rejected By' => $payout->rejectedBy ? $payout->rejectedBy->name : '',
                    'Paid By' => $payout->paidBy ? $payout->paidBy->name : '',
                    'Request Date' => $payout->created_at->format('Y-m-d H:i:s'),
                    'Approved Date' => $payout->approved_at ? $payout->approved_at->format('Y-m-d H:i:s') : '',
                    'Rejected Date' => $payout->rejected_at ? $payout->rejected_at->format('Y-m-d H:i:s') : '',
                    'Paid Date' => $payout->paid_at ? $payout->paid_at->format('Y-m-d H:i:s') : '',
                ];
            }

            $filename = 'payout-requests-' . ($status ? $status . '-' : '') . now()->format('Y-m-d');
            return $this->exportToCsv($exportData, $filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed. Please try again.');
        }
    }

    /**
     * Get payout statistics for dashboard (AJAX)
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'pending_count' => PayoutRequest::pending()->count(),
                'pending_amount' => PayoutRequest::pending()->sum('amount_requested'),
                'approved_count' => PayoutRequest::approved()->count(),
                'approved_amount' => PayoutRequest::approved()->sum('amount_approved'),
                'paid_today' => PayoutRequest::whereDate('paid_at', today())->count(),
                'paid_this_month' => PayoutRequest::whereMonth('paid_at', now()->month)
                                                 ->whereYear('paid_at', now()->year)
                                                 ->count(),
                'average_request_amount' => PayoutRequest::avg('amount_requested'),
                'largest_request' => PayoutRequest::max('amount_requested'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics.'
            ], 500);
        }
    }

    /**
     * Bulk actions for payout requests
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,mark_paid',
            'payout_ids' => 'required|array|min:1',
            'payout_ids.*' => 'exists:payout_requests,id',
            'bulk_reason' => 'nullable|string|max:1000',
            'bulk_payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $payouts = PayoutRequest::whereIn('id', $validated['payout_ids'])->get();
            $processed = 0;
            $errors = [];

            foreach ($payouts as $payout) {
                try {
                    switch ($validated['action']) {
                        case 'approve':
                            if ($payout->canBeApproved()) {
                                $payout->approve(auth()->user(), null, $validated['bulk_reason']);
                                $processed++;
                            } else {
                                $errors[] = "Cannot approve {$payout->request_number}";
                            }
                            break;

                        case 'reject':
                            if ($payout->canBeRejected()) {
                                $reason = $validated['bulk_reason'] ?? 'Bulk rejection';
                                $payout->reject(auth()->user(), $reason);
                                $processed++;
                            } else {
                                $errors[] = "Cannot reject {$payout->request_number}";
                            }
                            break;

                        case 'mark_paid':
                            if ($payout->canBeProcessed()) {
                                $reference = $validated['bulk_payment_reference'] ?? 'BULK-' . uniqid();
                                $payout->markAsPaid(auth()->user(), $reference);
                                $processed++;
                            } else {
                                $errors[] = "Cannot mark {$payout->request_number} as paid";
                            }
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing {$payout->request_number}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully processed {$processed} payout requests.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk action failed. Please try again.');
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