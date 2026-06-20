<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\User;
use App\Models\PayoutRequest;
use App\Models\PreApprovedUser;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user type.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Route different user types to their appropriate dashboards
        if ($user->isUnion()) {
            return redirect()->route('union.dashboard');
        }
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Handle Shop Owners and Customers with the original dashboard
        $data = [
            // 'recentActivities' => $this->getRecentActivities($user),
        ];

        // Add user-specific data
        // Add user-specific data
if ($user->isShopOwner()) {
    $shop = $user->shop;
    
    if (!$shop) {
        // Check if pre-approved
        $wasPreApproved = \App\Models\PreApprovedUser::where('email', $user->email)->exists();
        
        if ($wasPreApproved) {
            return redirect()->route('shop.index'); // Will auto-create shop
        } else {
            return redirect()->route('shop.create')
                ->with('info', 'Please complete your shop registration to access the dashboard.');
        }
    }
    
    // User has shop - continue with dashboard
    $data = array_merge($data, $this->getShopOwnerData($user));
} elseif ($user->isAdmin()) {
    $data = array_merge($data, $this->getAdminData($user));
} else {
    $data = array_merge($data, $this->getCustomerData($user));
}

return view('dashboard', $data);}
    //     if ($user->isShopOwner()) {
    //         $data = array_merge($data, $this->getShopOwnerData($user));
    //     } elseif ($user->isAdmin()) {
    //         $data = array_merge($data, $this->getAdminData($user));
    //     } else {
    //         $data = array_merge($data, $this->getCustomerData($user));
    //     }

    //     return view('dashboard', $data);
    // }

    /**
     * Get recent activities for the user.
     */
    // private function getRecentActivities($user): array
    // {
    //     // Mock data for now - replace with real activity log
    //     if ($user->isShopOwner()) {
    //         return [
    //             [
    //                 'title' => 'Account Created',
    //                 'description' => 'Welcome to Phone Anti-Theft Digital Receipt System',
    //                 'time' => $user->created_at->diffForHumans(),
    //                 'icon' => 'fa-user-plus',
    //                 'color' => 'success',
    //                 'badge' => ['text' => 'Welcome', 'color' => 'success']
    //             ]
    //         ];
    //     } elseif ($user->isAdmin()) {
    //         return [
    //             [
    //                 'title' => 'Admin Access',
    //                 'description' => 'Logged in to admin dashboard',
    //                 'time' => 'Just now',
    //                 'icon' => 'fa-shield-alt',
    //                 'color' => 'primary'
    //             ]
    //         ];
    //     } else {
    //         return [
    //             [
    //                 'title' => 'Account Created',
    //                 'description' => 'Welcome to Phone Anti-Theft Digital Receipt System',
    //                 'time' => $user->created_at->diffForHumans(),
    //                 'icon' => 'fa-user-plus',
    //                 'color' => 'success'
    //             ]
    //         ];
    //     }
    // }

    /**
     * Get shop owner specific data.
     */
    // private function getShopOwnerData($user): array
    // {
    //     try {
    //         $currentMonth = now()->month;
    //         $currentYear = now()->year;
            
    //         // Payment status breakdown
    //         $paidReceipts = $user->receipts()->where('payment_status', 'paid')->count();
    //         $partPaymentReceipts = $user->receipts()->where('payment_status', 'partial')->count();
    //         $pendingReceipts = $user->receipts()->where('payment_status', 'pending')->count();
            
    //         // Calculate total earnings from paid and partial payments
    //         $totalEarnings = 0;
    //         if (Schema::hasTable('payments')) {
    //             $totalEarnings = $user->receipts()
    //                 ->whereIn('payment_status', ['paid', 'partial'])
    //                 ->sum('amount') ?? 0;
    //         }
            
    //         return [
    //             'shop' => $user->shop,
    //             'monthlyReceipts' => $user->receipts()->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
    //             'yearlyReceipts' => $user->receipts()->whereYear('created_at', $currentYear)->count(),
    //             'totalEarnings' => $totalEarnings,
    //             'paymentStats' => [
    //                 'paid' => $paidReceipts,
    //                 'partial' => $partPaymentReceipts,
    //                 'pending' => $pendingReceipts,
    //                 'total' => $paidReceipts + $partPaymentReceipts + $pendingReceipts,
    //             ],
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'shop' => $user->shop,
    //             'monthlyReceipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
    //             'yearlyReceipts' => $user->receipts()->whereYear('created_at', now()->year)->count(),
    //             'totalEarnings' => 0,
    //             'paymentStats' => [
    //                 'paid' => 0,
    //                 'partial' => 0,
    //                 'pending' => 0,
    //                 'total' => 0,
    //             ],
    //         ];
    //     }
    // }

    // 
    
    /**
 * Get shop owner specific data with CORRECTED calculations.
 */
 //fixed version to be reviewed
