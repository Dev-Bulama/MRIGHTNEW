<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Receipt;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ShopOwnerController extends Controller
{
    /**
     * Display listing of shop owners managed by this union
     */
 
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied. Union account required.');
        }

        $query = $user->manageableShopOwners()->with(['shop', 'receipts']);

        // Filter by state
        if ($request->filled('state')) {
            $query->whereHas('shop', function($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by LGA
        if ($request->filled('lga')) {
            $query->whereHas('shop', function($q) use ($request) {
                $q->where('local_government', $request->lga);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by activity level
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'inactive':
                    $query->where('last_login_at', '<=', Carbon::now()->subDays(30))
                          ->orWhere('last_login_at', null);
                    break;
                case 'low_performance':
                    $query->whereDoesntHave('receipts', function($q) {
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    });
                    break;
                case 'high_performance':
                    $query->whereHas('receipts', function($q) {
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->havingRaw('COUNT(*) >= 10');
                    });
                    break;
            }
        }

        // Search by name, email, or shop name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('shop', function($shopQuery) use ($search) {
                      $shopQuery->where('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        $shopOwners = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics for each shop owner
        $shopOwners->getCollection()->transform(function ($shopOwner) {
            $shopOwner->monthly_receipts = $shopOwner->receipts()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $shopOwner->total_revenue = $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('amount') ?? 0;
            
            $shopOwner->monthly_revenue = $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount') ?? 0;

            return $shopOwner;
        });

        // Get filter options
        $states = $user->assigned_states;
        $lgas = [];
        if ($request->filled('state')) {
            $lgas = \App\Services\NigeriaData::lgas($request->state);
        }

        // Statistics for the page
        $stats = [
            'total_shop_owners' => $user->manageableShopOwners()->count(),
            'active_shop_owners' => $user->manageableShopOwners()->where('status', User::STATUS_ACTIVE)->count(),
            'new_this_month' => $user->manageableShopOwners()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'high_performers' => $user->manageableShopOwners()
                ->whereHas('receipts', function($q) {
                    $q->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year)
                      ->havingRaw('COUNT(*) >= 10');
                })
                ->count(),
        ];

        return view('union.shop-owners.index', compact('shopOwners', 'states', 'lgas', 'stats'));
    }

    /**
     * Show shop owner details
     */
    public function show(User $shopOwner)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied. Union account required.');
        }

        // Check if this union can manage this shop owner
        if (!$user->manageableShopOwners()->where('id', $shopOwner->id)->exists()) {
            abort(403, 'You are not authorized to view this shop owner.');
        }

        $shopOwner->load(['shop', 'receipts' => function($query) {
            $query->latest()->take(10);
        }, 'payoutRequests' => function($query) {
            $query->latest()->take(5);
        }]);

        // Calculate detailed statistics
        $stats = [
            'total_receipts' => $shopOwner->receipts()->count(),
            'successful_receipts' => $shopOwner->receipts()->where('payment_gateway_status', 'successful')->count(),
            'monthly_receipts' => $shopOwner->receipts()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_revenue' => $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('amount') ?? 0,
            'monthly_revenue' => $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount') ?? 0,
            'total_commission' => $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->sum('commission_amount') ?? 0,
            'available_balance' => $shopOwner->getAvailableCommissionBalance(),
            'payout_requests' => $shopOwner->payoutRequests()->count(),
            'pending_payouts' => $shopOwner->pendingPayoutRequests()->count(),
        ];

        // Performance metrics
        $performanceMetrics = [
            'success_rate' => $stats['total_receipts'] > 0 ? 
                round(($stats['successful_receipts'] / $stats['total_receipts']) * 100, 1) : 0,
            'avg_receipt_amount' => $stats['successful_receipts'] > 0 ? 
                round($stats['total_revenue'] / $stats['successful_receipts'], 2) : 0,
            'days_since_last_receipt' => $shopOwner->receipts()->latest()->first() ? 
                $shopOwner->receipts()->latest()->first()->created_at->diffInDays(now()) : null,
            'registration_days' => $shopOwner->created_at->diffInDays(now()),
        ];

        return view('union.shop-owners.show', compact('shopOwner', 'stats', 'performanceMetrics'));
    }

    /**
     * Get shop owner statistics (AJAX)
     */
//     public function statistics(User $shopOwner)
// {
//     $user = Auth::user();

//     if (!$user->isUnion()) {
//         // If it's an AJAX request, return JSON
//         if (request()->expectsJson()) {
//             return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
//         }
//         abort(403, 'Access denied. Union account required.');
//     }

//     // Check authorization
//     if (!$user->manageableShopOwners()->where('id', $shopOwner->id)->exists()) {
//         if (request()->expectsJson()) {
//             return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
//         }
//         abort(403, 'You are not authorized to view this shop owner.');
//     }

//     try {
//         // Get performance data for charts
//         $performanceData = $this->getShopOwnerPerformanceData($shopOwner);

//         // If it's an AJAX request, return JSON
//         if (request()->expectsJson()) {
//             return response()->json([
//                 'success' => true,
//                 'performance_data' => $performanceData
//             ]);
//         }

//         // Otherwise, return the statistics view
//         return view('union.shop-owners.statistics', compact('shopOwner', 'performanceData'));

//     } catch (\Exception $e) {
//         if (request()->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Failed to load statistics.'
//             ], 500);
//         }
        
//         return back()->with('error', 'Failed to load statistics. Please try again.');
//     }
// }
    // public function statistics(User $shopOwner)
    // {
    //     $user = Auth::user();

    //     if (!$user->isUnion()) {
    //         return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
    //     }

    //     // Check authorization
    //     if (!$user->manageableShopOwners()->where('id', $shopOwner->id)->exists()) {
    //         return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
    //     }

    //     try {
    //         // Get performance data for charts
    //         $performanceData = $this->getShopOwnerPerformanceData($shopOwner);

    //         return response()->json([
    //             'success' => true,
    //             'performance_data' => $performanceData
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to load statistics.'
    //         ], 500);
    //     }
    // }

    /**
     * Get performance data for a specific shop owner
     */
    private function getShopOwnerPerformanceData(User $shopOwner)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Daily receipts for the last 30 days
        $dailyReceipts = $shopOwner->receipts()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly performance for the last 6 months
        $monthlyPerformance = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $receipts = $shopOwner->receipts()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $revenue = $shopOwner->receipts()
                ->where('payment_gateway_status', 'successful')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount') ?? 0;

            $monthlyPerformance[] = [
                'month' => $date->format('M Y'),
                'receipts' => $receipts,
                'revenue' => $revenue,
            ];
        }

        // Success rate trend
        $successRateData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i);
            $weekStart = $date->startOfWeek();
            $weekEnd = $date->endOfWeek();

            $totalReceipts = $shopOwner->receipts()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();

            $successfulReceipts = $shopOwner->receipts()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('payment_gateway_status', 'successful')
                ->count();

            $successRate = $totalReceipts > 0 ? ($successfulReceipts / $totalReceipts) * 100 : 0;

            $successRateData[] = [
                'week' => $weekStart->format('M d'),
                'success_rate' => round($successRate, 1),
            ];
        }

        return [
            'daily_receipts' => $dailyReceipts,
            'monthly_performance' => $monthlyPerformance,
            'success_rate_trend' => $successRateData,
        ];
    }

    /**
     * Export shop owners data
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied.');
        }

        try {
            $shopOwners = $user->manageableShopOwners()->with(['shop', 'receipts'])->get();

            $exportData = [];
            foreach ($shopOwners as $shopOwner) {
                $totalReceipts = $shopOwner->receipts->count();
                $successfulReceipts = $shopOwner->receipts->where('payment_gateway_status', 'successful')->count();
                $totalRevenue = $shopOwner->receipts->where('payment_gateway_status', 'successful')->sum('amount');
                $totalCommission = $shopOwner->receipts->where('payment_gateway_status', 'successful')->sum('commission_amount');

                $exportData[] = [
                    'Name' => $shopOwner->name,
                    'Email' => $shopOwner->email,
                    'Phone' => $shopOwner->phone_number,
                    'Secondary Phone' => $shopOwner->secondary_phone ?? '',
                    'Shop Name' => $shopOwner->shop ? $shopOwner->shop->shop_name : 'No shop',
                    'Shop Address' => $shopOwner->shop ? $shopOwner->shop->business_address : 'N/A',
                    'State' => $shopOwner->shop ? $shopOwner->shop->state : 'N/A',
                    'LGA' => $shopOwner->shop ? $shopOwner->shop->local_government : 'N/A',
                    'Status' => ucfirst($shopOwner->status),
                    'Total Receipts' => $totalReceipts,
                    'Successful Receipts' => $successfulReceipts,
                    'Success Rate' => $totalReceipts > 0 ? round(($successfulReceipts / $totalReceipts) * 100, 1) . '%' : '0%',
                    'Total Revenue' => number_format($totalRevenue, 2),
                    'Total Commission' => number_format($totalCommission, 2),
                    'Available Balance' => number_format($shopOwner->getAvailableCommissionBalance(), 2),
                    'Registration Date' => $shopOwner->created_at->format('Y-m-d'),
                    'Last Login' => $shopOwner->last_login_at ? $shopOwner->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    'Account Activated' => $shopOwner->account_activated_at ? 'Yes' : 'No',
                ];
            }

            $filename = 'union-shop-owners-' . now()->format('Y-m-d');
            return $this->exportToCsv($exportData, $filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed. Please try again.');
        }
    }

    /**
     * Get LGAs for selected state (AJAX)
     */
    public function getLgas(Request $request)
    {
        $state = $request->input('state');
        
        if (!$state) {
            return response()->json([]);
        }

        $lgas = \App\Services\NigeriaData::lgas($state);
        
        return response()->json($lgas);
    }

    /**
     * Get dashboard widget data for union dashboard (AJAX)
     */
    public function getWidgetData()
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        try {
            $manageableShopOwners = $user->manageableShopOwners();
            
            $stats = [
                'total_shop_owners' => $manageableShopOwners->count(),
                'active_shop_owners' => $manageableShopOwners->where('status', User::STATUS_ACTIVE)->count(),
                'new_this_month' => $manageableShopOwners
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'high_performers' => $manageableShopOwners
                    ->whereHas('receipts', function($q) {
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->havingRaw('COUNT(*) >= 10');
                    })
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load widget data.'
            ], 500);
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
    /**
 * Display listing of manageable shop owners
 */
