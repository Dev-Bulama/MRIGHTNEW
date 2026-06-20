<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayoutController extends Controller
{
    /**
     * Display a listing of payout requests for the shop owner
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied. Shop owner account required.');
        }

        // Get payout requests for this shop owner
        $query = PayoutRequest::where('user_id', $user->id);

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

        $payoutRequests = $query->latest()->paginate(10);

        // Calculate commission stats
        $commissionStats = [
            'total_commission_earned' => $this->getTotalCommissionEarned($user),
            'total_paid_out' => $this->getTotalPaidOut($user),
            'available_balance' => $this->getAvailableBalance($user),
            'pending_requests' => PayoutRequest::where('user_id', $user->id)
                                               ->where('status', PayoutRequest::STATUS_PENDING)
                                               ->count(),
            'successful_receipts' => $user->receipts()
                                         ->where('payment_gateway_status', 'successful')
                                         ->count(),
        ];

        // Payout statistics
        $payoutStats = [
            'total_requests' => PayoutRequest::where('user_id', $user->id)->count(),
            'pending_requests' => PayoutRequest::where('user_id', $user->id)
                                               ->where('status', PayoutRequest::STATUS_PENDING)
                                               ->count(),
            'approved_requests' => PayoutRequest::where('user_id', $user->id)
                                                ->where('status', PayoutRequest::STATUS_APPROVED)
                                                ->count(),
            'paid_requests' => PayoutRequest::where('user_id', $user->id)
                                           ->where('status', PayoutRequest::STATUS_PAID)
                                           ->count(),
        ];

        return view('shop.payouts.index', compact(
            'payoutRequests', 
            'commissionStats', 
            'payoutStats'
        ));
    }

    /**
     * Show the form for creating a new payout request
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied. Shop owner account required.');
        }

        // Check if user can request payout
        if (!$user->canRequestPayout()) {
            return redirect()->route('shop.payouts.index')
                           ->with('error', 'You cannot request a payout at this time. Please check your commission balance and pending requests.');
        }

        $availableBalance = $this->getAvailableBalance($user);
        $commissionStats = [
            'total_commission_earned' => $this->getTotalCommissionEarned($user),
            'total_paid_out' => $this->getTotalPaidOut($user),
            'available_balance' => $availableBalance,
        ];

        return view('shop.payouts.create', compact('availableBalance', 'commissionStats'));
    }

    /**
     * Store a newly created payout request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            abort(403, 'Access denied.');
        }

        // Check if user can request payout
        if (!$user->canRequestPayout()) {
            return back()->with('error', 'You cannot request a payout at this time.');
        }

        $availableBalance = $this->getAvailableBalance($user);

        $validated = $request->validate([
            'amount_requested' => [
                'required',
                'numeric',
                'min:100',
                'max:' . $availableBalance
            ],
            'reason' => 'required|string|max:500',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            PayoutRequest::create([
                'user_id' => $user->id,
                'request_number' => $this->generateRequestNumber(),
                'amount_requested' => $validated['amount_requested'],
                'commission_balance' => $availableBalance,
                'reason' => $validated['reason'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => PayoutRequest::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->route('shop.payouts.index')
                           ->with('success', 'Payout request submitted successfully. You will be notified once it is reviewed.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                        ->with('error', 'Failed to submit payout request. Please try again.');
        }
    }

    /**
     * Display the specified payout request
     */
    public function show(PayoutRequest $payoutRequest)
    {
        $user = Auth::user();

        if (!$user->isShopOwner() || $payoutRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $commissionStats = [
            'total_commission_earned' => $this->getTotalCommissionEarned($user),
            'total_paid_out' => $this->getTotalPaidOut($user),
            'available_balance' => $this->getAvailableBalance($user),
        ];

        return view('shop.payouts.show', compact('payoutRequest', 'commissionStats'));
    }

    /**
     * Calculate total commission earned
     */
    private function getTotalCommissionEarned($user)
    {
        return $user->receipts()
                   ->where('payment_gateway_status', 'successful')
                   ->sum('commission_amount') ?? 0;
    }

    /**
     * Calculate total paid out
     */
    private function getTotalPaidOut($user)
    {
        return PayoutRequest::where('user_id', $user->id)
                          ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                          ->sum('amount_approved') ?? 0;
    }

    /**
     * Calculate available balance
     */
    private function getAvailableBalance($user)
    {
        $totalEarned = $this->getTotalCommissionEarned($user);
        $totalPaidOut = $this->getTotalPaidOut($user);
        
        return max(0, $totalEarned - $totalPaidOut);
    }

    /**
     * Generate unique request number
     */
    private function generateRequestNumber()
    {
        do {
            $number = 'PR-' . date('Ymd') . '-' . strtoupper(uniqid());
        } while (PayoutRequest::where('request_number', $number)->exists());

        return $number;
    }

    /**
     * Check payout eligibility (AJAX)
     */
    public function checkEligibility()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $availableBalance = $this->getAvailableBalance($user);
        $pendingRequests = PayoutRequest::where('user_id', $user->id)
                                       ->where('status', PayoutRequest::STATUS_PENDING)
                                       ->count();

        $canRequest = $user->canRequestPayout();
        $reasons = [];

        if (!$canRequest) {
            if ($availableBalance < 100) {
                $reasons[] = 'Minimum balance of ₦100 required';
            }
            if ($pendingRequests > 0) {
                $reasons[] = 'You have a pending payout request';
            }
        }

        return response()->json([
            'success' => true,
            'eligibility' => [
                'can_request' => $canRequest,
                'available_balance' => $availableBalance,
                'minimum_amount' => 100,
                'pending_requests' => $pendingRequests,
                'reasons' => $reasons
            ]
        ]);
    }

    /**
     * Get commission breakdown (AJAX)
     */
    public function commissionBreakdown()
    {
        $user = Auth::user();

        if (!$user->isShopOwner()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $breakdown = [
            'total_commission' => $this->getTotalCommissionEarned($user),
            'total_paid_out' => $this->getTotalPaidOut($user),
            'available_balance' => $this->getAvailableBalance($user),
            'recent_receipts' => $user->receipts()
                                    ->where('payment_gateway_status', 'successful')
                                    ->latest()
                                    ->take(10)
                                    ->get()
                                    ->map(function($receipt) {
                                        return [
                                            'receipt_number' => $receipt->receipt_number,
                                            'amount' => $receipt->amount,
                                            'commission_amount' => $receipt->commission_amount,
                                            'created_at' => $receipt->created_at->toISOString()
                                        ];
                                    }),
            'recent_payouts' => PayoutRequest::where('user_id', $user->id)
                                           ->latest()
                                           ->take(5)
                                           ->get()
                                           ->map(function($payout) {
                                               return [
                                                   'request_number' => $payout->request_number,
                                                   'amount_approved' => $payout->amount_approved ?? $payout->amount_requested,
                                                   'status' => $payout->status,
                                                   'created_at' => $payout->created_at->toISOString()
                                               ];
                                           })
        ];

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown
        ]);
    }
}