//  private function getShopOwnerData($user): array
// {
//     try {
//         $currentMonth = now()->month;
//         $currentYear = now()->year;
        
//         // SIMPLE: Only count SUCCESSFUL receipts and their service fees
//         $successfulReceipts = $user->receipts()->where('payment_gateway_status', 'successful');
        
//         // Count successful receipts
//         $totalSuccessful = (clone $successfulReceipts)->count();
//         $monthlySuccessful = (clone $successfulReceipts)
//             ->whereMonth('created_at', $currentMonth)
//             ->whereYear('created_at', $currentYear)
//             ->count();
//         $yearlySuccessful = (clone $successfulReceipts)
//             ->whereYear('created_at', $currentYear)
//             ->count();
        
//         // SIMPLE: Sum the service_fee from successful receipts
//         $totalEarnings = (clone $successfulReceipts)->sum('service_fee') ?? 0;
//         $monthlyEarnings = (clone $successfulReceipts)
//             ->whereMonth('created_at', $currentMonth)
//             ->whereYear('created_at', $currentYear)
//             ->sum('service_fee') ?? 0;
//         $yearlyEarnings = (clone $successfulReceipts)
//             ->whereYear('created_at', $currentYear)
//             ->sum('service_fee') ?? 0;
        
//         // Status breakdown
//         $allReceipts = $user->receipts();
//         $pendingCount = $allReceipts->where('payment_gateway_status', 'pending')->count();
//         $failedCount = $allReceipts->where('payment_gateway_status', 'failed')->count();
        
//         return [
//             'shop' => $user->shop,
//             'total_receipts' => $totalSuccessful, // Only successful
//             'monthly_receipts' => $monthlySuccessful,
//             'yearly_receipts' => $yearlySuccessful,
//             'total_earnings' => $totalEarnings, // Service fees from successful receipts
//             'monthly_earnings' => $monthlyEarnings,
//             'yearly_earnings' => $yearlyEarnings,
//             'paymentStats' => [
//                 'successful' => $totalSuccessful,
//                 'pending' => $pendingCount,
//                 'failed' => $failedCount,
//                 'total' => $allReceipts->count(),
//                 'success_rate' => $allReceipts->count() > 0 ? 
//                     round(($totalSuccessful / $allReceipts->count()) * 100, 1) : 0,
//             ],
//         ];
//     } catch (\Exception $e) {
//         \Log::error('Dashboard statistics error: ' . $e->getMessage());
        
