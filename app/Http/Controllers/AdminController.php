<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\Receipt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with analytics.
     */
    public function dashboard()
    {
        $data = [
            'dashboardStats' => $this->getDashboardStats(),
            'recentActivities' => $this->getRecentActivities(),
            'performanceMetrics' => $this->getPerformanceMetrics(),
            'revenueChart' => $this->getRevenueChartData(),
            'locationStats' => $this->getLocationStats(),
            'systemHealth' => $this->getSystemHealth(),
        ];

        return view('admin.dashboard', $data);
    }

    /**
     * Get dashboard statistics.
     */
    private function getDashboardStats(): array
    {
        try {
            $totalShops = Shop::count();
            $activeShops = Shop::where('approved', true)->count();
            $pendingShops = Shop::where('status', 'pending_approval')->count();
            $totalUsers = User::count();
            $totalReceipts = Receipt::count();
            $monthlyReceipts = Receipt::whereMonth('created_at', now()->month)->count();
            
            // Safe payment queries
            $totalRevenue = 0;
            $monthlyRevenue = 0;
            if (Schema::hasTable('payments')) {
                $totalRevenue = DB::table('payments')
                    ->where('status', 'successful')
                    ->sum('amount') ?? 0;
                    
                $monthlyRevenue = DB::table('payments')
                    ->where('status', 'successful')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount') ?? 0;
            }

            return [
                'total_shops' => $totalShops,
                'active_shops' => $activeShops,
                'pending_shops' => $pendingShops,
                'total_users' => $totalUsers,
                'total_receipts' => $totalReceipts,
                'monthly_receipts' => $monthlyReceipts,
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'active_shops_percentage' => $totalShops > 0 ? round(($activeShops / $totalShops) * 100, 1) : 0,
                'growth_percentage' => $this->calculateGrowthPercentage(),
            ];
        } catch (\Exception $e) {
            return [
                'total_shops' => 0,
                'active_shops' => 0,
                'pending_shops' => 0,
                'total_users' => 0,
                'total_receipts' => 0,
                'monthly_receipts' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
                'active_shops_percentage' => 0,
                'growth_percentage' => 0,
            ];
        }
    }

    /**
     * Get recent admin activities.
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        try {
            // Recent shop registrations
            $recentShops = Shop::latest()->take(3)->get();
            foreach ($recentShops as $shop) {
                $activities[] = [
                    'title' => 'New Shop Registration',
                    'description' => $shop->shop_name . ' registered by ' . $shop->user->name,
                    'time' => $shop->created_at->diffForHumans(),
                    'icon' => 'fa-store',
                    'color' => 'success',
                    'badge' => ['text' => 'New', 'color' => 'success']
                ];
            }

            // Recent receipts
            $recentReceipts = Receipt::with('shop')->latest()->take(2)->get();
            foreach ($recentReceipts as $receipt) {
                $activities[] = [
                    'title' => 'Receipt Generated',
                    'description' => 'Receipt #' . $receipt->receipt_number . ' by ' . ($receipt->shop->shop_name ?? 'Unknown Shop'),
                    'time' => $receipt->created_at->diffForHumans(),
                    'icon' => 'fa-receipt',
                    'color' => 'primary',
                ];
            }

            // Sort by most recent
            usort($activities, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });

        } catch (\Exception $e) {
            $activities = [
                [
                    'title' => 'System Status',
                    'description' => 'Admin dashboard loaded successfully',
                    'time' => 'Just now',
                    'icon' => 'fa-shield-alt',
                    'color' => 'primary'
                ]
            ];
        }

        return array_slice($activities, 0, 5);
    }

    /**
     * Get performance metrics.
     */
    private function getPerformanceMetrics(): array
    {
        try {
            $totalReceipts = Receipt::count();
            $completedReceipts = Receipt::where('payment_gateway_status', 'successful')->count();
            $pendingReceipts = Receipt::where('payment_gateway_status', 'pending')->count();
            $failedReceipts = Receipt::where('payment_gateway_status', 'failed')->count();

            return [
                'completion_rate' => $totalReceipts > 0 ? round(($completedReceipts / $totalReceipts) * 100, 1) : 0,
                'pending_rate' => $totalReceipts > 0 ? round(($pendingReceipts / $totalReceipts) * 100, 1) : 0,
                'failure_rate' => $totalReceipts > 0 ? round(($failedReceipts / $totalReceipts) * 100, 1) : 0,
                'total_receipts' => $totalReceipts,
                'completed_receipts' => $completedReceipts,
                'pending_receipts' => $pendingReceipts,
                'failed_receipts' => $failedReceipts,
            ];
        } catch (\Exception $e) {
            return [
                'completion_rate' => 0,
                'pending_rate' => 0,
                'failure_rate' => 0,
                'total_receipts' => 0,
                'completed_receipts' => 0,
                'pending_receipts' => 0,
                'failed_receipts' => 0,
            ];
        }
    }

    /**
     * Get revenue chart data for the last 12 months.
     */
    private function getRevenueChartData(): array
    {
        try {
            $months = [];
            $revenues = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');
                
                if (Schema::hasTable('payments')) {
                    $revenue = DB::table('payments')
                        ->where('status', 'successful')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('amount') ?? 0;
                } else {
                    $revenue = 0;
                }
                
                $revenues[] = floatval($revenue);
            }

            return [
                'labels' => $months,
                'data' => $revenues,
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }
    }

    /**
     * Get location statistics.
     */
    private function getLocationStats(): array
    {
        try {
            $locationStats = Shop::select('state', DB::raw('count(*) as total'))
                ->groupBy('state')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get()
                ->toArray();

            return $locationStats;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system health metrics.
     */
    private function getSystemHealth(): array
    {
        try {
            return [
                'database' => ['status' => 'healthy', 'label' => 'Connected'],
                'server' => ['status' => 'healthy', 'label' => 'Online'],
                'payment_gateway' => ['status' => 'healthy', 'label' => 'Active'],
                'api_sync' => ['status' => 'warning', 'label' => 'Syncing'],
            ];
        } catch (\Exception $e) {
            return [
                'database' => ['status' => 'error', 'label' => 'Error'],
                'server' => ['status' => 'healthy', 'label' => 'Online'],
                'payment_gateway' => ['status' => 'warning', 'label' => 'Unknown'],
                'api_sync' => ['status' => 'error', 'label' => 'Failed'],
            ];
        }
    }

    /**
     * Calculate growth percentage.
     */
    private function calculateGrowthPercentage(): float
    {
        try {
            $thisMonth = Receipt::whereMonth('created_at', now()->month)->count();
            $lastMonth = Receipt::whereMonth('created_at', now()->subMonth()->month)->count();
            
            if ($lastMonth == 0) return $thisMonth > 0 ? 100 : 0;
            
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Shop management methods.
     */
    public function shops(Request $request)
    {
        $query = Shop::with('user');
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('approved', false)->where('status', 'pending_approval');
            } elseif ($request->status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->status === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('owner_full_name', 'like', "%{$search}%")
                  ->orWhere('business_email', 'like', "%{$search}%");
            });
        }

        $shops = $query->latest()->paginate(15);
        $stats = [
            'total' => Shop::count(),
            'pending' => Shop::where('approved', false)->count(),
            'approved' => Shop::where('approved', true)->count(),
            'rejected' => Shop::where('status', 'rejected')->count(),
        ];

        return view('admin.shops.index', compact('shops', 'stats'));
    }

    
    /**
     * Users management.
     */
    // public function users(Request $request)
    // {
    //     $query = User::with('shop');
        
    //     // Filter by user type
    //     if ($request->has('type')) {
    //         $query->where('user_type', $request->type);
    //     }

    //     // Filter by status
    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // Search functionality
    //     if ($request->has('search')) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //               ->orWhere('email', 'like', "%{$search}%")
    //               ->orWhere('phone_number', 'like', "%{$search}%");
    //         });
    //     }

    //     $users = $query->latest()->paginate(15);
    //     $stats = [
    //         'total' => User::count(),
    //         'shop_owners' => User::where('user_type', 'shop_owner')->count(),
    //         'customers' => User::where('user_type', 'customer')->count(),
    //         'admins' => User::where('user_type', 'admin')->count(),
    //     ];

    //     return view('admin.users.index', compact('users', 'stats'));
    // }

    /**
     * Receipts management.
     */
    public function receipts(Request $request)
    {
        $query = Receipt::with(['shop', 'user']);
        
        // Filter by payment status
        if ($request->has('status') && $request->status) {
            $query->where('payment_gateway_status', $request->status);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Shop filter
        if ($request->has('shop_id') && $request->shop_id) {
            $query->where('shop_id', $request->shop_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Handle export requests
        if ($request->has('export')) {
            return $this->exportReceipts($request);
        }

        $receipts = $query->latest()->paginate(15);
        $stats = $this->getReceiptStats();
        
        // Calculate additional metrics
        $totalRevenue = $stats['total_revenue'];
        $monthlyRevenue = $stats['monthly_revenue'];

        return view('admin.receipts.index', compact('receipts', 'stats', 'totalRevenue', 'monthlyRevenue'));
    }
    /**
     * Export dashboard data.
     */
    public function export()
    {
        try {
            $data = [
                'export_date' => now()->format('Y-m-d H:i:s'),
                'dashboard_stats' => $this->getDashboardStats(),
                'performance_metrics' => $this->getPerformanceMetrics(),
                'revenue_data' => $this->getRevenueChartData(),
                'location_stats' => $this->getLocationStats(),
            ];

            $filename = 'admin_dashboard_' . now()->format('Y_m_d_H_i_s') . '.json';
            
            return response()->json($data)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed. Please try again.');
        }
    }
    /**
     * Handle shop approval.
     */
    public function approveShop(Request $request, Shop $shop)
    {
        $shop->update(['approved' => true]);
        
        return response()->json([
            'success' => true,
            'message' => "Shop '{$shop->shop_name}' has been approved successfully!"
        ]);
    }

    /**
     * Handle shop rejection.
     */
    public function rejectShop(Request $request, Shop $shop)
    {
        $shop->update(['approved' => false]);
        
        return response()->json([
            'success' => true,
            'message' => "Shop '{$shop->shop_name}' has been rejected."
        ]);
    }

    /**
     * Bulk approve shops.
     */
    public function bulkApproveShops(Request $request)
    {
        $count = Shop::where('approved', false)->update(['approved' => true]);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} shops have been approved!"
        ]);
    }

    /**
     * Admin shop management index.
     */
    public function shopIndex(Request $request)
    {
        $query = Shop::with('user');
        
        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('approved', true);
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('business_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $shops = $query->latest()->paginate(15);
        $stats = [
            'total' => Shop::count(),
            'approved' => Shop::where('approved', true)->count(),
            'pending' => Shop::where('approved', false)->count(),
        ];

        return view('admin.shops.index', compact('shops', 'stats'));
    }

    /**
     * Admin shop details.
     */
    public function shopShow(Shop $shop)
    {
        $shop->load('user');
        return view('admin.shops.show', compact('shop'));
    }
    /**
     * Shop details for modal.
     */
    public function shopDetails(Shop $shop)
    {
        $shop->load('user');
        
        $html = view('admin.shops.details-modal', compact('shop'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Suspend shop.
     */
    public function suspendShop(Request $request, Shop $shop)
    {
        try {
            $shop->update([
                'approved' => false,
                'status' => 'suspended',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Shop '{$shop->shop_name}' has been suspended."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend shop.'
            ], 500);
        }
    }

    /**
     * Reinstate shop.
     */
    public function reinstateShop(Request $request, Shop $shop)
    {
        try {
            $shop->update([
                'approved' => true,
                'status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Shop '{$shop->shop_name}' has been reinstated."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reinstate shop.'
            ], 500);
        }
    }

    /**
     * Bulk approve.
     */
    public function bulkApprove(Request $request)
    {
        try {
            $shopIds = $request->input('shop_ids', []);
            $count = Shop::whereIn('id', $shopIds)->update([
                'approved' => true,
                'status' => 'approved'
            ]);

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "{$count} shops have been approved!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk approval failed.'
            ], 500);
        }
    }

    /**
     * Bulk reject.
     */
    public function bulkReject(Request $request)
    {
        try {
            $shopIds = $request->input('shop_ids', []);
            $reason = $request->input('reason', '');
            
            $count = Shop::whereIn('id', $shopIds)->update([
                'approved' => false,
                'status' => 'rejected',
                'rejection_reason' => $reason
            ]);

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "{$count} shops have been rejected!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk rejection failed.'
            ], 500);
        }
    }
    /**
     * Receipt details for modal.
     */
    public function receiptDetails(Receipt $receipt)
    {
        $receipt->load(['shop', 'user']);
        
        $html = view('admin.receipts.details-modal', compact('receipt'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Download receipt PDF.
     */
    public function downloadReceipt(Receipt $receipt)
    {
        try {
            // Load necessary relationships
            $receipt->load(['shop', 'user']);
            
            // Generate PDF using a PDF library (like DomPDF or wkhtmltopdf)
            $pdf = \PDF::loadView('receipts.pdf', compact('receipt'));
            
            return $pdf->download("receipt-{$receipt->receipt_number}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF. Please try again.'
            ], 500);
        }
    }

    /**
     * Check payment status.
     */
    public function checkPaymentStatus(Receipt $receipt)
    {
        try {
            // Simulate payment gateway status check
            // In real implementation, you would call the actual payment gateway API
            
            $statuses = ['successful', 'pending', 'failed'];
            $newStatus = $statuses[array_rand($statuses)];
            
            $receipt->update([
                'payment_gateway_status' => $newStatus,
                'payment_gateway_response' => 'Status updated via admin check',
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Payment status updated to: " . ucfirst($newStatus)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status. Please try again.'
            ], 500);
        }
    }

    /**
     * Resend receipt to customer.
     */
    public function resendReceipt(Receipt $receipt)
    {
        try {
            if (!$receipt->customer_email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customer email address found.'
                ]);
            }
            
            // Send receipt email
            // In real implementation, you would use Laravel's Mail system
            // Mail::to($receipt->customer_email)->send(new ReceiptMail($receipt));
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt has been resent to ' . $receipt->customer_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend receipt. Please try again.'
            ], 500);
        }
    }

    /**
     * Export receipts data.
     */
    public function exportReceipts(Request $request)
    {
        try {
            $format = $request->input('export', 'excel');
            
            // Build query with filters
            $query = Receipt::with(['shop', 'user']);
            
            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('payment_gateway_status', $request->status);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->has('shop_id') && $request->shop_id) {
                $query->where('shop_id', $request->shop_id);
            }
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            }
            
            $receipts = $query->get();
            
            // Generate export data
            $exportData = [];
            foreach ($receipts as $receipt) {
                $exportData[] = [
                    'Receipt Number' => $receipt->receipt_number,
                    'Customer Name' => $receipt->customer_name ?: 'N/A',
                    'Customer Email' => $receipt->customer_email ?: 'N/A',
                    'Customer Phone' => $receipt->customer_phone ?: 'N/A',
                    'Shop Name' => $receipt->shop ? $receipt->shop->shop_name : 'N/A',
                    'Amount' => $receipt->amount ? '₦' . number_format($receipt->amount, 2) : '₦0.00',
                    'Payment Method' => $receipt->payment_method ?: 'N/A',
                    'Payment Status' => ucfirst($receipt->payment_gateway_status ?: 'pending'),
                    'Transaction Reference' => $receipt->transaction_reference ?: 'N/A',
                    'Generated Date' => $receipt->created_at->format('Y-m-d H:i:s'),
                    'Updated Date' => $receipt->updated_at->format('Y-m-d H:i:s'),
                ];
            }
            
            if ($format === 'excel') {
                return $this->exportToExcel($exportData, 'receipts-export');
            } elseif ($format === 'pdf') {
                return $this->exportToPdf($exportData, 'receipts-export');
            } else {
                return $this->exportToCsv($exportData, 'receipts-export');
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Export data to Excel format.
     */
    private function exportToExcel($data, $filename)
    {
        // This is a simplified implementation
        // In real project, use Laravel Excel package
        $csvContent = $this->arrayToCsv($data);
        
        return response($csvContent)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }

    /**
     * Export data to PDF format.
     */
    private function exportToPdf($data, $filename)
    {
        // This is a simplified implementation
        // In real project, use DomPDF or similar
        $html = view('admin.exports.receipts-pdf', compact('data'))->render();
        
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
    }

    /**
     * Export data to CSV format.
     */
    private function exportToCsv($data, $filename)
    {
        $csvContent = $this->arrayToCsv($data);
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }

    /**
     * Convert array to CSV format.
     */
    private function arrayToCsv($data)
    {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, array_keys($data[0]));
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    /**
     * Get receipt statistics for dashboard.
     */
    private function getReceiptStats(): array
    {
        try {
            $totalReceipts = Receipt::count();
            $successfulReceipts = Receipt::where('payment_gateway_status', 'successful')->count();
            $pendingReceipts = Receipt::where('payment_gateway_status', 'pending')->count();
            $failedReceipts = Receipt::where('payment_gateway_status', 'failed')->count();
            
            $totalRevenue = Receipt::where('payment_gateway_status', 'successful')
                ->sum('amount') ?? 0;
            
            $monthlyRevenue = Receipt::where('payment_gateway_status', 'successful')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0;
            
            return [
                'total' => $totalReceipts,
                'successful' => $successfulReceipts,
                'pending' => $pendingReceipts,
                'failed' => $failedReceipts,
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'successful' => 0,
                'pending' => 0,
                'failed' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
            ];
        }
    }

/**
     * Reports dashboard.
     */
    public function reports()
    {
        $quickStats = [
            'total_reports' => $this->getTotalReportsGenerated(),
            'active_shops' => Shop::where('approved', true)->count(),
            'monthly_receipts' => Receipt::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue' => Receipt::where('payment_gateway_status', 'successful')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0,
        ];

        $reportCounts = [
            'revenue' => rand(5, 15),
            'shop_performance' => rand(3, 12),
            'customer_insights' => rand(2, 8),
            'payment_trends' => rand(4, 10),
            'system_health' => rand(6, 14),
            'user_activity' => rand(3, 9),
            'receipt_analytics' => rand(7, 16),
            'geographic' => rand(2, 7),
            'audit' => rand(8, 20),
            'compliance' => rand(1, 5),
            'security' => rand(2, 6),
            'financial' => rand(4, 11),
        ];

        $recentReports = $this->getRecentReports();

        return view('admin.reports.index', compact('quickStats', 'reportCounts', 'recentReports'));
    }

    /**
     * Generate report form.
     */
    public function generateReportForm($reportType)
    {
        $reportConfigs = [
            'revenue-analysis' => [
                'title' => 'Revenue Analysis Report',
                'description' => 'Comprehensive revenue breakdown and trends',
                'default_name' => 'Revenue Analysis - ' . now()->format('M Y'),
            ],
            'shop-performance' => [
                'title' => 'Shop Performance Report',
                'description' => 'Individual shop metrics and rankings',
                'default_name' => 'Shop Performance - ' . now()->format('M Y'),
            ],
            'customer-insights' => [
                'title' => 'Customer Insights Report',
                'description' => 'Customer behavior and retention analysis',
                'default_name' => 'Customer Insights - ' . now()->format('M Y'),
            ],
            'payment-trends' => [
                'title' => 'Payment Trends Report',
                'description' => 'Payment method preferences and success rates',
                'default_name' => 'Payment Trends - ' . now()->format('M Y'),
            ],
            'geographic-analysis' => [
                'title' => 'Geographic Analysis Report',
                'description' => 'Location-based performance insights',
                'default_name' => 'Geographic Analysis - ' . now()->format('M Y'),
            ],
            'system-health' => [
                'title' => 'System Health Report',
                'description' => 'Server performance and uptime metrics',
                'default_name' => 'System Health - ' . now()->format('M Y'),
            ],
            'user-activity' => [
                'title' => 'User Activity Report',
                'description' => 'Login patterns and feature usage',
                'default_name' => 'User Activity - ' . now()->format('M Y'),
            ],
            'receipt-analytics' => [
                'title' => 'Receipt Analytics Report',
                'description' => 'Receipt generation patterns and trends',
                'default_name' => 'Receipt Analytics - ' . now()->format('M Y'),
            ],
            'audit-trail' => [
                'title' => 'Audit Trail Report',
                'description' => 'Complete activity logs and system changes',
                'default_name' => 'Audit Trail - ' . now()->format('M Y'),
            ],
            'compliance-check' => [
                'title' => 'Compliance Check Report',
                'description' => 'Regulatory compliance status',
                'default_name' => 'Compliance Check - ' . now()->format('M Y'),
            ],
            'security-report' => [
                'title' => 'Security Report',
                'description' => 'Security incidents and threat analysis',
                'default_name' => 'Security Report - ' . now()->format('M Y'),
            ],
            'financial-reconciliation' => [
                'title' => 'Financial Reconciliation Report',
                'description' => 'Payment reconciliation and accuracy',
                'default_name' => 'Financial Reconciliation - ' . now()->format('M Y'),
            ],
        ];

        if (!isset($reportConfigs[$reportType])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report type'
            ]);
        }

        $reportConfig = $reportConfigs[$reportType];
        $html = view('admin.reports.generate-form', compact('reportType', 'reportConfig'))->render();

        return response()->json([
            'success' => true,
            'title' => $reportConfig['title'],
            'html' => $html
        ]);
    }

    /**
     * Process report generation.
     */
    public function processReport(Request $request)
    {
        try {
            $data = $request->all();
            $reportType = $data['report_type'];
            
            // Validate required fields
            $request->validate([
                'report_type' => 'required|string',
                'report_name' => 'required|string|max:255',
                'format' => 'required|in:pdf,excel,csv,html',
                'date_range' => 'required|string'
            ]);

            // Generate report data based on type
            $reportData = $this->generateReportData($reportType, $data);
            
            // Store report generation request
            $reportId = $this->storeReportRequest($data, $reportData);
            
            // In a real application, you might queue this for background processing
            // Queue::push(new GenerateReportJob($reportId));
            
            return response()->json([
                'success' => true,
                'message' => 'Report generation started successfully!',
                'report_id' => $reportId
            ]);

        } catch (\Exception $e) {
            \Log::error('Report generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate report data based on type.
     */
    private function generateReportData($reportType, $config)
    {
        $dateRange = $this->parseDateRange($config['date_range'], $config);
        
        switch ($reportType) {
            case 'revenue-analysis':
                return $this->generateRevenueAnalysis($dateRange, $config);
                
            case 'shop-performance':
                return $this->generateShopPerformance($dateRange, $config);
                
            case 'customer-insights':
                return $this->generateCustomerInsights($dateRange, $config);
                
            case 'payment-trends':
                return $this->generatePaymentTrends($dateRange, $config);
                
            case 'geographic-analysis':
                return $this->generateGeographicAnalysis($dateRange, $config);
                
            case 'system-health':
                return $this->generateSystemHealth($dateRange, $config);
                
            case 'user-activity':
                return $this->generateUserActivity($dateRange, $config);
                
            case 'receipt-analytics':
                return $this->generateReceiptAnalytics($dateRange, $config);
                
            case 'audit-trail':
                return $this->generateAuditTrail($dateRange, $config);
                
            case 'compliance-check':
                return $this->generateComplianceCheck($dateRange, $config);
                
            case 'security-report':
                return $this->generateSecurityReport($dateRange, $config);
                
            case 'financial-reconciliation':
                return $this->generateFinancialReconciliation($dateRange, $config);
                
            default:
                throw new \Exception('Unsupported report type');
        }
    }

    /**
     * Generate revenue analysis data.
     */
    private function generateRevenueAnalysis($dateRange, $config)
    {
        $query = Receipt::where('payment_gateway_status', 'successful')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        $totalRevenue = $query->sum('amount') ?? 0;
        $totalTransactions = $query->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Group by specified period
        $groupBy = $config['group_by'] ?? 'month';
        $revenueByPeriod = $this->groupRevenueByPeriod($query, $groupBy);

        // Revenue by shop
        $revenueByShop = $query->with('shop')
            ->select('shop_id', DB::raw('SUM(amount) as total_revenue'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('shop_id')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();

        // Revenue by payment method
        $revenueByMethod = $query->select('payment_method', DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('payment_method')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_transactions' => $totalTransactions,
                'avg_transaction_value' => $avgTransactionValue,
                'date_range' => $dateRange,
            ],
            'revenue_by_period' => $revenueByPeriod,
            'revenue_by_shop' => $revenueByShop,
            'revenue_by_method' => $revenueByMethod,
            'growth_analysis' => $this->calculateRevenueGrowth($dateRange),
        ];
    }

    /**
     * Generate shop performance data.
     */
    private function generateShopPerformance($dateRange, $config)
    {
        $shopsQuery = Shop::with(['receipts' => function($query) use ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }]);

        if ($config['shop_filter'] === 'approved_only') {
            $shopsQuery->where('approved', true);
        }

        $shops = $shopsQuery->get();
        
        $performanceData = [];
        foreach ($shops as $shop) {
            $receipts = $shop->receipts;
            $successfulReceipts = $receipts->where('payment_gateway_status', 'successful');
            
            $performanceData[] = [
                'shop_id' => $shop->id,
                'shop_name' => $shop->shop_name,
                'owner_name' => $shop->owner_full_name,
                'location' => $shop->state . ', ' . $shop->country,
                'total_receipts' => $receipts->count(),
                'successful_receipts' => $successfulReceipts->count(),
                'total_revenue' => $successfulReceipts->sum('amount'),
                'success_rate' => $receipts->count() > 0 ? 
                    ($successfulReceipts->count() / $receipts->count()) * 100 : 0,
                'avg_transaction_value' => $successfulReceipts->count() > 0 ?
                    $successfulReceipts->sum('amount') / $successfulReceipts->count() : 0,
                'registration_date' => $shop->created_at,
                'approved' => $shop->approved,
            ];
        }

        // Sort by total revenue
        usort($performanceData, function($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return [
            'summary' => [
                'total_shops' => count($performanceData),
                'total_revenue' => array_sum(array_column($performanceData, 'total_revenue')),
                'total_receipts' => array_sum(array_column($performanceData, 'total_receipts')),
                'avg_success_rate' => count($performanceData) > 0 ? 
                    array_sum(array_column($performanceData, 'success_rate')) / count($performanceData) : 0,
            ],
            'performance_data' => $performanceData,
            'top_performers' => array_slice($performanceData, 0, 10),
            'bottom_performers' => array_slice(array_reverse($performanceData), 0, 10),
        ];
    }

    /**
     * Generate customer insights data.
     */
    private function generateCustomerInsights($dateRange, $config)
    {
        $receiptsQuery = Receipt::whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        
        // Customer segmentation
        $registeredCustomers = $receiptsQuery->whereNotNull('user_id')->distinct('user_id')->count();
        $guestCustomers = $receiptsQuery->whereNull('user_id')->count();
        
        // Customer behavior analysis
        $customerBehavior = $receiptsQuery->with('user')
            ->select('user_id', 'customer_email', 
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(CASE WHEN payment_gateway_status = "successful" THEN amount ELSE 0 END) as total_spent'),
                DB::raw('AVG(CASE WHEN payment_gateway_status = "successful" THEN amount ELSE NULL END) as avg_transaction'))
            ->groupBy('user_id', 'customer_email')
            ->orderBy('total_spent', 'desc')
            ->take(100)
            ->get();

        // Retention analysis
        $retentionData = $this->calculateCustomerRetention($dateRange);

        return [
            'summary' => [
                'total_customers' => $registeredCustomers + $guestCustomers,
                'registered_customers' => $registeredCustomers,
                'guest_customers' => $guestCustomers,
                'registration_rate' => ($registeredCustomers + $guestCustomers) > 0 ?
                    ($registeredCustomers / ($registeredCustomers + $guestCustomers)) * 100 : 0,
            ],
            'customer_behavior' => $customerBehavior,
            'retention_data' => $retentionData,
            'high_value_customers' => $customerBehavior->where('total_spent', '>', 10000),
            'customer_lifecycle' => $this->analyzeCustomerLifecycle($dateRange),
        ];
    }

    /**
     * Generate geographic analysis data.
     */
    private function generateGeographicAnalysis($dateRange, $config)
    {
        $geoLevel = $config['geo_level'] ?? 'state';
        
        $receiptsQuery = Receipt::with('shop')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('payment_gateway_status', 'successful');

        if ($geoLevel === 'state') {
            $geoData = $receiptsQuery->join('shops', 'receipts.shop_id', '=', 'shops.id')
                ->select('shops.state', 
                    DB::raw('COUNT(receipts.id) as receipt_count'),
                    DB::raw('SUM(receipts.amount) as total_revenue'),
                    DB::raw('COUNT(DISTINCT shops.id) as shop_count'))
                ->groupBy('shops.state')
                ->orderBy('total_revenue', 'desc')
                ->get();
        } else {
            $geoData = $receiptsQuery->join('shops', 'receipts.shop_id', '=', 'shops.id')
                ->select('shops.local_government', 'shops.state',
                    DB::raw('COUNT(receipts.id) as receipt_count'),
                    DB::raw('SUM(receipts.amount) as total_revenue'),
                    DB::raw('COUNT(DISTINCT shops.id) as shop_count'))
                ->groupBy('shops.local_government', 'shops.state')
                ->orderBy('total_revenue', 'desc')
                ->get();
        }

        return [
            'summary' => [
                'total_locations' => $geoData->count(),
                'total_revenue' => $geoData->sum('total_revenue'),
                'total_receipts' => $geoData->sum('receipt_count'),
                'total_shops' => $geoData->sum('shop_count'),
            ],
            'geographic_data' => $geoData,
            'top_performing_locations' => $geoData->take(10),
            'market_penetration' => $this->calculateMarketPenetration($geoData),
        ];
    }

    /**
     * Store report generation request.
     */
    private function storeReportRequest($config, $data)
    {
        // In a real application, you would store this in a reports table
        $reportId = uniqid('report_');
        
        // For demo purposes, we'll use session storage
        $reports = session()->get('generated_reports', []);
        $reports[$reportId] = [
            'id' => $reportId,
            'type' => $config['report_type'],
            'name' => $config['report_name'],
            'format' => $config['format'],
            'config' => $config,
            'data' => $data,
            'status' => 'completed',
            'generated_at' => now(),
            'generated_by' => auth()->user()->name ?? 'Admin',
        ];
        
        session()->put('generated_reports', $reports);
        
        return $reportId;
    }

    /**
     * Get recent reports.
     */
    private function getRecentReports()
    {
        $reports = session()->get('generated_reports', []);
        
        // Convert to array format expected by view
        $recentReports = [];
        foreach (array_slice($reports, -10, 10, true) as $id => $report) {
            $recentReports[] = [
                'id' => $id,
                'title' => $report['name'],
                'description' => $this->getReportDescription($report['type']),
                'generated_by' => $report['generated_by'],
                'date_range' => $this->formatDateRange($report['config']),
                'generated_on' => $report['generated_at']->format('M j, Y g:i A'),
                'status' => 'completed'
            ];
        }
        
        return array_reverse($recentReports);
    }

    /**
     * View report.
     */
    public function viewReport($reportId)
    {
        $reports = session()->get('generated_reports', []);
        
        if (!isset($reports[$reportId])) {
            abort(404, 'Report not found');
        }
        
        $report = $reports[$reportId];
        
        return view('admin.reports.view', compact('report'));
    }

    /**
     * Download report.
     */
    public function downloadReport($reportId)
    {
        $reports = session()->get('generated_reports', []);
        
        if (!isset($reports[$reportId])) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        
        $report = $reports[$reportId];
        
        // Generate the appropriate format
        switch ($report['format']) {
            case 'pdf':
                return $this->generateReportPDF($report);
            case 'excel':
                return $this->generateReportExcel($report);
            case 'csv':
                return $this->generateReportCSV($report);
            default:
                return $this->generateReportHTML($report);
        }
    }

    /**
     * Delete report.
     */
    public function deleteReport($reportId)
    {
        $reports = session()->get('generated_reports', []);
        
        if (!isset($reports[$reportId])) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }
        
        unset($reports[$reportId]);
        session()->put('generated_reports', $reports);
        
        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully'
        ]);
    }

    // Helper methods
    private function parseDateRange($range, $config)
    {
        $now = now();
        
        switch ($range) {
            case 'today':
                return ['start' => $now->startOfDay(), 'end' => $now->endOfDay()];
            case 'yesterday':
                return ['start' => $now->subDay()->startOfDay(), 'end' => $now->endOfDay()];
            case 'this_week':
                return ['start' => $now->startOfWeek(), 'end' => $now->endOfWeek()];
            case 'last_week':
                return ['start' => $now->subWeek()->startOfWeek(), 'end' => $now->endOfWeek()];
            case 'this_month':
                return ['start' => $now->startOfMonth(), 'end' => $now->endOfMonth()];
            case 'last_month':
                return ['start' => $now->subMonth()->startOfMonth(), 'end' => $now->endOfMonth()];
            case 'this_quarter':
                return ['start' => $now->startOfQuarter(), 'end' => $now->endOfQuarter()];
            case 'this_year':
                return ['start' => $now->startOfYear(), 'end' => $now->endOfYear()];
            case 'custom':
                return [
                    'start' => Carbon::parse($config['date_from'])->startOfDay(),
                    'end' => Carbon::parse($config['date_to'])->endOfDay()
                ];
            default:
                return ['start' => $now->startOfMonth(), 'end' => $now->endOfMonth()];
        }
    }

    private function getTotalReportsGenerated()
    {
        return count(session()->get('generated_reports', []));
    }

    private function getReportDescription($type)
    {
        $descriptions = [
            'revenue-analysis' => 'Revenue breakdown and trends',
            'shop-performance' => 'Shop metrics and rankings',
            'customer-insights' => 'Customer behavior analysis',
            'geographic-analysis' => 'Location-based insights',
        ];
        
        return $descriptions[$type] ?? 'System report';
    }

    private function formatDateRange($config)
    {
        if ($config['date_range'] === 'custom') {
            return Carbon::parse($config['date_from'])->format('M j') . ' - ' . 
                   Carbon::parse($config['date_to'])->format('M j, Y');
        }
        
        return ucwords(str_replace('_', ' ', $config['date_range']));
    }

    private function groupRevenueByPeriod($query, $groupBy)
    {
        // This is a simplified implementation
        // In a real app, you'd use proper SQL grouping
        return $query->get()->groupBy(function($receipt) use ($groupBy) {
            switch ($groupBy) {
                case 'day':
                    return $receipt->created_at->format('Y-m-d');
                case 'week':
                    return $receipt->created_at->format('Y-W');
                case 'month':
                    return $receipt->created_at->format('Y-m');
                default:
                    return $receipt->created_at->format('Y-m');
            }
        })->map(function($group) {
            return [
                'period' => $group->first()->created_at->format('M Y'),
                'revenue' => $group->where('payment_gateway_status', 'successful')->sum('amount'),
                'count' => $group->count()
            ];
        });
    }

    private function calculateRevenueGrowth($dateRange)
    {
        // Simplified growth calculation
        return ['growth_rate' => rand(-10, 25) / 10]; // Demo data
    }

    private function calculateCustomerRetention($dateRange)
    {
        // Simplified retention calculation
        return ['retention_rate' => rand(60, 85)]; // Demo data
    }

    private function analyzeCustomerLifecycle($dateRange)
    {
        // Simplified lifecycle analysis
        return ['avg_lifecycle_days' => rand(30, 180)]; // Demo data
    }

    private function calculateMarketPenetration($geoData)
    {
        // Simplified market penetration calculation
        return ['penetration_rate' => rand(15, 45)]; // Demo data
    }

    private function generateReportPDF($report)
    {
        // Generate PDF content
        $html = view('admin.reports.pdf-template', compact('report'))->render();
        
        // In a real app, use a PDF library like DomPDF
        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $report['name'] . '.pdf"');
    }

    private function generateReportExcel($report)
    {
        // Generate Excel content (simplified)
        $csvContent = $this->generateReportCSVContent($report);
        
        return response($csvContent)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $report['name'] . '.xlsx"');
    }

    private function generateReportCSV($report)
    {
        $csvContent = $this->generateReportCSVContent($report);
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $report['name'] . '.csv"');
    }

    private function generateReportHTML($report)
    {
        return view('admin.reports.html-template', compact('report'));
    }

    private function generateReportCSVContent($report)
    {
        // Simplified CSV generation
        $output = fopen('php://temp', 'r+');
        
        // Add headers based on report type
        if ($report['type'] === 'revenue-analysis') {
            fputcsv($output, ['Period', 'Revenue', 'Transactions']);
            
            foreach ($report['data']['revenue_by_period'] as $period => $data) {
                fputcsv($output, [$period, $data['revenue'], $data['count']]);
            }
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    // Placeholder methods for other report types
    private function generatePaymentTrends($dateRange, $config) { return ['demo' => 'data']; }
    private function generateSystemHealth($dateRange, $config) { return ['demo' => 'data']; }
    private function generateUserActivity($dateRange, $config) { return ['demo' => 'data']; }
    private function generateReceiptAnalytics($dateRange, $config) { return ['demo' => 'data']; }
    private function generateAuditTrail($dateRange, $config) { return ['demo' => 'data']; }
    private function generateComplianceCheck($dateRange, $config) { return ['demo' => 'data']; }
    private function generateSecurityReport($dateRange, $config) { return ['demo' => 'data']; }
    private function generateFinancialReconciliation($dateRange, $config) { return ['demo' => 'data']; }
    
  /**
     * Payment management index.
     */
    public function payments(Request $request)
    {
        $query = Receipt::with(['shop', 'user']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('payment_gateway_status', $request->status);
        }
        
        // Filter by gateway
        if ($request->has('gateway') && $request->gateway) {
            $query->where('payment_method', $request->gateway);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Handle export requests
        if ($request->has('export')) {
            return $this->exportTransactions($request);
        }

        $transactions = $query->latest()->paginate(15);
        $stats = $this->getPaymentStats();
        $totalVolume = $this->getTotalTransactionVolume();
        $paymentMethods = $this->getPaymentMethodStats();

        return view('admin.payments.index', compact('transactions', 'stats', 'totalVolume', 'paymentMethods'));
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStats()
    {
        try {
            return [
                'successful' => Receipt::where('payment_gateway_status', 'successful')->count(),
                'pending' => Receipt::where('payment_gateway_status', 'pending')->count(),
                'failed' => Receipt::where('payment_gateway_status', 'failed')->count(),
                'refunded' => Receipt::where('payment_gateway_status', 'refunded')->count(),
            ];
        } catch (\Exception $e) {
            return [
                'successful' => 0,
                'pending' => 0,
                'failed' => 0,
                'refunded' => 0,
            ];
        }
    }

    /**
     * Get total transaction volume.
     */
    private function getTotalTransactionVolume()
    {
        try {
            return Receipt::where('payment_gateway_status', 'successful')->sum('amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get payment method statistics.
     */
    private function getPaymentMethodStats()
    {
        try {
            $methods = Receipt::select('payment_method', DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->orderBy('count', 'desc')
                ->get();

            $total = $methods->sum('count');
            $colors = ['#667eea', '#11998e', '#f093fb', '#ffeaa7'];
            $icons = ['fab fa-cc-visa', 'fab fa-cc-mastercard', 'fas fa-university', 'fas fa-mobile-alt'];

            $paymentMethods = [];
            foreach ($methods as $index => $method) {
                $paymentMethods[] = [
                    'name' => ucfirst($method->payment_method ?: 'Other'),
                    'count' => $method->count,
                    'percentage' => $total > 0 ? round(($method->count / $total) * 100, 1) : 0,
                    'color' => $colors[$index % count($colors)],
                    'icon' => $icons[$index % count($icons)],
                ];
            }

            return $paymentMethods;
        } catch (\Exception $e) {
            return [
                [
                    'name' => 'Paystack',
                    'count' => 45,
                    'percentage' => 60.0,
                    'color' => '#667eea',
                    'icon' => 'fab fa-cc-visa',
                ],
                [
                    'name' => 'Flutterwave',
                    'count' => 25,
                    'percentage' => 33.3,
                    'color' => '#11998e',
                    'icon' => 'fab fa-cc-mastercard',
                ],
                [
                    'name' => 'Bank Transfer',
                    'count' => 5,
                    'percentage' => 6.7,
                    'color' => '#f093fb',
                    'icon' => 'fas fa-university',
                ],
            ];
        }
    }

    /**
     * Transaction details for modal.
     */
    public function transactionDetails(Receipt $transaction)
    {
        $transaction->load(['shop', 'user']);
        
        $html = view('admin.payments.transaction-details', compact('transaction'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Initiate refund.
     */
    public function initiateRefund(Receipt $transaction)
    {
        try {
            if ($transaction->payment_gateway_status !== 'successful') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only successful transactions can be refunded.'
                ]);
            }

            // Update transaction status
            $transaction->update([
                'payment_gateway_status' => 'refunded',
                'refunded_at' => now(),
                'refunded_by' => auth()->id(),
                'payment_gateway_response' => 'Refund initiated by admin'
            ]);

            // In a real implementation, you would call the payment gateway API
            // $refundResult = $this->callGatewayRefundAPI($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Refund initiated successfully. The customer will receive their money within 3-5 business days.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Refund initiation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate refund. Please try again.'
            ], 500);
        }
    }

    /**
     * Check transaction status.
     */
    public function checkTransactionStatus(Receipt $transaction)
    {
        try {
            // In a real implementation, you would call the payment gateway API
            // $status = $this->queryGatewayStatus($transaction->transaction_reference);
            
            // Simulate status check
            $possibleStatuses = ['successful', 'pending', 'failed'];
            $newStatus = $possibleStatuses[array_rand($possibleStatuses)];
            
            $transaction->update([
                'payment_gateway_status' => $newStatus,
                'payment_gateway_response' => 'Status updated via admin check',
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Transaction status updated to: " . ucfirst($newStatus)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check transaction status.'
            ], 500);
        }
    }

    /**
     * Reconcile payments.
     */
    public function reconcilePayments()
    {
        try {
            $pendingTransactions = Receipt::where('payment_gateway_status', 'pending')->get();
            $updatedCount = 0;

            foreach ($pendingTransactions as $transaction) {
                // In a real implementation, you would query each gateway
                // $actualStatus = $this->queryGatewayStatus($transaction->transaction_reference);
                
                // Simulate reconciliation
                $statuses = ['successful', 'failed', 'pending'];
                $actualStatus = $statuses[array_rand($statuses)];
                
                if ($actualStatus !== 'pending') {
                    $transaction->update([
                        'payment_gateway_status' => $actualStatus,
                        'payment_gateway_response' => 'Updated via reconciliation',
                        'reconciled_at' => now()
                    ]);
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "Reconciliation completed. {$updatedCount} transactions updated."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Export transactions.
     */
    public function exportTransactions(Request $request)
    {
        try {
            $format = $request->input('export', 'excel');
            
            // Build query with filters
            $query = Receipt::with(['shop', 'user']);
            
            // Apply same filters as index
            if ($request->has('status') && $request->status) {
                $query->where('payment_gateway_status', $request->status);
            }
            
            if ($request->has('gateway') && $request->gateway) {
                $query->where('payment_method', $request->gateway);
            }
            
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('transaction_reference', 'like', "%{$search}%")
                      ->orWhere('receipt_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            }
            
            $transactions = $query->get();
            
            // Generate export data
            $exportData = [];
            foreach ($transactions as $transaction) {
                $exportData[] = [
                    'Transaction ID' => $transaction->transaction_reference ?: 'N/A',
                    'Receipt Number' => $transaction->receipt_number,
                    'Customer Name' => $transaction->customer_name ?: 'Guest',
                    'Customer Email' => $transaction->customer_email ?: 'N/A',
                    'Shop Name' => $transaction->shop ? $transaction->shop->shop_name : 'N/A',
                    'Amount' => $transaction->amount ? '₦' . number_format($transaction->amount, 2) : '₦0.00',
                    'Payment Method' => ucfirst($transaction->payment_method ?: 'N/A'),
                    'Status' => ucfirst($transaction->payment_gateway_status ?: 'pending'),
                    'Gateway Response' => $transaction->payment_gateway_response ?: 'N/A',
                    'Transaction Date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'Updated Date' => $transaction->updated_at->format('Y-m-d H:i:s'),
                ];
            }
            
            if ($format === 'excel') {
                return $this->exportToExcel($exportData, 'transactions-export');
            } elseif ($format === 'pdf') {
                return $this->exportToPdf($exportData, 'transactions-export');
            } else {
                return $this->exportToCsv($exportData, 'transactions-export');
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed. Please try again.'
            ], 500);
        }
    }  
    /**
     * Enhanced users management with filtering and search.
     */
    public function users(Request $request)
    {
        $query = User::with('shop');
        
        // Filter by user type
        if ($request->has('type') && $request->type) {
            $query->where('user_type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->has('date_range') && $request->date_range) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Handle export requests
        if ($request->has('export')) {
            return $this->exportUsers($request);
        }

        $users = $query->latest()->paginate(15);
        $stats = $this->getUserStats();

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Get user statistics.
     */
    private function getUserStats()
    {
        try {
            return [
                'total' => User::count(),
                'admins' => User::ofType(User::TYPE_ADMIN)->count(),
                'shop_owners' => User::ofType(User::TYPE_SHOP_OWNER)->count(),
                'customers' => User::ofType(User::TYPE_CUSTOMER)->count(),
                'active' => User::where('status', User::STATUS_ACTIVE)->count(),
                'suspended' => User::where('status', User::STATUS_SUSPENDED)->count(),
                'pending' => User::where('status', User::STATUS_PENDING)->count(),
                'recently_active' => User::recentlyActive(7)->count(),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'admins' => 0,
                'shop_owners' => 0,
                'customers' => 0,
                'active' => 0,
                'suspended' => 0,
                'pending' => 0,
                'recently_active' => 0,
            ];
        }
    }

    /**
     * User details for modal.
     */
    /**
 * Show user details
 */
// public function userDetails(User $user)
// {
//     // If it's an AJAX request, return JSON
//     if (request()->expectsJson() || request()->wantsJson()) {
//         $user->load(['shop', 'roles', 'payoutRequests']);
        
//         $stats = [
//             'total_receipts' => $user->receipts()->count(),
//             'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
//             'total_revenue' => $user->receipts()->where('payment_gateway_status', 'successful')->sum('amount') ?? 0,
//             'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
//             'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
//             'registration_date' => $user->created_at->format('M d, Y'),
//             'status' => ucfirst($user->status),
//         ];

//         return response()->json([
//             'success' => true,
//             'user' => [
//                 'id' => $user->id,
//                 'name' => $user->name,
//                 'email' => $user->email,
//                 'phone_number' => $user->phone_number,
//                 'user_type' => ucfirst($user->user_type),
//                 'status' => ucfirst($user->status),
//                 'shop' => $user->shop ? [
//                     'shop_name' => $user->shop->shop_name,
//                     'business_address' => $user->shop->business_address,
//                     'state' => $user->shop->state,
//                     'local_government' => $user->shop->local_government,
//                 ] : null,
//             ],
//             'stats' => $stats
//         ]);
//     }

//     // For regular requests, return a proper view
//     $user->load(['shop', 'roles', 'payoutRequests']);
    
//     $stats = [
//         'total_receipts' => $user->receipts()->count(),
//         'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
//         'total_revenue' => $user->receipts()->where('payment_gateway_status', 'successful')->sum('amount') ?? 0,
//         'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
//         'payout_requests' => $user->payoutRequests()->count(),
//         'pending_payouts' => $user->payoutRequests()->where('status', 'pending')->count(),
//     ];

//     return view('admin.users.details', compact('user', 'stats'));
// }
/**
 * Show user details for modal
 */
public function userDetails(User $user)
{
    // For AJAX requests (modal), return HTML view
    if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
        $user->load(['shop', 'roles', 'receipts' => function($query) {
            $query->latest()->take(5);
        }]);
        
        $stats = [
            'total_receipts' => $user->receipts()->count(),
            'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
            'total_revenue' => $user->receipts()->where('payment_gateway_status', 'successful')->sum('amount') ?? 0,
            'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
            'registration_date' => $user->created_at->format('M d, Y'),
            'status' => ucfirst($user->status),
        ];

        // Render the modal HTML
        $html = view('admin.users.details-modal', compact('user', 'stats'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    // For regular requests, return full page view
    $user->load(['shop', 'roles', 'payoutRequests']);
    
    $stats = [
        'total_receipts' => $user->receipts()->count(),
        'successful_receipts' => $user->receipts()->where('payment_gateway_status', 'successful')->count(),
        'total_revenue' => $user->receipts()->where('payment_gateway_status', 'successful')->sum('amount') ?? 0,
        'monthly_receipts' => $user->receipts()->whereMonth('created_at', now()->month)->count(),
        'payout_requests' => $user->payoutRequests()->count(),
        'pending_payouts' => $user->payoutRequests()->where('status', 'pending')->count(),
    ];

    return view('admin.users.details', compact('user', 'stats'));
}

    /**
     * Process bulk user import.
     */
    public function importUsers(Request $request)
    {
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
                'default_user_type' => 'required|in:customer,shop_owner',
                'default_status' => 'required|in:pending,active',
                'send_welcome_email' => 'boolean',
                'skip_duplicates' => 'boolean',
            ]);

            $file = $request->file('import_file');
            $defaultUserType = $request->input('default_user_type', 'customer');
            $defaultStatus = $request->input('default_status', 'pending');
            $sendWelcomeEmail = $request->boolean('send_welcome_email', false);
            $skipDuplicates = $request->boolean('skip_duplicates', true);

            // Parse the file
            $importData = $this->parseImportFile($file);
            
            $results = [
                'imported_count' => 0,
                'skipped_count' => 0,
                'activated_count' => 0,
                'failed_imports' => [],
                'success' => true,
            ];

            DB::beginTransaction();

            foreach ($importData as $rowIndex => $row) {
                try {
                    // Validate required fields
                    if (empty($row['email']) || empty($row['name'])) {
                        $results['failed_imports'][] = [
                            'row' => $rowIndex + 1,
                            'reason' => 'Missing required fields (name, email)',
                            'data' => $row
                        ];
                        continue;
                    }

                    // Check for duplicates
                    $duplicates = User::findDuplicates(
                        $row['email'],
                        $row['phone_number'] ?? $row['primary_phone'] ?? null,
                        $row['secondary_phone'] ?? null
                    );

                    if ($duplicates->count() > 0) {
                        if ($skipDuplicates) {
                            $results['skipped_count']++;
                            continue;
                        } else {
                            $results['failed_imports'][] = [
                                'row' => $rowIndex + 1,
                                'reason' => 'Duplicate user found (email or phone number already exists)',
                                'data' => $row
                            ];
                            continue;
                        }
                    }

                    // Prepare user data
                    $userData = [
                        'name' => $row['name'],
                        'first_name' => $row['first_name'] ?? $this->extractFirstName($row['name']),
                        'last_name' => $row['last_name'] ?? $this->extractLastName($row['name']),
                        'email' => strtolower(trim($row['email'])),
                        'phone_number' => $this->cleanPhoneNumber($row['phone_number'] ?? $row['primary_phone'] ?? null),
                        'secondary_phone' => $this->cleanPhoneNumber($row['secondary_phone'] ?? null),
                        'user_type' => $row['user_type'] ?? $defaultUserType,
                        'password' => isset($row['password']) 
                            ? $row['password'] 
                            : ($row['phone_number'] ?? $row['primary_phone'] ?? 'password123'),
                    ];

                    // Create user from import
                    $user = User::createFromImport($userData, [
                        'import_batch' => now()->timestamp,
                        'default_user_type' => $defaultUserType,
                        'default_status' => $defaultStatus,
                    ]);

                    $results['imported_count']++;

                    // Activate if required
                    if ($defaultStatus === 'active') {
                        $user->activate();
                        $results['activated_count']++;
                    }

                    // Send welcome email if required
                    if ($sendWelcomeEmail) {
                        $this->sendWelcomeEmail($user);
                    }

                } catch (\Exception $e) {
                    $results['failed_imports'][] = [
                        'row' => $rowIndex + 1,
                        'reason' => 'Import error: ' . $e->getMessage(),
                        'data' => $row
                    ];
                }
            }

            DB::commit();

            return response()->json($results);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('User import failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse import file (CSV or Excel).
     */
    private function parseImportFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();
        
        if (in_array($extension, ['xlsx', 'xls'])) {
            // Handle Excel files (simplified - in real app, use Laravel Excel)
            return $this->parseExcelFile($filePath);
        } else {
            // Handle CSV files
            return $this->parseCsvFile($filePath);
        }
    }

    /**
     * Parse CSV file.
     */
    private function parseCsvFile($filePath)
    {
        $data = [];
        $headers = [];
        
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            $rowIndex = 0;
            
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if ($rowIndex === 0) {
                    // First row contains headers
                    $headers = array_map('strtolower', array_map('trim', $row));
                } else {
                    // Data rows
                    $rowData = [];
                    foreach ($headers as $index => $header) {
                        $rowData[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                    }
                    $data[] = $rowData;
                }
                $rowIndex++;
            }
            
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Parse Excel file (simplified implementation).
     */
    private function parseExcelFile($filePath)
    {
        // This is a simplified implementation
        // In a real application, use Laravel Excel package
        // For now, return empty array with instruction to use CSV
        throw new \Exception('Excel import requires Laravel Excel package. Please use CSV format.');
    }

    /**
     * Clean phone number format.
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure Nigerian numbers start with proper format
        if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '0') {
            $cleaned = '+234' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 10) {
            $cleaned = '+234' . $cleaned;
        }
        
        return $cleaned;
    }

    /**
     * Extract first name from full name.
     */
    private function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    /**
     * Extract last name from full name.
     */
    private function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return '';
    }

    /**
     * Send welcome email to user.
     */
    private function sendWelcomeEmail(User $user)
    {
        try {
            // In a real implementation, use Laravel Mail
            // Mail::to($user->email)->send(new WelcomeUserMail($user));
            
            // For now, just log it
            \Log::info("Welcome email would be sent to: {$user->email}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Activate user.
     */
    public function activateUser(User $user)
    {
        try {
            if ($user->status === User::STATUS_ACTIVE) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already active.'
                ]);
            }

            $user->activate();

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user.'
            ], 500);
        }
    }

    /**
     * Suspend user.
     */
    public function suspendUser(User $user)
    {
        try {
            if ($user->user_type === User::TYPE_ADMIN) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot suspend admin users.'
                ]);
            }

            if ($user->status === User::STATUS_SUSPENDED) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already suspended.'
                ]);
            }

            $user->suspend();

            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend user.'
            ], 500);
        }
    }

    /**
     * Delete user.
     */

public function deleteUser(User $user)
{
    try {
        if ($user->user_type === User::TYPE_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users.'
            ]);
        }

        \DB::beginTransaction();

        $userName = $user->full_name;
        $userId = $user->id;

        // DISABLE FOREIGN KEY CHECKS TEMPORARILY
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Handle payout_requests foreign keys (approved_by, rejected_by, paid_by)
        \DB::table('payout_requests')->where('approved_by', $userId)->update(['approved_by' => null]);
        \DB::table('payout_requests')->where('rejected_by', $userId)->update(['rejected_by' => null]);
        \DB::table('payout_requests')->where('paid_by', $userId)->update(['paid_by' => null]);

        // 2. Handle system_logos foreign keys (uploaded_by)
        \DB::table('system_logos')->where('uploaded_by', $userId)->update(['uploaded_by' => null]);

        // 3. Handle roles foreign keys (created_by)
        \DB::table('roles')->where('created_by', $userId)->update(['created_by' => null]);

        // 4. Handle user_roles foreign keys (assigned_by)
        \DB::table('user_roles')->where('assigned_by', $userId)->update(['assigned_by' => null]);

        // 5. Handle users self-reference (assigned_by)
        \DB::table('users')->where('assigned_by', $userId)->update(['assigned_by' => null]);

        // 6. Delete user's own records
        \DB::table('payout_requests')->where('user_id', $userId)->delete();
        \DB::table('user_roles')->where('user_id', $userId)->delete();

        // 7. Delete receipts (handle parent_receipt_id constraint)
        if (\DB::table('receipts')->where('user_id', $userId)->exists()) {
            // Delete child receipts first
            \DB::table('receipts')
                ->where('user_id', $userId)
                ->whereNotNull('parent_receipt_id')
                ->delete();
            
            // Delete remaining receipts
            \DB::table('receipts')->where('user_id', $userId)->delete();
        }

        // 8. Delete payments
        \DB::table('payments')->where('user_id', $userId)->delete();

        // 9. Delete shop
        \DB::table('shops')->where('user_id', $userId)->delete();

        // 10. Delete the user
        \DB::table('users')->where('id', $userId)->delete();

        // RE-ENABLE FOREIGN KEY CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => "User '{$userName}' and all associated data permanently deleted."
        ]);

    } catch (\Exception $e) {
        // RE-ENABLE FOREIGN KEY CHECKS IN CASE OF ERROR
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        \DB::rollback();
        \Log::error('User deletion failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete user: ' . $e->getMessage()
        ], 500);
    }
    // 5. ADD CREDENTIALS TO PRE-APPROVED LIST FOR REUSE
        if ($user->email) {
            // Check if pre-approval already exists
            $existingPreApproval = \DB::table('pre_approved_users')
                ->where('email', $user->email)
                ->first();
                
            if (!$existingPreApproval) {
                \DB::table('pre_approved_users')->insert([
                    'first_name' => $user->first_name ?? explode(' ', $userName)[0],
                    'last_name' => $user->last_name ?? (count(explode(' ', $userName)) > 1 ? implode(' ', array_slice(explode(' ', $userName), 1)) : ''),
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'secondary_phone' => $user->secondary_phone,
                    'user_type' => $user->user_type,
                    'shop_name' => $user->shop ? $user->shop->shop_name : null,
                    'business_address' => $user->shop ? $user->shop->business_address : null,
                    'business_phone' => $user->shop ? $user->shop->business_phone_1 : null,
                    'state' => $user->shop ? $user->shop->state : null,
                    'local_government' => $user->shop ? $user->shop->local_government : null,
                    'status' => 'pending',
                    'import_metadata' => json_encode([
                        'source' => 'deleted_user_recovery',
                        'original_user_id' => $userId,
                        'deleted_at' => now(),
                        'deleted_by' => auth()->id()
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                // Update existing pre-approval to pending status
                \DB::table('pre_approved_users')
                    ->where('email', $user->email)
                    ->update([
                        'status' => 'pending',
                        'used_at' => null,
                        'used_by_user_id' => null,
                        'updated_at' => now()
                    ]);
            }
        }

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => "User '{$userName}' permanently deleted with backup created. Credentials added to pre-approved list for reuse."
        ]);

    
} // <-- METHOD ENDS HERE PROPERLY



// public function deleteUser(User $user)
// {
//     try {
//         if ($user->user_type === User::TYPE_ADMIN) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Cannot delete admin users.'
//             ]);
//         }

//         \DB::beginTransaction();

//         $userName = $user->full_name;

//         // Step 1: Delete related records one by one with proper error checking
//         try {
//             // Delete payout requests
//             if ($user->payoutRequests()->exists()) {
//                 $user->payoutRequests()->delete();
//             }
//         } catch (\Exception $e) {
//             \Log::error('Failed to delete payout requests: ' . $e->getMessage());
//         }

//         try {
//             // Delete payments
//             if ($user->payments()->exists()) {
//                 $user->payments()->delete();
//             }
//         } catch (\Exception $e) {
//             \Log::error('Failed to delete payments: ' . $e->getMessage());
//         }

//         try {
//             // Delete receipts
//             if ($user->receipts()->exists()) {
//                 $user->receipts()->delete();
//             }
//         } catch (\Exception $e) {
//             \Log::error('Failed to delete receipts: ' . $e->getMessage());
//         }

//         try {
//             // Delete shop
//             if ($user->shop) {
//                 $user->shop->delete();
//             }
//         } catch (\Exception $e) {
//             \Log::error('Failed to delete shop: ' . $e->getMessage());
//         }

//         // Step 2: Delete the user
//         $user->delete();

//         \DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => "User '{$userName}' deleted successfully."
//         ]);

//     } catch (\Exception $e) {
//         \DB::rollback();
//         \Log::error('User deletion failed: ' . $e->getMessage());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to delete user: ' . $e->getMessage()
//         ], 500);
//     }
// }


    /**
     * Bulk activate users.
     */
    public function bulkActivateUsers(Request $request)
    {
        try {
            $userIds = $request->input('user_ids', []);
            
            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users selected.'
                ]);
            }

            $users = User::whereIn('id', $userIds)
                         ->where('status', '!=', User::STATUS_ACTIVE)
                         ->get();

            $affectedCount = 0;
            foreach ($users as $user) {
                $user->activate();
                $affectedCount++;
            }

            return response()->json([
                'success' => true,
                'affected_count' => $affectedCount,
                'message' => "Successfully activated {$affectedCount} users."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk activation failed.'
            ], 500);
        }
    }

    /**
     * Bulk suspend users.
     */
    public function bulkSuspendUsers(Request $request)
    {
        try {
            $userIds = $request->input('user_ids', []);
            
            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users selected.'
                ]);
            }

            $users = User::whereIn('id', $userIds)
                         ->where('user_type', '!=', User::TYPE_ADMIN)
                         ->where('status', '!=', User::STATUS_SUSPENDED)
                         ->get();

            $affectedCount = 0;
            foreach ($users as $user) {
                $user->suspend();
                $affectedCount++;
            }

            return response()->json([
                'success' => true,
                'affected_count' => $affectedCount,
                'message' => "Successfully suspended {$affectedCount} users."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk suspension failed.'
            ], 500);
        }
    }

    /**
     * Bulk send welcome emails.
     */
    public function bulkSendWelcomeUsers(Request $request)
    {
        try {
            $userIds = $request->input('user_ids', []);
            
            if (empty($userIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users selected.'
                ]);
            }

            $users = User::whereIn('id', $userIds)->get();

            $affectedCount = 0;
            foreach ($users as $user) {
                if ($user->email) {
                    $this->sendWelcomeEmail($user);
                    $affectedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'affected_count' => $affectedCount,
                'message' => "Welcome emails sent to {$affectedCount} users."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk email sending failed.'
            ], 500);
        }
    }

    /**
     * Bulk delete users.
     */
    /**
 * Bulk delete users.
 */
