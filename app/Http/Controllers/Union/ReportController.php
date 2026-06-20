<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Shop;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports for union's assigned areas
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isUnion()) {
            abort(403, 'Access denied. Union account required.');
        }

        // Get manageable shop owners
        $manageableShopOwners = $user->manageableShopOwners();
        $shopOwnerIds = $manageableShopOwners->pluck('id');

        // Performance data
        $performanceData = $this->getPerformanceData($shopOwnerIds);
        
        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends($shopOwnerIds);
        
        // Top performers
        $topPerformers = $this->getTopPerformers($manageableShopOwners);

        return view('union.reports.index', compact(
            'performanceData', 
            'monthlyTrends', 
            'topPerformers'
        ));
    }

    private function getPerformanceData($shopOwnerIds)
    {
        return [
            'total_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)->count(),
            'successful_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->where('payment_status', 'paid')->count(),
            'total_revenue' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->where('payment_status', 'paid')->sum('amount'),
            'this_month_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->whereMonth('created_at', now()->month)->count(),
        ];
    }

    private function getMonthlyTrends($shopOwnerIds)
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $receipts = Receipt::whereIn('user_id', $shopOwnerIds)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'receipts' => $receipts,
            ];
        }
        return $trends;
    }

    private function getTopPerformers($manageableShopOwners)
    {
        return $manageableShopOwners
            ->with(['shop', 'receipts' => function($query) {
                $query->where('payment_status', 'paid')
                      ->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->map(function($user) {
                return [
                    'user' => $user,
                    'monthly_receipts' => $user->receipts->count(),
                    'monthly_revenue' => $user->receipts->sum('amount'),
                ];
            })
            ->sortByDesc('monthly_revenue')
            ->take(5);
    }
}