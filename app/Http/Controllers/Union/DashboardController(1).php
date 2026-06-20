<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\PayoutRequest;
use App\Services\NigeriaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Union dashboard with location-based filtering
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied. Union account required.');
        }

        // Get filter parameters
        $selectedState = $request->get('state');
        $selectedLga = $request->get('lga');
        $searchTerm = $request->get('search');

        // Get available states for this union user
        $availableStates = $user->assigned_states ?? [];
        
        // If a specific state is selected, validate and use it
        if ($selectedState && in_array($selectedState, $availableStates)) {
            $filterStates = [$selectedState];
        } else {
            $filterStates = $availableStates;
        }

        // Get manageable shop owners with location filtering
        $manageableShopOwners = $this->getFilteredShopOwners($user, $filterStates, $selectedLga, $searchTerm);
        
        // Calculate statistics
        $stats = $this->calculateStatistics($manageableShopOwners);
        $receiptStats = $this->calculateReceiptStatistics($manageableShopOwners);
        $payoutStats = $this->calculatePayoutStatistics($manageableShopOwners);
        
        // Get state performance data
        $statePerformance = $this->getStatePerformance($user, $filterStates);
        
        // Get LGA breakdown if specific state is selected
        $lgaBreakdown = [];
        if ($selectedState) {
            $lgaBreakdown = $this->getLgaBreakdown($user, $selectedState);
        }

        // Get recent activities
        $recentActivities = $this->getRecentActivities($manageableShopOwners->pluck('id'));
        
        // Get recent shop owners for display
        $recentShopOwners = $manageableShopOwners->with('shop')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get alerts
        $alerts = $this->getSystemAlerts($manageableShopOwners);

        return view('union.dashboard', compact(
            'stats',
            'receiptStats', 
            'payoutStats',
            'statePerformance',
            'lgaBreakdown',
            'recentShopOwners',
            'recentActivities',
            'alerts',
            'availableStates',
            'selectedState',
            'selectedLga',
            'searchTerm'
        ));
    }

    /**
     * Get filtered shop owners based on location and search
     */
    // private function getFilteredShopOwners($user, $filterStates, $selectedLga = null, $searchTerm = null)
    // {
    //     $query = User::where('user_type', User::TYPE_SHOP_OWNER)
    //         ->whereHas('shop', function($q) use ($filterStates, $selectedLga) {
    //             $q->whereIn('state', $filterStates);
                
    //             if ($selectedLga) {
    //                 $q->where('local_government', $selectedLga);
    //             }
    //         });

    //     // Apply search filter
    //     if ($searchTerm) {
    //         $query->where(function($q) use ($searchTerm) {
    //             $q->where('name', 'like', "%{$searchTerm}%")
    //               ->orWhere('email', 'like', "%{$searchTerm}%")
    //               ->orWhere('phone_number', 'like', "%{$searchTerm}%")
    //               ->orWhereHas('shop', function($shopQuery) use ($searchTerm) {
    //                   $shopQuery->where('shop_name', 'like', "%{$searchTerm}%");
    //               });
    //         });
    //     }

    //     return $query;
    // }
    /**
 * Get filtered shop owners based on location and search (OPTIMIZED)
 */
/**
 * Get filtered shop owners based on location and search (OPTIMIZED VERSION)
 */
/**
 * Get filtered shop owners based on location and search (OPTIMIZED VERSION)
 */

/**
 * Get filtered shop owners based on location and search (OPTIMIZED VERSION)
 */