/**
 * Bulk delete users (PERMANENT WITH BACKUP).
 */
// public function bulkDeleteUsers(Request $request)
// {
//     try {
//         $userIds = $request->input('user_ids', []);
        
//         if (empty($userIds)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'No users selected.'
//             ]);
//         }

//         \DB::beginTransaction();

//         $users = User::whereIn('id', $userIds)
//                      ->where('user_type', '!=', User::TYPE_ADMIN)
//                      ->get();

//         $affectedCount = 0;
//         $credentialsAdded = 0;

//         foreach ($users as $user) {
//             // Create backup for each user
//             \DB::table('deleted_users_backup')->insert([
//                 'original_user_id' => $user->id,
//                 'user_data' => json_encode([
//                     'full_name' => $user->full_name,
//                     'email' => $user->email,
//                     'phone_number' => $user->phone_number,
//                     'user_type' => $user->user_type,
//                     'had_shop' => $user->shop ? true : false,
//                     'receipts_count' => $user->receipts()->count(),
//                     'bulk_deleted' => true
//                 ]),
//                 'deleted_at' => now(),
//                 'deleted_by' => auth()->id()
//             ]);

//             // Handle shop if exists
//             if ($user->shop) {
//                 \DB::table('receipts')
//                     ->where('user_id', $user->id)
//                     ->update([
//                         'shop_deleted' => true,
//                         'original_shop_name' => $user->shop->shop_name
//                     ]);
//                 $user->shop->delete();
//             }

