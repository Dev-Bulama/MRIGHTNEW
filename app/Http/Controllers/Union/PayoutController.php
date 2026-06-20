<?php

namespace App\Http\Controllers\Union;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayoutController extends Controller
{
    /**
     * Display payout requests for union's assigned areas
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

        $query = PayoutRequest::whereIn('user_id', $shopOwnerIds)
                             ->with(['user', 'user.shop']);

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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payouts = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total_requests' => PayoutRequest::whereIn('user_id', $shopOwnerIds)->count(),
            'pending_requests' => PayoutRequest::whereIn('user_id', $shopOwnerIds)
                ->where('status', 'pending')->count(),
            'approved_requests' => PayoutRequest::whereIn('user_id', $shopOwnerIds)
                ->where('status', 'approved')->count(),
            'total_amount_requested' => PayoutRequest::whereIn('user_id', $shopOwnerIds)
                ->sum('amount_requested'),
        ];

        return view('union.payouts.index', compact('payouts', 'stats'));
    }
}