private function getFilteredShopOwners($user, $filterStates, $selectedLga = null, $searchTerm = null)
{
    // Start with optimized base query - select only needed columns for performance
    $query = User::select([
            'users.id',
            'users.name', 
            'users.email',
            'users.phone_number',
            'users.status',
            'users.created_at'
        ])
        ->where('users.user_type', User::TYPE_SHOP_OWNER);

    // Apply location filtering first (most restrictive) - IMPROVED LOGIC
    if (!empty($filterStates)) {
        $query->whereHas('shop', function($q) use ($filterStates, $selectedLga, $user) {
            // Filter by assigned states
            $q->whereIn('state', $filterStates);
            
            // Handle LGA filtering with proper user assignment validation
            if ($selectedLga) {
                // Ensure user can manage this specific LGA
                $userAssignedLgas = $user->assigned_lgas ?? [];
                if (!empty($userAssignedLgas) && !in_array($selectedLga, $userAssignedLgas)) {
                    // User not assigned to this LGA, return no results
                    $q->whereRaw('1 = 0');
                } else {
                    $q->where('local_government', $selectedLga);
                }
            } else {
                // If no specific LGA selected, filter by user's assigned LGAs
                $userAssignedLgas = $user->assigned_lgas ?? [];
                if (!empty($userAssignedLgas)) {
                    $q->whereIn('local_government', $userAssignedLgas);
                }
                // If user has no assigned LGAs, they can see all LGAs in their assigned states
            }
        });
    } else {
        // No states available, return empty result
        $query->whereRaw('1 = 0');
    }

    // Apply search filter with improved performance
    if ($searchTerm && strlen(trim($searchTerm)) >= 2) {
        $searchTerm = trim($searchTerm);
        
        // Use more efficient search with subquery approach
        $query->where(function($q) use ($searchTerm) {
            // Search in user fields (these should be indexed)
            $q->where('users.name', 'like', "%{$searchTerm}%")
              ->orWhere('users.email', 'like', "%{$searchTerm}%");
            
            // Only search phone numbers if search term looks like a phone number
            if (preg_match('/^[\d\+\-\s\(\)]+$/', $searchTerm)) {
                $q->orWhere('users.phone_number', 'like', "%{$searchTerm}%");
            }
            
            // Only search shop names for longer terms (3+ characters) to avoid expensive operations
            if (strlen($searchTerm) >= 3) {
                $q->orWhereHas('shop', function($shopQuery) use ($searchTerm) {
                    $shopQuery->where('shop_name', 'like', "%{$searchTerm}%");
                });
            }
        });
    }

    // Add proper eager loading to avoid N+1 queries
    $query->with(['shop:id,user_id,shop_name,state,local_government,status']);

    return $query;
}
/**
 * Calculate basic shop owner statistics
 */
private function calculateStatistics($shopOwnersQuery)
{
    // Get the base query
    $baseQuery = clone $shopOwnersQuery;
    
    // Calculate basic statistics
    $totalShopOwners = $baseQuery->count();
    
    $activeShopOwners = (clone $shopOwnersQuery)
        ->where('users.status', 'active')
        ->count();
    
    $newThisMonth = (clone $shopOwnersQuery)
        ->where('users.created_at', '>=', now()->startOfMonth())
        ->count();
    
    // Calculate high performers (shop owners with >5 successful receipts this month)
    $highPerformers = (clone $shopOwnersQuery)
        ->whereHas('receipts', function($q) {
            $q->where('payment_gateway_status', 'successful')
              ->where('created_at', '>=', now()->startOfMonth())
              ->havingRaw('COUNT(*) > 5');
        })
        ->count();

    return [
        'total_shop_owners' => $totalShopOwners,
        'active' => $activeShopOwners,
        'inactive' => $totalShopOwners - $activeShopOwners,
        'new_this_month' => $newThisMonth,
        'high_performers' => $highPerformers,
    ];
}

/**
 * Calculate receipt statistics
 */
/**
 * Calculate receipt statistics - FIXED VERSION (user_id not shop_owner_id)
 */