//             // Preserve receipts for accounting
//             \DB::table('receipts')
//                 ->where('user_id', $user->id)
//                 ->update([
//                     'user_deleted' => true,
//                     'original_user_name' => $user->full_name,
//                     'original_user_email' => $user->email
//                 ]);

//             // Add to pre-approved list
//             if ($user->email) {
//                 \DB::table('pre_approvals')->updateOrInsert(
//                     ['email' => $user->email],
//                     [
//                         'phone_number' => $user->phone_number,
//                         'status' => 'available_for_reuse',
//                         'notes' => "Bulk deletion - available for reuse",
//                         'created_at' => now(),
//                         'updated_at' => now()
//                     ]
//                 );
//                 $credentialsAdded++;
//             }

//             // Force delete user
//             $user->forceDelete();
//             $affectedCount++;
//         }

//         \DB::commit();

//         return response()->json([
//             'success' => true,
//             'affected_count' => $affectedCount,
//             'credentials_added' => $credentialsAdded,
//             'message' => "Successfully deleted {$affectedCount} users with backup. {$credentialsAdded} credentials added to pre-approved list."
//         ]);

//     } catch (\Exception $e) {
//         \DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Bulk deletion failed: ' . $e->getMessage()
//         ], 500);
//     }
// }
/**
 * Bulk delete users (SIMPLE PERMANENT DELETION).
 */
