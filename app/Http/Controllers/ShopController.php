<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\NigeriaData;



class ShopController extends Controller
{
   /**
 * Display shop dashboard or registration form.
 */
public function index()
{
    $user = Auth::user();
    $shop = $user->shop;

    if ($shop) {
        // User has a shop - redirect to dashboard
        return redirect()->route('dashboard');
    }

    // Check if user was pre-approved (shouldn't reach here if pre-approved)
    $wasPreApproved = \App\Models\PreApprovedUser::where('email', $user->email)
                                                   ->where('status', 'used')
                                                   ->exists();
    
    if ($wasPreApproved) {
        // Pre-approved user somehow doesn't have shop - create it now
        return $this->createShopForPreApprovedUser($user);
    }

    // Regular user - show registration form
    return redirect()->route('shop.create');
}

    /**
     * Show shop registration form.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a shop
        if ($user->shop) {
            return redirect()->route('dashboard')
                ->with('info', 'You already have a registered shop.');
        }

        return view('shop.create');
    }
    /**
 * Store new shop registration.
 */
public function store(Request $request)
{
    $user = Auth::user();

    // Check if user already has a shop
    if ($user->shop) {
        return redirect()->route('dashboard')
            ->with('info', 'You already have a registered shop.');
    }

    // Check if user is pre-approved for auto-approval
    $preApproved = \App\Models\PreApprovedUser::where('email', $user->email)
                                               ->where('status', 'pending')
                                               ->first();

    $validated = $request->validate([
        'shop_name' => ['required', 'string', 'max:255'],
        'owner_full_name' => ['required', 'string', 'max:255'],
        'business_address' => ['required', 'string', 'max:500'],
        'business_phone_1' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
        'business_phone_2' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
        'business_email' => ['required', 'email', 'max:255'],
        'country' => ['required', 'string', 'max:100'],
        'state' => ['required', 'string', 'max:100'],
        'local_government' => ['required', 'string', 'max:100'],
        'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'terms_and_conditions' => ['nullable', 'string', 'max:1000'],
    ]);

    // Handle logo upload
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('shop-logos', 'public');
        $validated['logo'] = $logoPath;
    }