private function calculateReceiptStatistics($shopOwnersQuery)
{
    $shopOwnerIds = (clone $shopOwnersQuery)->pluck('users.id');
    
    if ($shopOwnerIds->isEmpty()) {
        return [
            'total_receipts' => 0,
            'successful_receipts' => 0,
            'pending_receipts' => 0,
            'failed_receipts' => 0,
            'total_revenue' => 0,
            'this_month_receipts' => 0,
            'this_month_revenue' => 0,
            'success_rate' => 0,
        ];
    }

    $totalReceipts = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->count();

    $successfulReceipts = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('payment_gateway_status', 'successful')
        ->count();

    $pendingReceipts = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('payment_gateway_status', 'pending')
        ->count();

    $failedReceipts = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('payment_gateway_status', 'failed')
        ->count();

    $totalRevenue = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('payment_gateway_status', 'successful')
        ->sum('amount') ?? 0;

    $thisMonthReceipts = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('created_at', '>=', now()->startOfMonth())
        ->count();

    $thisMonthRevenue = DB::table('receipts')
        ->whereIn('user_id', $shopOwnerIds)  // FIXED: user_id not shop_owner_id
        ->where('payment_gateway_status', 'successful')
        ->where('created_at', '>=', now()->startOfMonth())
        ->sum('amount') ?? 0;

    $successRate = $totalReceipts > 0 ? round(($successfulReceipts / $totalReceipts) * 100, 1) : 0;

    return [
        'total_receipts' => $totalReceipts,
        'successful_receipts' => $successfulReceipts,
        'pending_receipts' => $pendingReceipts,
        'failed_receipts' => $failedReceipts,
        'total_revenue' => $totalRevenue,
        'this_month_receipts' => $thisMonthReceipts,
        'this_month_revenue' => $thisMonthRevenue,
        'success_rate' => $successRate,
    ];
}

// /**
//  * Get state performance data
//  */
// private function getStatePerformance($user, $filterStates)
// {
//     if (empty($filterStates)) {
//         return collect();
//     }

//     $userAssignedLgas = $user->assigned_lgas ?? [];

//     $query = DB::table('shops')
//         ->join('users', 'shops.user_id', '=', 'users.id')
//         ->leftJoin('receipts', 'users.id', '=', 'receipts.user_id')
//         ->whereIn('shops.state', $filterStates)
//         ->where('users.user_type', 'shop_owner');

//     // Filter by assigned LGAs if user has specific LGA assignments
//     if (!empty($userAssignedLgas)) {
//         $query->whereIn('shops.local_government', $userAssignedLgas);
//     }

//     return $query->select([
//             'shops.state',
//             DB::raw('COUNT(DISTINCT shops.id) as shop_count'),
//             DB::raw('COUNT(receipts.id) as total_receipts'),
//             DB::raw('SUM(CASE WHEN receipts.payment_gateway_status = "successful" THEN receipts.amount ELSE 0 END) as total_revenue')
//         ])
//         ->groupBy('shops.state')
//         ->orderBy('shop_count', 'desc')
//         ->get();
// }

// /**
//  * Get LGA breakdown for specific state
//  */
// private function getLgaBreakdown($user, $selectedState)
// {
//     $userAssignedLgas = $user->assigned_lgas ?? [];

//     $query = DB::table('shops')
//         ->join('users', 'shops.user_id', '=', 'users.id')
//         ->leftJoin('receipts', 'users.id', '=', 'receipts.shop_owner_id')
//         ->where('shops.state', $selectedState)
//         ->where('users.user_type', 'shop_owner');

//     // Filter by assigned LGAs if user has specific LGA assignments
//     if (!empty($userAssignedLgas)) {
//         $query->whereIn('shops.local_government', $userAssignedLgas);
//     }

//     return $query->select([
//             'shops.local_government as lga',
//             DB::raw('COUNT(DISTINCT shops.id) as shop_count'),
//             DB::raw('COUNT(receipts.id) as total_receipts'),
//             DB::raw('COUNT(CASE WHEN receipts.payment_gateway_status = "successful" THEN 1 END) as successful_receipts'),
//             DB::raw('SUM(CASE WHEN receipts.payment_gateway_status = "successful" THEN receipts.amount ELSE 0 END) as total_revenue'),
//             DB::raw('COUNT(CASE WHEN receipts.created_at >= "' . now()->startOfMonth()->toDateString() . '" THEN 1 END) as receipts_this_month')
//         ])
//         ->groupBy('shops.local_government')
//         ->orderBy('shop_count', 'desc')
//         ->get();
// }