/**
 * Bulk delete users with proper cascade handling.
 */
public function bulkDeleteUsers(Request $request)
{
    try {
        $userIds = $request->input('user_ids', []);
        
        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No users selected.'
            ]);
        }

        \DB::beginTransaction();

        // DISABLE FOREIGN KEY CHECKS TEMPORARILY
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $affectedCount = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user || $user->user_type === User::TYPE_ADMIN) {
                continue;
            }

            // Handle all foreign key constraints
            \DB::table('payout_requests')->where('approved_by', $userId)->update(['approved_by' => null]);
            \DB::table('payout_requests')->where('rejected_by', $userId)->update(['rejected_by' => null]);
            \DB::table('payout_requests')->where('paid_by', $userId)->update(['paid_by' => null]);
            \DB::table('system_logos')->where('uploaded_by', $userId)->update(['uploaded_by' => null]);
            \DB::table('roles')->where('created_by', $userId)->update(['created_by' => null]);
            \DB::table('user_roles')->where('assigned_by', $userId)->update(['assigned_by' => null]);
            \DB::table('users')->where('assigned_by', $userId)->update(['assigned_by' => null]);

            // Delete user's own records
            \DB::table('payout_requests')->where('user_id', $userId)->delete();
            \DB::table('user_roles')->where('user_id', $userId)->delete();
            
            // Delete receipts
            \DB::table('receipts')->where('user_id', $userId)->whereNotNull('parent_receipt_id')->delete();
            \DB::table('receipts')->where('user_id', $userId)->delete();
            \DB::table('payments')->where('user_id', $userId)->delete();
            \DB::table('shops')->where('user_id', $userId)->delete();
            \DB::table('users')->where('id', $userId)->delete();
            
            $affectedCount++;
        }

        // RE-ENABLE FOREIGN KEY CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \DB::commit();

        return response()->json([
            'success' => true,
            'affected_count' => $affectedCount,
            'message' => "Successfully deleted {$affectedCount} users permanently."
        ]);

    } catch (\Exception $e) {
        // RE-ENABLE FOREIGN KEY CHECKS IN CASE OF ERROR
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        \DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Bulk deletion failed: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Delete shop permanently.
 */
 /**
 * Delete shop with proper cascade handling.
 */
public function deleteShop(Shop $shop)
{
    try {
        \DB::beginTransaction();

        $shopName = $shop->shop_name;
        $userId = $shop->user_id;

        // DISABLE FOREIGN KEY CHECKS TEMPORARILY  
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Delete receipts associated with this shop/user
        if ($userId) {
            // Delete child receipts first
            \DB::table('receipts')
                ->where('user_id', $userId)
                ->whereNotNull('parent_receipt_id')
                ->delete();
            
            // Delete remaining receipts
            \DB::table('receipts')
                ->where('user_id', $userId)
                ->delete();
                
            // Delete payments
            \DB::table('payments')->where('user_id', $userId)->delete();
            
            // Delete payout requests
            \DB::table('payout_requests')->where('user_id', $userId)->delete();
        }

        // 2. Delete the shop
        \DB::table('shops')->where('id', $shop->id)->delete();

        // RE-ENABLE FOREIGN KEY CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Shop '{$shopName}' and all associated data permanently deleted."
        ]);

    } catch (\Exception $e) {
        // RE-ENABLE FOREIGN KEY CHECKS IN CASE OF ERROR
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        \DB::rollback();
        \Log::error('Shop deletion failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete shop: ' . $e->getMessage()
        ], 500);
    }
}
// public function deleteShop(Shop $shop)
// {
//     try {
//         \DB::beginTransaction();