    // Auto-approve if pre-approved
    if ($preApproved) {
        $adminUser = User::where('user_type', User::TYPE_ADMIN)->first();
        $validated['approved'] = true;
        $validated['approved_at'] = now();
        $validated['approved_by'] = $adminUser ? $adminUser->id : null;
        $validated['status'] = 'active';
        $validated['registration_source'] = 'pre_approved_manual';
        
        // Mark pre-approval as used
        $preApproved->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by_user_id' => $user->id
        ]);
    }

    // Create shop
    $shop = $user->shop()->create($validated);

    $message = $preApproved 
        ? 'Shop registered and approved automatically! You can now start generating receipts.'
        : 'Shop registered successfully! Your application is pending approval.';

    return redirect()->route('dashboard')->with('success', $message);
}

    // /**
    //  * Store new shop registration.
    //  */
    // public function store(Request $request)
    // {
    //     $user = Auth::user();

    //     // Check if user already has a shop
    //     if ($user->shop) {
    //         return redirect()->route('dashboard')
    //             ->with('error', 'You already have a registered shop.');
    //     }

    //     $validated = $request->validate([
    //         'shop_name' => ['required', 'string', 'max:255'],
    //         'owner_full_name' => ['required', 'string', 'max:255'],
    //         'business_address' => ['required', 'string', 'max:500'],
    //         'business_phone_1' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
    //         'business_phone_2' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
    //         'business_email' => ['required', 'email', 'max:255'],
    //         'country' => ['required', 'string', 'max:100'],
    //         'state' => ['required', 'string', 'max:100'],
    //         'local_government' => ['required', 'string', 'max:100'],
    //         'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    //         'terms_and_conditions' => ['nullable', 'string', 'max:1000'],
    //     ]);

    //     // Handle logo upload
    //     if ($request->hasFile('logo')) {
    //         $logoPath = $request->file('logo')->store('shop-logos', 'public');
    //         $validated['logo'] = $logoPath;
    //     }

    //     // Create shop
    //     $shop = $user->shop()->create($validated);

    //     return redirect()->route('dashboard')
    //         ->with('success', 'Shop registered successfully! Your application is pending approval.');
    // }

    /**
     * Display shop details.
     */
    public function show(Shop $shop)
    {
        // Check if user owns this shop or is admin
        if (Auth::user()->id !== $shop->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $stats = [
            'total_receipts' => $shop->receipts()->count(),
            'monthly_receipts' => $shop->currentMonthReceipts()->count(),
            'yearly_receipts' => $shop->currentYearReceipts()->count(),
            'recent_receipts' => $shop->receipts()->latest()->take(5)->get(),
            'total_earnings' => $shop->total_commission_earned,
        ];

        return view('shop.show', compact('shop', 'stats'));
    }

    /**
     * Show edit form for shop.
     */
public function edit(Shop $shop)
{
    return view('shop.edit', [
        'shop' => $shop,
        'states' => NigeriaData::states()
    ]);
}
    // public function edit(Shop $shop)
    // {
    //     // Check if user owns this shop
    //     if (Auth::user()->id !== $shop->user_id) {
    //         return redirect()->route('dashboard')
    //             ->with('error', 'Unauthorized access.');
    //     }
        
    //     return view('shop.edit', compact('shop'));
    // }

    /**
     * Update shop information.
     */
      /**
     * Update shop details.
     */
    public function update(Request $request, Shop $shop)
    {
        // Check if user owns this shop
        if (Auth::user()->id !== $shop->user_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'owner_full_name' => ['required', 'string', 'max:255'],
            'business_address' => ['required', 'string', 'max:500'],
            'business_phone_1' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
            'business_phone_2' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
            'business_email' => ['required', 'email', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'local_government' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'terms_and_conditions' => ['nullable', 'string', 'max:1000'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($shop->logo) {
                Storage::disk('public')->delete($shop->logo);
            }
            
            $logoPath = $request->file('logo')->store('shop-logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Update shop
        $shop->update($validated);

        return redirect()->route('shop.show', $shop)
            ->with('success', 'Shop details updated successfully!');
    }
    // public function update(Request $request, Shop $shop)
    // {
    //     // Check if user owns this shop
    //     if (Auth::user()->id !== $shop->user_id) {
    //         return redirect()->route('dashboard')
    //             ->with('error', 'Unauthorized access.');
    //     }

    //     $validated = $request->validate([
    //         'shop_name' => ['required', 'string', 'max:255'],
    //         'owner_full_name' => ['required', 'string', 'max:255'],
    //         'business_address' => ['required', 'string', 'max:500'],
    //         'business_phone_1' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
    //         'business_phone_2' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]+$/', 'max:20'],
    //         'business_email' => ['required', 'email', 'max:255'],
    //         'country' => ['required', 'string', 'max:100'],
    //         'state' => ['required', 'string', 'max:100'],
    //         'local_government' => ['required', 'string', 'max:100'],
    //         'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    //         'terms_and_conditions' => ['nullable', 'string', 'max:1000'],
    //     ]);

    //     // Handle logo upload
    //     if ($request->hasFile('logo')) {
    //         // Delete old logo if exists
    //         if ($shop->logo && Storage::disk('public')->exists($shop->logo)) {
    //             Storage::disk('public')->delete($shop->logo);
    //         }
            
    //         $logoPath = $request->file('logo')->store('shop-logos', 'public');
    //         $validated['logo'] = $logoPath;
    //     }

    //     $shop->update($validated);

    //     return redirect()->route('dashboard')
    //         ->with('success', 'Shop information updated successfully!');
    // }

    /**
     * Get Nigerian states and LGAs for AJAX.
     */
    public function getStatesAndLgas()
    {
        $states = [
            'Lagos' => ['Alimosho', 'Amuwo-Odofin', 'Apapa', 'Badagry', 'Epe', 'Eti-Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye', 'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland', 'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere'],
            'Kano' => ['Ajingi', 'Albasu', 'Bagwai', 'Bebeji', 'Bichi', 'Bunkure', 'Dala', 'Dambatta', 'Dawakin Kudu', 'Dawakin Tofa', 'Doguwa', 'Fagge', 'Gabasawa', 'Garko', 'Garun Mallam', 'Gaya', 'Gezawa', 'Gwale', 'Gwarzo', 'Kabo', 'Kano Municipal', 'Karaye', 'Kibiya', 'Kiru', 'Kumbotso', 'Kunchi', 'Kura', 'Madobi', 'Makoda', 'Minjibir', 'Nasarawa', 'Rano', 'Rimin Gado', 'Rogo', 'Shanono', 'Sumaila', 'Takai', 'Tarauni', 'Tofa', 'Tsanyawa', 'Tudun Wada', 'Ungogo', 'Warawa', 'Wudil'],
            'Abuja' => ['Abaji', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali', 'Municipal Area Council'],
            'Rivers' => ['Port Harcourt', 'Obio-Akpor', 'Okrika', 'Ogu–Bolo', 'Eleme', 'Tai', 'Gokana', 'Khana', 'Oyigbo', 'Opobo–Nkoro', 'Andoni', 'Bonny', 'Degema', 'Asari-Toru', 'Akuku-Toru', 'Abua–Odual', 'Ahoada West', 'Ahoada East', 'Ogba–Egbema–Ndoni', 'Emohua', 'Ikwerre', 'Etche', 'Omuma'],
            'Oyo' => ['Ibadan North', 'Ibadan North-East', 'Ibadan North-West', 'Ibadan South-East', 'Ibadan South-West', 'Ibarapa Central', 'Ibarapa East', 'Ibarapa North', 'Ido', 'Irepo', 'Iseyin', 'Itesiwaju', 'Iwajowa', 'Kajola', 'Lagelu', 'Ogbomosho North', 'Ogbomosho South', 'Ogo Oluwa', 'Olorunsogo', 'Oluyole', 'Ona Ara', 'Orelope', 'Ori Ire', 'Oyo East', 'Oyo West', 'Saki East', 'Saki West', 'Surulere'],
        ];

        return response()->json($states);
    }

    /**
     * Get shop statistics for dashboard.
     */
    public function getStats()
    {
        $user = Auth::user();
        $shop = $user->shop;

        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        $stats = [
            'total_receipts' => $shop->receipts()->count(),
            'monthly_receipts' => $shop->currentMonthReceipts()->count(),
            'yearly_receipts' => $shop->currentYearReceipts()->count(),
            'pending_receipts' => $shop->receipts()->where('payment_status', 'pending')->count(),
            'total_earnings' => $shop->total_commission_earned,
            'commission_this_month' => $shop->currentMonthReceipts()->count() * 50, // ₦50 per receipt
        ];

        return response()->json($stats);
    }

    /**
     * Download receipt template preview.
     */
    public function downloadReceiptTemplate()
    {
        $user = Auth::user();
        $shop = $user->shop;
        
        if (!$shop) {
            return redirect()->route('shop.create')
                ->with('error', 'Please register your shop first.');
        }

        // Generate sample receipt preview
        $sampleData = [
            'shop' => $shop,
            'receipt_number' => 'SAMPLE-' . date('YmdHis'),
            'customer_name' => 'John Doe',
            'customer_phone' => '08012345678',
            'phone_name' => 'iPhone 13 Pro',
            'phone_color' => 'Gold',
            'amount' => 250000,
            'date' => now()->format('d/m/Y'),
        ];

        return view('receipt.template', $sampleData);
    }
  /**
     * Show shop payments overview.
     */
    /**
     * Show shop payments overview.
     */
    public function payments()
    {
        $user = Auth::user();
        
        // Check if user is a shop owner
        if (!$user->isShopOwner()) {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. Shop owner privileges required.');
        }

        // Get user's shop with proper loading
        $shop = $user->shop()->first();
        
        // Check if user has a shop
        if (!$shop) {
            return redirect()->route('shop.create')
                ->with('info', 'Please create a shop first to view payments.');
        }

        // Get shop receipts and payment data
        $receipts = Receipt::where('user_id', $user->id)
                          ->with('shop')
                          ->latest()
                          ->get();
        
        // Calculate statistics
        $stats = [
            'total_receipts' => $receipts->count(),
            'total_revenue' => $receipts->where('payment_gateway_status', 'successful')->sum('amount'),
            'pending_payments' => $receipts->where('payment_gateway_status', 'pending')->count(),
            'failed_payments' => $receipts->where('payment_gateway_status', 'failed')->count(),
            'successful_payments' => $receipts->where('payment_gateway_status', 'successful')->count(),
            'this_month_revenue' => $receipts->where('payment_gateway_status', 'successful')
                                            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                                            ->sum('amount'),
            'this_week_receipts' => $receipts->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'average_receipt_value' => $receipts->where('payment_gateway_status', 'successful')->avg('amount') ?? 0,
        ];

        // Get recent transactions
        $recentTransactions = $receipts->take(10);

        // Get monthly data for chart
        $monthlyData = $receipts
            ->where('payment_gateway_status', 'successful')
            ->groupBy(function($receipt) {
                return $receipt->created_at->format('Y-m');
            })
            ->map(function($monthReceipts) {
                return [
                    'month' => $monthReceipts->first()->created_at->format('M Y'),
                    'revenue' => $monthReceipts->sum('amount'),
                    'count' => $monthReceipts->count()
                ];
            })
            ->take(6)
            ->reverse()
            ->values();

        return view('shop.payments.index', [
            'shop' => $shop,
            'receipts' => $receipts,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'monthlyData' => $monthlyData
        ]);
    }
    
    /**
 * Create shop for pre-approved user who somehow missed auto-creation.
 */
private function createShopForPreApprovedUser($user)
{
    $preApproved = \App\Models\PreApprovedUser::where('email', $user->email)->first();
    $adminUser = User::where('user_type', User::TYPE_ADMIN)->first();
    
    $shop = $user->shop()->create([
        'shop_name' => $preApproved->shop_name ?? ($user->name . "'s Shop"),
        'owner_full_name' => $user->name,
        'business_address' => $preApproved->business_address ?? 'Address to be updated',
        'business_phone_1' => $preApproved->business_phone ?? $user->phone_number,
        'business_phone_2' => $user->secondary_phone,
        'business_email' => $user->email,
        'country' => 'Nigeria',
        'state' => $preApproved->state ?? 'Lagos',
        'local_government' => $preApproved->local_government ?? 'Lagos Island',
        'approved' => true,  // ← AUTO-APPROVE
        'approved_at' => now(),
        'approved_by' => $adminUser ? $adminUser->id : null,
        'status' => 'active',
        'registration_source' => 'pre_approved_recovery'
    ]);

    return redirect()->route('dashboard')
        ->with('success', 'Your pre-approved shop has been activated! You can now generate receipts.');
}
}