/**
 * Get recent activities
 */
// private function getRecentActivities($shopOwnerIds)
// {
//     if ($shopOwnerIds->isEmpty()) {
//         return collect();
//     }

//     return DB::table('receipts')
//         ->join('users', 'receipts.shop_owner_id', '=', 'users.id')
//         ->join('shops', 'users.id', '=', 'shops.user_id')
//         ->whereIn('receipts.shop_owner_id', $shopOwnerIds)
//         ->select([
//             'receipts.id',
//             'receipts.receipt_number',
//             'receipts.amount',
//             'receipts.payment_gateway_status',
//             'receipts.created_at',
//             'users.name as shop_owner_name',
//             'shops.shop_name'
//         ])
//         ->orderBy('receipts.created_at', 'desc')
//         ->limit(10)
//         ->get();
// }



/**
     * Calculate receipt-related statistics
     */
    // private function calculateReceiptStatistics($shopOwnersQuery)
    // {
    //     $shopOwnerIds = $shopOwnersQuery->pluck('id');
        
    //     return [
    //         'total_receipts' => DB::table('receipts')
    //             ->whereIn('user_id', $shopOwnerIds)
    //             ->count(),
    //         'receipts_this_month' => DB::table('receipts')
    //             ->whereIn('user_id', $shopOwnerIds)
    //             ->whereMonth('created_at', now()->month)
    //             ->whereYear('created_at', now()->year)
    //             ->count(),
    //         'successful_receipts' => DB::table('receipts')
    //             ->whereIn('user_id', $shopOwnerIds)
    //             ->where('payment_gateway_status', 'successful')
    //             ->count(),
    //         'total_revenue' => DB::table('receipts')
    //             ->whereIn('user_id', $shopOwnerIds)
    //             ->where('payment_gateway_status', 'successful')
    //             ->sum('amount') ?? 0,
    //         'revenue_this_month' => DB::table('receipts')
    //             ->whereIn('user_id', $shopOwnerIds)
    //             ->where('payment_gateway_status', 'successful')
    //             ->whereMonth('created_at', now()->month)
    //             ->whereYear('created_at', now()->year)
    //             ->sum('amount') ?? 0,
    //     ];
    // }

    /**
     * Calculate payout-related statistics
     */
    private function calculatePayoutStatistics($shopOwnersQuery)
    {
        $shopOwnerIds = $shopOwnersQuery->pluck('id');
        
        return [
            'pending_payouts' => PayoutRequest::whereIn('user_id', $shopOwnerIds)
                ->where('status', PayoutRequest::STATUS_PENDING)
                ->count(),
            'total_payout_requests' => PayoutRequest::whereIn('user_id', $shopOwnerIds)->count(),
            'total_commission_earned' => DB::table('receipts')
                ->whereIn('user_id', $shopOwnerIds)
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'total_paid_out' => PayoutRequest::whereIn('user_id', $shopOwnerIds)
                ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
                ->sum('amount_approved') ?? 0,
        ];
    }

    /**
     * Get performance data by state
     */
   private function getStatePerformance($user, $filterStates)
{
    if (empty($filterStates)) {
        return collect();
    }

    $userAssignedLgas = $user->assigned_lgas ?? [];

    $query = DB::table('shops')
        ->join('users', 'shops.user_id', '=', 'users.id')
        ->leftJoin('receipts', 'users.id', '=', 'receipts.user_id')  // FIXED: user_id not shop_owner_id
        ->whereIn('shops.state', $filterStates)
        ->where('users.user_type', 'shop_owner');

    // Filter by assigned LGAs if user has specific LGA assignments
    if (!empty($userAssignedLgas)) {
        $query->whereIn('shops.local_government', $userAssignedLgas);
    }

    return $query->select([
            'shops.state',
            DB::raw('COUNT(DISTINCT shops.id) as shop_count'),
            DB::raw('COUNT(receipts.id) as total_receipts'),
            DB::raw('SUM(CASE WHEN receipts.payment_gateway_status = "successful" THEN receipts.amount ELSE 0 END) as total_revenue')
        ])
        ->groupBy('shops.state')
        ->orderBy('shop_count', 'desc')
        ->get();
}

   /**
 * Get LGA breakdown for specific state - FIXED VERSION (user_id not shop_owner_id)
 */