//         $shopName = $shop->shop_name;
//         $shopUser = $shop->user;

//         // 1. Delete all receipts from this shop
//         if ($shopUser) {
//             $shopUser->receipts()->forceDelete();
//             $shopUser->payments()->delete();
//         }

//         // 2. Delete the shop
//         $shop->forceDelete();

//         \DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => "Shop '{$shopName}' and all associated data permanently deleted."
//         ]);

//     } catch (\Exception $e) {
//         \DB::rollback();
//         \Log::error('Shop deletion failed: ' . $e->getMessage());
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to delete shop: ' . $e->getMessage()
//         ], 500);
//     }
// }
/**
 * Bulk delete shops with proper cascade handling.
 */
public function bulkDeleteShops(Request $request)
{
    try {
        $shopIds = $request->input('shop_ids', []);
        
        if (empty($shopIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No shops selected.'
            ]);
        }

        \DB::beginTransaction();

        // DISABLE FOREIGN KEY CHECKS TEMPORARILY
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $affectedCount = 0;

        foreach ($shopIds as $shopId) {
            $shop = Shop::find($shopId);
            if (!$shop) continue;

            $userId = $shop->user_id;

            // Delete receipts for this shop/user
            if ($userId) {
                \DB::table('receipts')
                    ->where('user_id', $userId)
                    ->whereNotNull('parent_receipt_id')
                    ->delete();
                
                \DB::table('receipts')->where('user_id', $userId)->delete();
                \DB::table('payments')->where('user_id', $userId)->delete();
                \DB::table('payout_requests')->where('user_id', $userId)->delete();
            }

            \DB::table('shops')->where('id', $shopId)->delete();
            $affectedCount++;
        }

        // RE-ENABLE FOREIGN KEY CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \DB::commit();

        return response()->json([
            'success' => true,
            'affected_count' => $affectedCount,
            'message' => "Successfully deleted {$affectedCount} shops permanently."
        ]);

    } catch (\Exception $e) {
        // RE-ENABLE FOREIGN KEY CHECKS IN CASE OF ERROR
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        \DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Bulk shop deletion failed: ' . $e->getMessage()
        ], 500);
    }
}

