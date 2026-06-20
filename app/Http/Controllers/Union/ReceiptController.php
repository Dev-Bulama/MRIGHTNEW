<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    /**
     * Display receipts for union's assigned areas
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

        $query = Receipt::whereIn('user_id', $shopOwnerIds)
                       ->with(['user', 'shop']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone_name', 'like', "%{$search}%");
            });
        }

        $receipts = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)->count(),
            'successful_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->where('payment_status', 'paid')->count(),
            'pending_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->where('payment_status', 'pending')->count(),
            'this_month_receipts' => Receipt::whereIn('user_id', $shopOwnerIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('union.receipts.index', compact('receipts', 'stats'));
    }
}