private function getLgaBreakdown($user, $selectedState)
{
    $userAssignedLgas = $user->assigned_lgas ?? [];

    $query = DB::table('shops')
        ->join('users', 'shops.user_id', '=', 'users.id')
        ->leftJoin('receipts', 'users.id', '=', 'receipts.user_id')  // FIXED: user_id not shop_owner_id
        ->where('shops.state', $selectedState)
        ->where('users.user_type', 'shop_owner');

    // Filter by assigned LGAs if user has specific LGA assignments
    if (!empty($userAssignedLgas)) {
        $query->whereIn('shops.local_government', $userAssignedLgas);
    }

    return $query->select([
            'shops.local_government as lga',
            DB::raw('COUNT(DISTINCT shops.id) as shop_count'),
            DB::raw('COUNT(receipts.id) as total_receipts'),
            DB::raw('COUNT(CASE WHEN receipts.payment_gateway_status = "successful" THEN 1 END) as successful_receipts'),
            DB::raw('SUM(CASE WHEN receipts.payment_gateway_status = "successful" THEN receipts.amount ELSE 0 END) as total_revenue'),
            DB::raw('COUNT(CASE WHEN receipts.created_at >= "' . now()->startOfMonth()->toDateString() . '" THEN 1 END) as receipts_this_month')
        ])
        ->groupBy('shops.local_government')
        ->orderBy('shop_count', 'desc')
        ->get();
}

    /**
     * Get high performing shop owners
     */
    private function getHighPerformers($shopOwners)
    {
        return $shopOwners->filter(function($shopOwner) {
            $receiptsThisMonth = $shopOwner->receipts()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            return $receiptsThisMonth >= 5; // 5 or more receipts this month = high performer
        });
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($shopOwnerIds)
    {
        $activities = [];

        // Recent registrations
        $recentRegistrations = User::whereIn('id', $shopOwnerIds)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with('shop')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRegistrations as $user) {
            $activities[] = [
                'type' => 'registration',
                'title' => 'New Shop Owner Registration',
                'description' => "{$user->name} registered" . ($user->shop ? " with shop '{$user->shop->shop_name}'" : ''),
                'time' => $user->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => 'success',
            ];
        }

        // Recent receipts
        $recentReceipts = DB::table('receipts')
            ->join('users', 'receipts.user_id', '=', 'users.id')
            ->join('shops', 'users.id', '=', 'shops.user_id')
            ->whereIn('receipts.user_id', $shopOwnerIds)
            ->where('receipts.created_at', '>=', Carbon::now()->subDays(7))
            ->select('receipts.*', 'users.name as user_name', 'shops.shop_name')
            ->orderBy('receipts.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentReceipts as $receipt) {
            $status = $receipt->payment_gateway_status === 'successful' ? 'success' : 'warning';
            $activities[] = [
                'type' => 'receipt',
                'title' => 'New Receipt Generated',
                'description' => "{$receipt->user_name} generated receipt #{$receipt->receipt_number} (₦" . number_format($receipt->amount) . ")",
                'time' => Carbon::parse($receipt->created_at),
                'icon' => 'fas fa-receipt',
                'color' => $status,
            ];
        }

        // Sort by time and return latest 10
        usort($activities, function($a, $b) {
            return $b['time']->timestamp <=> $a['time']->timestamp;
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts($shopOwnersQuery)
    {
        $alerts = [];
        $shopOwnerIds = $shopOwnersQuery->pluck('id');

        // Check for inactive users
        $inactiveUsers = $shopOwnersQuery->where('status', '!=', User::STATUS_ACTIVE)->count();
        if ($inactiveUsers > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Inactive Shop Owners',
                'message' => "{$inactiveUsers} shop owners are currently inactive.",
                'action_url' => route('union.shop-owners.index', ['filter' => 'inactive']),
                'action_text' => 'View Inactive',
            ];
        }

        // Check for low performing shops
        $lowPerforming = User::whereIn('id', $shopOwnerIds)
            ->whereDoesntHave('receipts', function($query) {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            })
            ->where('created_at', '<=', Carbon::now()->subWeek())
            ->count();

        if ($lowPerforming > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Low Performance Alert',
                'message' => "{$lowPerforming} shops haven't generated any receipts this month.",
                'action_url' => route('union.shop-owners.index', ['filter' => 'low_performance']),
                'action_text' => 'Review Performance',
            ];
        }

        return $alerts;
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStatistics(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            // Get filter parameters
            $selectedState = $request->get('state');
            $selectedLga = $request->get('lga');
            $searchTerm = $request->get('search');

            // Get available states for this union user
            $availableStates = $user->assigned_states ?? [];
            
            // If a specific state is selected, validate and use it
            if ($selectedState && in_array($selectedState, $availableStates)) {
                $filterStates = [$selectedState];
            } else {
                $filterStates = $availableStates;
            }

            // Get manageable shop owners with filtering
            $manageableShopOwners = $this->getFilteredShopOwners($user, $filterStates, $selectedLga, $searchTerm);
            
            // Calculate statistics
            $stats = $this->calculateStatistics($manageableShopOwners);
            $receiptStats = $this->calculateReceiptStatistics($manageableShopOwners);
            $payoutStats = $this->calculatePayoutStatistics($manageableShopOwners);

            return response()->json([
                'success' => true,
                'stats' => [
                    'shop_owners' => $stats,
                    'receipts' => $receiptStats,
                    'payouts' => $payoutStats,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get LGAs for a specific state (AJAX)
     */
     /**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs)
 */
 /**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs) - FIXED VERSION
 */
/**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs) - FIXED VERSION
 */
/**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs) - FIXED VERSION
 */
/**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs) - DEBUG VERSION
 */
/**
 * Get LGAs for a specific state (ONLY ASSIGNED LGAs) - FIXED VERSION
 */
public function getLgas(Request $request)
{
    try {
        $user = Auth::user();
        $state = $request->input('state');
        
        // Debug: Log what we received
        Log::info('DEBUG - getLgas called', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'requested_state' => $state,
            'user_assigned_states' => $user->assigned_states,
            'user_assigned_lgas' => $user->assigned_lgas,
        ]);
        
        if (!$state) {
            return response()->json(['success' => false, 'message' => 'State is required'], 400);
        }

        // ✅ FIXED: Check if user can manage this state (simplified logic)
        $userAssignedStates = $user->assigned_states ?? [];
        if (!in_array($state, $userAssignedStates)) {
            Log::warning('DEBUG - User cannot manage state', [
                'user_id' => $user->id,
                'requested_state' => $state,
                'user_assigned_states' => $userAssignedStates
            ]);
            return response()->json(['success' => false, 'message' => 'Access denied for this state'], 403);
        }

        // Get user's assigned LGAs
        $userAssignedLgas = $user->assigned_lgas ?? [];
        
        // Debug: Check if user has assigned LGAs
        Log::info('DEBUG - User assigned LGAs', [
            'user_assigned_lgas' => $userAssignedLgas,
            'lgas_count' => count($userAssignedLgas),
            'lgas_empty' => empty($userAssignedLgas)
        ]);
        
        // If user has NO assigned LGAs, they shouldn't see any LGAs
        if (empty($userAssignedLgas)) {
            Log::info('DEBUG - No assigned LGAs for user', ['user_id' => $user->id]);
            return response()->json([
                'success' => true,
                'lgas' => [],
                'total_count' => 0,
                'message' => 'No LGAs assigned to this user',
                'debug_info' => [
                    'user_assigned_lgas' => [],
                    'state' => $state,
                    'filtered_count' => 0
                ]
            ]);
        }

        // ✅ FIXED: Get all LGAs for the state with proper error handling
        try {
            $allStateLgas = NigeriaData::lgas($state);
        } catch (\Exception $e) {
            Log::error('NigeriaData::lgas failed', [
                'state' => $state,
                'error' => $e->getMessage()
            ]);
            
            // Fallback: Use basic LGA data if NigeriaData fails
            $allStateLgas = $this->getFallbackLgas($state);
        }
        
        // Debug: Check what LGAs exist for this state
        Log::info('DEBUG - State LGAs from NigeriaData', [
            'state' => $state,
            'all_state_lgas' => $allStateLgas,
            'state_lgas_count' => count($allStateLgas)
        ]);
        
        // Filter to show ONLY LGAs that are:
        // 1. In the selected state AND 
        // 2. Assigned to the user
        $filteredLgas = array_values(array_intersect($allStateLgas, $userAssignedLgas));
        
        // Debug: Check filtering result
        Log::info('DEBUG - LGA filtering result', [
            'state' => $state,
            'user_assigned_lgas' => $userAssignedLgas,
            'all_state_lgas' => $allStateLgas,
            'filtered_lgas' => $filteredLgas,
            'filtered_count' => count($filteredLgas),
            'intersection_result' => array_intersect($allStateLgas, $userAssignedLgas)
        ]);
        
        // Sort the LGAs alphabetically
        sort($filteredLgas);
        
        return response()->json([
            'success' => true,
            'lgas' => $filteredLgas,
            'total_count' => count($filteredLgas),
            'debug_info' => [
                'user_assigned_lgas' => $userAssignedLgas,
                'state_total_lgas' => count($allStateLgas),
                'filtered_count' => count($filteredLgas),
                'state' => $state,
                'all_state_lgas' => $allStateLgas,
                'intersection_result' => array_intersect($allStateLgas, $userAssignedLgas)
            ]
        ]);
        
    } catch (\Exception $e) {
        // ✅ FIXED: Proper error handling for 500 errors
        Log::error('getLgas method failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
            'state' => $request->input('state')
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Server error while loading LGAs: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * ✅ FIXED: Fallback LGA data if NigeriaData service fails
 */
private function getFallbackLgas($state)
{
    $fallbackData = [
        'Adamawa' => ['Demsa', 'Fufore', 'Ganye', 'Girei', 'Gombi', 'Guyuk', 'Hong', 'Jada', 'Lamurde', 'Madagali', 'Maiha', 'Mayo-Belwa', 'Michika', 'Mubi North', 'Mubi South', 'Numan', 'Shelleng', 'Song', 'Toungo', 'Yola North', 'Yola South'],
        'Borno' => ['Abadam', 'Askira/Uba', 'Bama', 'Bayo', 'Biu', 'Chibok', 'Damboa', 'Dikwa', 'Gubio', 'Guzamala', 'Gwoza', 'Hawul', 'Jere', 'Kaga', 'Kala/Balge', 'Konduga', 'Kukawa', 'Kwaya Kusar', 'Mafa', 'Magumeri', 'Maiduguri', 'Marte', 'Mobbar', 'Monguno', 'Ngala', 'Nganzai', 'Shani'],
        'Lagos' => ['Agege', 'Ajeromi-Ifelodun', 'Alimosho', 'Amuwo-Odofin', 'Apapa', 'Badagry', 'Epe', 'Eti Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye', 'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland', 'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere']
    ];
    
    return $fallbackData[$state] ?? [];
}
/**
 * Get system alerts and notifications
 */
// 
/**
 * Get high performing shop owners
 */
// private function getHighPerformers($shopOwners)
// {
//     return $shopOwners->filter(function($shopOwner) {
//         // A high performer has more than 10 successful receipts this month
//         $successfulReceiptsThisMonth = DB::table('receipts')
//             ->where('shop_owner_id', $shopOwner->id)
//             ->where('payment_gateway_status', 'successful')
//             ->where('created_at', '>=', now()->startOfMonth())
//             ->count();
            
//         return $successfulReceiptsThisMonth > 10;
//     });
// }
// public function getLgas($state)
// {
//     $user = Auth::user();
    
//     if (!$state) {
//         return response()->json(['success' => false, 'message' => 'State is required'], 400);
//     }

//     // Check if user can manage this state
//     if (!$user->canManageState($state)) {
//         return response()->json(['success' => false, 'message' => 'Access denied for this state'], 403);
//     }

//     // Get all LGAs for the state from NigeriaData
//     $allStateLgas = NigeriaData::lgas($state);
    
//     // Filter by user's assigned LGAs
//     $assignedLgas = $user->assigned_lgas ?? [];
    
//     if (!empty($assignedLgas)) {
//         // Only show LGAs that are both in the state AND assigned to the user
//         $filteredLgas = array_values(array_intersect($allStateLgas, $assignedLgas));
//     } else {
//         // If no specific LGAs assigned, show all LGAs in assigned states
//         // This handles cases where user is assigned entire states
//         $filteredLgas = $allStateLgas;
//     }
    
//     // Sort the LGAs alphabetically
//     sort($filteredLgas);
    
//     return response()->json([
//         'success' => true,
//         'lgas' => $filteredLgas,
//         'total_count' => count($filteredLgas),
//         'debug_info' => [
//             'user_assigned_lgas' => $assignedLgas,
//             'state_total_lgas' => count($allStateLgas),
//             'filtered_count' => count($filteredLgas)
//         ]
//     ]);
// }

    /**
     * Export union performance report
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        // Get filter parameters
        $selectedState = $request->get('state');
        $selectedLga = $request->get('lga');

        // Apply same filtering logic
        $availableStates = $user->assigned_states ?? [];
        if ($selectedState && in_array($selectedState, $availableStates)) {
            $filterStates = [$selectedState];
        } else {
            $filterStates = $availableStates;
        }

        $manageableShopOwners = $this->getFilteredShopOwners($user, $filterStates, $selectedLga)
            ->with(['shop', 'receipts'])
            ->get();
        
        $exportData = [];
        foreach ($manageableShopOwners as $shopOwner) {
            $totalReceipts = $shopOwner->receipts->count();
            $successfulReceipts = $shopOwner->receipts->where('payment_gateway_status', 'successful')->count();
            $totalRevenue = $shopOwner->receipts->where('payment_gateway_status', 'successful')->sum('amount');
            $totalCommission = $shopOwner->receipts->where('payment_gateway_status', 'successful')->sum('commission_amount');
            
            $exportData[] = [
                'Shop Owner' => $shopOwner->name,
                'Email' => $shopOwner->email,
                'Phone' => $shopOwner->phone_number,
                'Shop Name' => $shopOwner->shop ? $shopOwner->shop->shop_name : 'No shop',
                'State' => $shopOwner->shop ? $shopOwner->shop->state : 'N/A',
                'LGA' => $shopOwner->shop ? $shopOwner->shop->local_government : 'N/A',
                'Status' => ucfirst($shopOwner->status),
                'Total Receipts' => $totalReceipts,
                'Successful Receipts' => $successfulReceipts,
                'Total Revenue' => number_format($totalRevenue, 2),
                'Total Commission' => number_format($totalCommission, 2),
                'Success Rate' => $totalReceipts > 0 ? round(($successfulReceipts / $totalReceipts) * 100, 1) . '%' : '0%',
                'Registration Date' => $shopOwner->created_at->format('Y-m-d'),
            ];
        }

        // Generate CSV
        $filename = 'union_performance_report_' . ($selectedState ? strtolower(str_replace(' ', '_', $selectedState)) . '_' : '') . date('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if (!empty($exportData)) {
                fputcsv($file, array_keys($exportData[0]));
            }
            
            // Add data
            foreach ($exportData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}