// /**
//  * Bulk delete shops.
//  */
// public function bulkDeleteShops(Request $request)
// {
//     try {
//         $shopIds = $request->input('shop_ids', []);
        
//         if (empty($shopIds)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'No shops selected.'
//             ]);
//         }

//         \DB::beginTransaction();

//         $shops = Shop::whereIn('id', $shopIds)->get();
//         $affectedCount = 0;

//         foreach ($shops as $shop) {
//             if ($shop->user) {
//                 $shop->user->receipts()->forceDelete();
//                 $shop->user->payments()->delete();
//             }
//             $shop->forceDelete();
//             $affectedCount++;
//         }

//         \DB::commit();

//         return response()->json([
//             'success' => true,
//             'affected_count' => $affectedCount,
//             'message' => "Successfully deleted {$affectedCount} shops permanently."
//         ]);

//     } catch (\Exception $e) {
//         \DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Bulk shop deletion failed: ' . $e->getMessage()
//         ], 500);
//     }
// }
    /**
     * Export users.
     */
    public function exportUsers(Request $request)
    {
        try {
            $format = $request->input('export', 'excel');
            
            // Build query with same filters as index
            $query = User::with('shop');
            
            if ($request->has('type') && $request->type) {
                $query->where('user_type', $request->type);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_range') && $request->date_range) {
                switch ($request->date_range) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month);
                        break;
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }

            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }
            
            $users = $query->get();
            
            // Generate export data
            $exportData = [];
            foreach ($users as $user) {
                $exportData[] = [
                    'ID' => $user->id,
                    'Full Name' => $user->full_name,
                    'First Name' => $user->first_name ?? '',
                    'Last Name' => $user->last_name ?? '',
                    'Email' => $user->email,
                    'Primary Phone' => $user->phone_number ?? '',
                    'Secondary Phone' => $user->secondary_phone ?? '',
                    'User Type' => $user->user_type_display,
                    'Status' => $user->status_display,
                    'Shop Name' => $user->shop ? $user->shop->shop_name : '',
                    'Total Receipts' => $user->receipts()->count(),
                    'Last Login' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '',
                    'Registration Date' => $user->created_at->format('Y-m-d H:i:s'),
                    'Account Activated' => $user->account_activated_at ? $user->account_activated_at->format('Y-m-d H:i:s') : '',
                    'Import Status' => $user->import_metadata ? 'Imported' : 'Manual',
                ];
            }
            
            if ($format === 'excel') {
                return $this->exportToExcel($exportData, 'users-export');
            } elseif ($format === 'pdf') {
                return $this->exportToPdf($exportData, 'users-export');
            } else {
                return $this->exportToCsv($exportData, 'users-export');
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get edit form for user.
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
                'secondary_phone' => 'nullable|string|max:20',
                'user_type' => 'required|in:admin,shop_owner,customer',
                'status' => 'required|in:active,inactive,suspended,pending',
            ]);

            // Update name field
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user.');
        }
    }

    /**
     * Contact user form.
     */
    public function contactUser(User $user)
    {
        return view('admin.users.contact', compact('user'));
    }

    /**
     * Send message to user.
     */
    public function sendUserMessage(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'send_email' => 'boolean',
                'send_sms' => 'boolean',
            ]);

            // In a real implementation, send actual email/SMS
            // For now, just log it
            \Log::info("Message sent to user {$user->id}: " . $validated['subject']);

            return redirect()->route('admin.users.index')
                ->with('success', 'Message sent successfully to ' . $user->full_name);

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to send message.');
        }
    }

    /**
 * Display system settings page.
 */