//         return [
//             'shop' => $user->shop,
//             'total_receipts' => 0,
//             'monthly_receipts' => 0,
//             'yearly_receipts' => 0,
//             'total_earnings' => 0,
//             'monthly_earnings' => 0,
//             'yearly_earnings' => 0,
//             'paymentStats' => [
//                 'successful' => 0,
//                 'pending' => 0,
//                 'failed' => 0,
//                 'total' => 0,
//                 'success_rate' => 0,
//             ],
//         ];
//     }
// }
private function getShopOwnerData($user): array
{
    try {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Only count SUCCESSFUL payments for statistics
        $successfulReceipts = $user->receipts()->where('payment_gateway_status', 'successful');
        $allReceipts = $user->receipts();
        
        // Payment status breakdown - use payment_gateway_status for accuracy
        $paidReceipts = $user->receipts()->where('payment_gateway_status', 'successful')->count();
        $pendingReceipts = $user->receipts()->where('payment_gateway_status', 'pending')->count();
        $failedReceipts = $user->receipts()->where('payment_gateway_status', 'failed')->count();
        
        // Calculate ACTUAL earnings from service fees (not item amounts)
        $totalEarnings = (clone $successfulReceipts)->sum('service_fee') ?? 0;
        $monthlyEarnings = (clone $successfulReceipts)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('service_fee') ?? 0;
        $yearlyEarnings = (clone $successfulReceipts)
            ->whereYear('created_at', $currentYear)
            ->sum('service_fee') ?? 0;
        
        // Count successful receipts only for meaningful metrics
        $monthlySuccessfulReceipts = (clone $successfulReceipts)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        $yearlySuccessfulReceipts = (clone $successfulReceipts)
            ->whereYear('created_at', $currentYear)
            ->count();
        
        return [
            'shop' => $user->shop,
            'monthlyReceipts' => $monthlySuccessfulReceipts, // Only successful
            'yearlyReceipts' => $yearlySuccessfulReceipts, // Only successful
            'totalEarnings' => $totalEarnings, // From service fees
            'monthlyEarnings' => $monthlyEarnings, // From service fees
            'yearlyEarnings' => $yearlyEarnings, // From service fees
            'paymentStats' => [
                'successful' => $paidReceipts,
                'pending' => $pendingReceipts,
                'failed' => $failedReceipts,
                'total' => $allReceipts->count(),
                'success_rate' => $allReceipts->count() > 0 ? round(($paidReceipts / $allReceipts->count()) * 100, 1) : 0,
            ],
        ];
    } catch (\Exception $e) {
        \Log::error('Dashboard statistics error: ' . $e->getMessage());
        
        return [
            'shop' => $user->shop,
            'monthlyReceipts' => 0,
            'yearlyReceipts' => 0,
            'totalEarnings' => 0,
            'monthlyEarnings' => 0,
            'yearlyEarnings' => 0,
            'paymentStats' => [
                'successful' => 0,
                'pending' => 0,
                'failed' => 0,
                'total' => 0,
                'success_rate' => 0,
            ],
        ];
    }
}
    /**
     * Get admin specific data.
     */
    private function getAdminData($user): array
    {
        return [
            'totalShops' => \App\Models\Shop::count(),
            'pendingShops' => \App\Models\Shop::where('status', 'pending_approval')->count(),
            'activeShops' => \App\Models\Shop::where('approved', true)->count(),
            'totalUsers' => \App\Models\User::count(),
            'totalReceipts' => \App\Models\Receipt::count(),
            'monthlyReceipts' => \App\Models\Receipt::whereMonth('created_at', now()->month)->count(),
            'totalRevenue' => \App\Models\Payment::where('status', 'successful')->sum('amount'),
        ];
    }

    /**
     * Get customer specific data.
     */
    private function getCustomerData($user): array
    {
        $userReceipts = \App\Models\Receipt::where('customer_email', $user->email)
                                          ->orWhere('customer_phone', $user->phone_number)
                                          ->latest()
                                          ->take(5)
                                          ->get();

        return [
            'userReceipts' => $userReceipts,
            'totalReceipts' => \App\Models\Receipt::where('customer_email', $user->email)
                                                 ->orWhere('customer_phone', $user->phone_number)
                                                 ->count(),
        ];
    }

    /**
     * Performance dashboard.
     */
    public function performance()
    {
        $user = auth()->user();
        
        if (!$user->isShopOwner() || !$user->shop || !$user->shop->approved) {
            return redirect()->route('dashboard')
                ->with('warning', 'Shop must be approved to view performance metrics.');
        }

        $shop = $user->shop;
        $data = [
            'shop' => $shop,
            'monthlyStats' => [
                'receipts' => $user->currentMonthReceipts()->count(),
                'revenue' => $user->currentMonthReceipts()->sum('amount'),
                'customers' => $user->currentMonthReceipts()->distinct('customer_phone')->count(),
            ],
            'yearlyStats' => [
                'receipts' => $user->currentYearReceipts()->count(),
                'revenue' => $user->currentYearReceipts()->sum('amount'),
                'customers' => $user->currentYearReceipts()->distinct('customer_phone')->count(),
            ],
            'recentReceipts' => $user->receipts()->latest()->take(10)->get(),
        ];

        return view('performance.index', $data);
    }

    /**
     * Receipt analytics.
     */
    public function receiptAnalytics()
    {
        $user = auth()->user();
        
        if (!$user->isShopOwner() || !$user->shop || !$user->shop->approved) {
            return redirect()->route('dashboard')
                ->with('warning', 'Shop must be approved to view analytics.');
        }

        return redirect()->route('performance.index')
            ->with('info', 'Receipt analytics integrated in performance dashboard.');
    }

    /**
     * Revenue analytics.
     */
    public function revenueAnalytics()
    {
        $user = auth()->user();
        
        if (!$user->isShopOwner() || !$user->shop || !$user->shop->approved) {
            return redirect()->route('dashboard')
                ->with('warning', 'Shop must be approved to view analytics.');
        }

        return redirect()->route('performance.index')
            ->with('info', 'Revenue analytics integrated in performance dashboard.');
    }

    /**
     * Export performance data.
     */
    public function exportData()
    {
        $user = auth()->user();
        
        if (!$user->isShopOwner() || !$user->shop || !$user->shop->approved) {
            return redirect()->route('dashboard')
                ->with('warning', 'Shop must be approved to export data.');
        }

        $data = [
            'export_date' => now()->format('Y-m-d H:i:s'),
            'shop' => $user->shop->toArray(),
            'monthly_receipts' => $user->currentMonthReceipts()->count(),
            'yearly_receipts' => $user->currentYearReceipts()->count(),
            'total_earnings' => $user->totalEarnings,
        ];

        $filename = 'shop_performance_' . now()->format('Y_m_d_H_i_s') . '.json';
        
        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Admin export functionality.
     */
    public function export()
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin access required.');
        }

        return redirect()->route('admin.dashboard')
            ->with('info', 'Admin export functionality coming in Phase 5.');
    }
