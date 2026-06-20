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
    //                 'description' => 'Welcome to M-right Digital Receipt System',
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
    //                 'description' => 'Welcome to M-right Digital Receipt System',
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
    private function getShopOwnerData($user): array
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            
            // Payment status breakdown
            $paidReceipts = $user->receipts()->where('payment_status', 'paid')->count();
            $partPaymentReceipts = $user->receipts()->where('payment_status', 'partial')->count();
            $pendingReceipts = $user->receipts()->where('payment_status', 'pending')->count();
            
            // Calculate total earnings from paid and partial payments
            $totalEarnings = 0;
            if (Schema::hasTable('payments')) {
                $totalEarnings = $user->receipts()
                    ->whereIn('payment_status', ['paid', 'partial'])
                    ->sum('amount') ?? 0;
            }
            
            return [
                'shop' => $user->shop,
                'monthlyReceipts' => $user->receipts()->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count(),
                'yearlyReceipts' => $user->receipts()->whereYear('created_at', $currentYear)->count(),
                'totalEarnings' => $totalEarnings,
                'paymentStats' => [
                    'paid' => $paidReceipts,
                    'partial' => $partPaymentReceipts,
                    'pending' => $pendingReceipts,
                    'total' => $paidReceipts + $partPaymentReceipts + $pendingReceipts,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'shop' => $user->shop,
                'monthlyReceipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
                'yearlyReceipts' => $user->receipts()->whereYear('created_at', now()->year)->count(),
                'totalEarnings' => 0,
                'paymentStats' => [
                    'paid' => 0,
                    'partial' => 0,
                    'pending' => 0,
                    'total' => 0,
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
     * Get dashboard statistics (AJAX)
     */
    public function getStats()
    {
        $user = auth()->user();
        
        if ($user->isShopOwner()) {
            return response()->json([
                'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
                'yearly_receipts' => $user->receipts()->whereYear('created_at', now()->year)->count(),
                'total_earnings' => $user->receipts()->whereIn('payment_status', ['paid', 'partial'])->sum('amount') ?? 0,
                'pending_payments' => $user->receipts()->where('payment_status', 'pending')->count(),
            ]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}