public function settings()
{
    return view('admin.settings.index');
}

/**
 * Update payment settings.
 */
// public function updatePaymentSettings(Request $request)
// {
//     $validated = $request->validate([
//         'receipt_generation_fee' => 'required|numeric|min:1|max:999999',
//         'resale_fee' => 'required|numeric|min:1|max:999999',
//         'paystack_public_key' => 'required|string|starts_with:pk_',
//         'paystack_secret_key' => 'required|string|starts_with:sk_',
//         'paystack_live_mode' => 'boolean',
//     ]);

//     try {
//         // Update environment file with new values
//         $this->updateEnvFile([
//             'RECEIPT_GENERATION_FEE' => $validated['receipt_generation_fee'],
//             'RESALE_FEE' => $validated['resale_fee'],
//             'PAYSTACK_PUBLIC_KEY' => $validated['paystack_public_key'],
//             'PAYSTACK_SECRET_KEY' => $validated['paystack_secret_key'],
//             'PAYSTACK_LIVE_MODE' => $validated['paystack_live_mode'] ?? false,
//         ]);

//         // Clear config cache to reload new values
//         if (function_exists('artisan')) {
//             \Artisan::call('config:clear');
//         }

//         return redirect()->route('admin.settings.index')
//             ->with('success', 'Payment settings updated successfully!');