/**
 * Get dashboard statistics (AJAX) - CORRECTED VERSION
 */
public function getStats()
{
    $user = auth()->user();
    
    if ($user->isShopOwner()) {
        $successfulReceipts = $user->receipts()->where('payment_gateway_status', 'successful');
        $currentMonth = now()->month;
        
        $monthlySuccessful = (clone $successfulReceipts)->whereMonth('created_at', $currentMonth)->count();
        $yearlySuccessful = (clone $successfulReceipts)->whereYear('created_at', now()->year)->count();
        $totalSuccessful = (clone $successfulReceipts)->count();
        
        return response()->json([
            'monthly_receipts' => $monthlySuccessful,
            'yearly_receipts' => $yearlySuccessful,
            'total_receipts' => $totalSuccessful,
            'total_earnings' => (clone $successfulReceipts)->sum('service_fee') ?? 0,
            'monthly_earnings' => (clone $successfulReceipts)->whereMonth('created_at', $currentMonth)->sum('service_fee') ?? 0,
            'pending_payments' => $user->receipts()->where('payment_gateway_status', 'pending')->count(),
            'failed_payments' => $user->receipts()->where('payment_gateway_status', 'failed')->count(),
        ]);
    }
    
    return response()->json(['error' => 'Unauthorized'], 403);
}

   /**
     * Get dashboard statistics (AJAX)
     */
    // public function getStats()
    // {
    //     $user = auth()->user();
        
    //     if ($user->isShopOwner()) {
    //         return response()->json([
    //             'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
    //             'yearly_receipts' => $user->receipts()->whereYear('created_at', now()->year)->count(),
    //             'total_earnings' => $user->receipts()->whereIn('payment_status', ['paid', 'partial'])->sum('amount') ?? 0,
    //             'pending_payments' => $user->receipts()->where('payment_status', 'pending')->count(),
    //         ]);
    //     }
        
    //     return response()->json(['error' => 'Unauthorized'], 403);
    // }
}