// public function index(Request $request)
// {
//     $user = Auth::user();

//     if (!$user->isUnion()) {
//         abort(403, 'Access denied. Union account required.');
//     }

//     $query = $user->manageableShopOwners()->with(['shop', 'receipts']);

//     // Filter by status
//     if ($request->filled('status')) {
//         $query->where('status', $request->status);
//     }

//     // Filter by state (only states user can manage)
//     if ($request->filled('state')) {
//         $query->whereHas('shop', function($q) use ($request) {
//             $q->where('state', $request->state);
//         });
//     }

//     // Search functionality
//     if ($request->filled('search')) {
//         $search = $request->search;
//         $query->where(function($q) use ($search) {
//             $q->where('name', 'like', "%{$search}%")
//               ->orWhere('email', 'like', "%{$search}%")
//               ->orWhereHas('shop', function($shopQuery) use ($search) {
//                   $shopQuery->where('shop_name', 'like', "%{$search}%");
//               });
//         });
//     }

//     $shopOwners = $query->latest()->paginate(15);

//     return view('union.shop-owners.index', compact('shopOwners'));
// }
/**
 * Display statistics for a specific shop owner
 */
public function statistics(User $user)
{
    $currentUser = Auth::user();

    if (!$currentUser->isUnion()) {
        abort(403, 'Access denied. Union account required.');
    }

    // Verify this shop owner is in the union's assigned areas
    if ($currentUser->assigned_states && $user->shop) {
        if (!in_array($user->shop->state, $currentUser->assigned_states)) {
            abort(403, 'Access denied. This shop owner is not in your assigned areas.');
        }
    }

    // Get statistics for this shop owner
    $receipts = Receipt::where('user_id', $user->id)->with(['shop']);
    
    $stats = [
        'total_receipts' => $receipts->count(),
        'successful_receipts' => (clone $receipts)->where('payment_status', 'paid')->count(),
        'pending_receipts' => (clone $receipts)->where('payment_status', 'pending')->count(),
        'failed_receipts' => (clone $receipts)->where('payment_status', 'failed')->count(),
        'total_revenue' => (clone $receipts)->where('payment_status', 'paid')->sum('amount'),
        'this_month_receipts' => (clone $receipts)->whereMonth('created_at', now()->month)->count(),
        'this_month_revenue' => (clone $receipts)
            ->whereMonth('created_at', now()->month)
            ->where('payment_status', 'paid')
            ->sum('amount'),
    ];

    // Monthly trends (last 6 months)
    $monthlyTrends = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $monthReceipts = (clone $receipts)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();
        
        $monthRevenue = (clone $receipts)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->where('payment_status', 'paid')
            ->sum('amount');
            
        $monthlyTrends[] = [
            'month' => $date->format('M Y'),
            'receipts' => $monthReceipts,
            'revenue' => $monthRevenue,
        ];
    }

    // Recent receipts
    $recentReceipts = (clone $receipts)->latest()->take(10)->get();

    // Payout requests
    $payouts = PayoutRequest::where('user_id', $user->id)->latest()->take(5)->get();
    $totalPayouts = PayoutRequest::where('user_id', $user->id)->sum('amount_approved');

    return view('union.shop-owners.statistics', compact(
        'user', 
        'stats', 
        'monthlyTrends', 
        'recentReceipts', 
        'payouts', 
        'totalPayouts'
    ));
}
}