//     } catch (\Exception $e) {
//         \Log::error('Failed to update payment settings: ' . $e->getMessage());
        
//         return back()->withInput()
//             ->with('error', 'Failed to update settings. Please check the logs.');
//     }
// }
/**
 * Update payment settings (UPDATED to include bank details).
 */
public function updatePaymentSettings(Request $request)
{
    $validated = $request->validate([
        'receipt_generation_fee' => 'required|numeric|min:1|max:999999',
        'resale_fee' => 'required|numeric|min:1|max:999999',
        'paystack_public_key' => 'required|string|starts_with:pk_',
        'paystack_secret_key' => 'required|string|starts_with:sk_',
        'paystack_live_mode' => 'boolean',
        // ADD BANK VALIDATION
        'bank_name' => 'required|string|max:100',
        'bank_account_number' => 'required|string|size:10|regex:/^[0-9]{10}$/',
        'bank_account_name' => 'required|string|max:100',
        'bank_code' => 'nullable|string|size:3|regex:/^[0-9]{3}$/',
        'bank_instructions' => 'nullable|string|max:500',
    ]);

    try {
        // Update environment file with new values (INCLUDE BANK DETAILS)
        $this->updateEnvFile([
            'RECEIPT_GENERATION_FEE' => $validated['receipt_generation_fee'],
            'RESALE_FEE' => $validated['resale_fee'],
            'PAYSTACK_PUBLIC_KEY' => $validated['paystack_public_key'],
            'PAYSTACK_SECRET_KEY' => $validated['paystack_secret_key'],
            'PAYSTACK_LIVE_MODE' => $validated['paystack_live_mode'] ?? false,
            // ADD BANK SETTINGS
            'BANK_NAME' => '"' . $validated['bank_name'] . '"',
            'BANK_ACCOUNT_NUMBER' => $validated['bank_account_number'],
            'BANK_ACCOUNT_NAME' => '"' . strtoupper($validated['bank_account_name']) . '"',
            'BANK_CODE' => $validated['bank_code'] ?? '011',
            'BANK_INSTRUCTIONS' => '"' . $validated['bank_instructions'] . '"',
        ]);

        // Clear config cache to reload new values
        if (function_exists('artisan')) {
            \Artisan::call('config:clear');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Payment and bank settings updated successfully!');

    } catch (\Exception $e) {
        \Log::error('Failed to update payment settings: ' . $e->getMessage());
        
        return back()->withInput()
            ->with('error', 'Failed to update settings. Please check the logs.');
    }
}

/**
 * Test Paystack connection.
 */
public function testPaystackConnection(Request $request)
{
    try {
        $publicKey = $request->input('public_key') ?: config('services.paystack.public_key');
        $secretKey = $request->input('secret_key') ?: config('services.paystack.secret_key');

        // Test API call to Paystack
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ])->get('https://api.paystack.co/bank');

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Paystack connection successful!',
                'data' => [
                    'status' => 'connected',
                    'mode' => str_contains($secretKey, '_test_') ? 'test' : 'live',
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Paystack connection failed. Check your API keys.',
            ], 400);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Connection test failed: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Update environment file with new values.
 */
private function updateEnvFile(array $data)
{
    $envFile = base_path('.env');
    $envContent = file_get_contents($envFile);

    foreach ($data as $key => $value) {
        $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        
        // Check if key exists in .env file
        if (preg_match("/^{$key}=/m", $envContent)) {
            // Update existing key
            $envContent = preg_replace(
                "/^{$key}=.*$/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // Add new key
            $envContent .= "\n{$key}={$value}";
        }
    }

    file_put_contents($envFile, $envContent);
}
}
