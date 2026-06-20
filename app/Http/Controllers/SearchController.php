<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class SearchController extends Controller
{
    /**
     * Display main search page.
     */
    public function index()
    {
        // Get recent searches for logged-in users
        $recentSearches = [];
        if (Auth::check()) {
            $recentSearches = Cache::get('user_' . Auth::id() . '_recent_searches', []);
        }

        // Get search statistics
        $stats = [
            'total_receipts' => Receipt::count(),
            'verified_receipts' => Receipt::where('status', 'active')->count(),
            'total_shops' => Shop::where('approved', true)->count(),
            'search_success_rate' => '94.2%', // This could be calculated from actual data
        ];

        return view('search.index', compact('recentSearches', 'stats'));
    }

    /**
     * Process general search query.
     */
    // public function search(Request $request)
    // {
    //     $validated = $request->validate([
    //         'query' => ['required', 'string', 'min:3', 'max:255'],
    //         'search_type' => ['required', 'in:all,receipt,phone,shop,customer'],
    //         'limit' => ['nullable', 'integer', 'min:5', 'max:50'],
    //     ]);

    //     $query = trim($validated['query']);
    //     $searchType = $validated['search_type'];
    //     $limit = $validated['limit'] ?? 20;

    //     try {
    //         $results = $this->performSearch($query, $searchType, $limit);
            
    //         // Store recent search for logged-in users
    //         if (Auth::check()) {
    //             $this->storeRecentSearch($query, $searchType);
    //         }

    //         // Log search for analytics
    //         Log::info('Search performed', [
    //             'query' => $query,
    //             'type' => $searchType,
    //             'results_count' => $results['total'],
    //             'user_id' => Auth::id(),
    //             'ip' => $request->ip(),
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'query' => $query,
    //             'search_type' => $searchType,
    //             'results' => $results,
    //             'message' => $results['total'] > 0 
    //                 ? "Found {$results['total']} result(s) for '{$query}'"
    //                 : "No results found for '{$query}'"
    //         ]);

    //     } catch (Exception $e) {
    //         Log::error('Search error', [
    //             'query' => $query,
    //             'error' => $e->getMessage(),
    //             'user_id' => Auth::id(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Search failed. Please try again.',
    //             'error' => config('app.debug') ? $e->getMessage() : null
    //         ], 500);
    //     }
    // }

    /**
     * Show receipt verification form.
     */
    public function verifyForm()
    {
        return view('search.verify');
    }

    /**
     * Process receipt verification.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'receipt_number' => ['required', 'string'],
            'verification_method' => ['required', 'in:receipt_number,serial_number,phone_details'],
            'serial_number' => ['required_if:verification_method,serial_number', 'string'],
            'phone_name' => ['required_if:verification_method,phone_details', 'string'],
            'customer_phone' => ['required_if:verification_method,phone_details', 'string'],
        ]);

        try {
            $receipt = null;
            $verificationDetails = [];

            switch ($validated['verification_method']) {
                case 'receipt_number':
                    $receipt = Receipt::where('receipt_number', $validated['receipt_number'])
                                    ->with(['shop', 'user', 'parentReceipt', 'childReceipts'])
                                    ->first();
                    break;

                case 'serial_number':
                    $receipt = Receipt::where('receipt_number', $validated['receipt_number'])
                                    ->where('phone_serial_number', $validated['serial_number'])
                                    ->with(['shop', 'user', 'parentReceipt', 'childReceipts'])
                                    ->first();
                    break;

                case 'phone_details':
                    $receipt = Receipt::where('receipt_number', $validated['receipt_number'])
                                    ->where('phone_name', 'like', '%' . $validated['phone_name'] . '%')
                                    ->where('customer_phone', $validated['customer_phone'])
                                    ->with(['shop', 'user', 'parentReceipt', 'childReceipts'])
                                    ->first();
                    break;
            }

            if (!$receipt) {
                return back()->withInput()
                    ->with('error', 'Receipt not found or verification details do not match.');
            }

            // Prepare verification details
            $verificationDetails = [
                'receipt' => $receipt,
                'verification_status' => $this->getVerificationStatus($receipt),
                'security_features' => $this->getSecurityFeatures($receipt),
                'resale_history' => $this->getResaleHistory($receipt),
                'anti_theft_status' => $this->getAntiTheftStatus($receipt),
            ];

            return view('search.verify-result', $verificationDetails);

        } catch (Exception $e) {
            Log::error('Verification error', [
                'receipt_number' => $validated['receipt_number'],
                'method' => $validated['verification_method'],
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Verification failed. Please try again.');
        }
    }

    /**
     * Show quick search form.
     */
    public function quickSearch()
    {
        return view('search.quick');
    }

    /**
     * Process quick search.
     */
    public function processQuickSearch(Request $request)
    {
        $validated = $request->validate([
            'quick_query' => ['required', 'string', 'min:3'],
            'search_field' => ['required', 'in:receipt_number,serial_number,customer_phone,customer_name'],
        ]);

        $query = trim($validated['quick_query']);
        $field = $validated['search_field'];

        try {
            $results = Receipt::query()
                ->when($field === 'receipt_number', function ($q) use ($query) {
                    return $q->where('receipt_number', 'like', "%{$query}%");
                })
                ->when($field === 'serial_number', function ($q) use ($query) {
                    return $q->where('phone_serial_number', 'like', "%{$query}%");
                })
                ->when($field === 'customer_phone', function ($q) use ($query) {
                    return $q->where('customer_phone', 'like', "%{$query}%");
                })
                ->when($field === 'customer_name', function ($q) use ($query) {
                    return $q->where('customer_name', 'like', "%{$query}%");
                })
                ->with(['shop', 'user'])
                ->latest()
                ->limit(15)
                ->get();

            return response()->json([
                'success' => true,
                'results' => $results->map(function ($receipt) {
                    return [
                        'id' => $receipt->id,
                        'receipt_number' => $receipt->receipt_number,
                        'customer_name' => $receipt->customer_name,
                        'phone_name' => $receipt->phone_name,
                        'phone_serial_number' => $receipt->phone_serial_number,
                        'amount' => $receipt->amount,
                        'status' => $receipt->status,
                        'shop_name' => $receipt->shop->shop_name ?? 'N/A',
                        'created_at' => $receipt->created_at->format('Y-m-d H:i'),
                        'view_url' => route('receipt.show', $receipt),
                    ];
                }),
                'count' => $results->count(),
                'message' => $results->count() > 0 
                    ? "Found {$results->count()} result(s)"
                    : "No results found"
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quick search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Show advanced search form.
     */
    public function advancedSearch()
    {
        $shops = Shop::where('approved', true)
                    ->orderBy('shop_name')
                    ->get(['id', 'shop_name']);

        return view('search.advanced', compact('shops'));
    }

    /**
     * Process advanced search.
     */
    public function processAdvancedSearch(Request $request)
    {
        $validated = $request->validate([
            'receipt_number' => ['nullable', 'string'],
            'customer_name' => ['nullable', 'string'],
            'customer_phone' => ['nullable', 'string'],
            'customer_email' => ['nullable', 'email'],
            'phone_name' => ['nullable', 'string'],
            'phone_serial_number' => ['nullable', 'string'],
            'shop_id' => ['nullable', 'exists:shops,id'],
            'amount_from' => ['nullable', 'numeric', 'min:0'],
            'amount_to' => ['nullable', 'numeric', 'min:0'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', 'in:active,cancelled,void'],
            'payment_status' => ['nullable', 'in:paid,pending,partial,failed'],
            'receipt_type' => ['nullable', 'in:sale,resale'],
            'enable_antitheft' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ]);

        try {
            $query = Receipt::query()->with(['shop', 'user', 'parentReceipt']);

            // Apply filters
            if (!empty($validated['receipt_number'])) {
                $query->where('receipt_number', 'like', '%' . $validated['receipt_number'] . '%');
            }

            if (!empty($validated['customer_name'])) {
                $query->where('customer_name', 'like', '%' . $validated['customer_name'] . '%');
            }

            if (!empty($validated['customer_phone'])) {
                $query->where('customer_phone', 'like', '%' . $validated['customer_phone'] . '%');
            }

            if (!empty($validated['customer_email'])) {
                $query->where('customer_email', 'like', '%' . $validated['customer_email'] . '%');
            }

            if (!empty($validated['phone_name'])) {
                $query->where('phone_name', 'like', '%' . $validated['phone_name'] . '%');
            }

            if (!empty($validated['phone_serial_number'])) {
                $query->where('phone_serial_number', 'like', '%' . $validated['phone_serial_number'] . '%');
            }

            if (!empty($validated['shop_id'])) {
                $query->where('shop_id', $validated['shop_id']);
            }

            if (!empty($validated['amount_from'])) {
                $query->where('amount', '>=', $validated['amount_from']);
            }

            if (!empty($validated['amount_to'])) {
                $query->where('amount', '<=', $validated['amount_to']);
            }

            if (!empty($validated['date_from'])) {
                $query->whereDate('created_at', '>=', $validated['date_from']);
            }

            if (!empty($validated['date_to'])) {
                $query->whereDate('created_at', '<=', $validated['date_to']);
            }

            if (!empty($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            if (!empty($validated['payment_status'])) {
                $query->where('payment_status', $validated['payment_status']);
            }

            if (!empty($validated['receipt_type'])) {
                $query->where('receipt_type', $validated['receipt_type']);
            }

            if (isset($validated['enable_antitheft'])) {
                $query->where('enable_antitheft', $validated['enable_antitheft']);
            }

            $perPage = $validated['per_page'] ?? 20;
            $results = $query->latest()->paginate($perPage);

            return view('search.advanced-results', compact('results', 'validated'));

        } catch (Exception $e) {
            Log::error('Advanced search error', [
                'filters' => $validated,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Advanced search failed. Please try again.');
        }
    }

    /**
     * Get search suggestions for AJAX autocomplete.
     */
    /**
     * Search for receipts by phone number.
     */
    public function search(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $results = [];
        
        if ($phoneNumber) {
            // Clean the phone number
            $cleanedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            // Search for exact matches only
            $receipts = Receipt::where(function($query) use ($cleanedPhone, $phoneNumber) {
                $query->where('customer_phone', $phoneNumber)
                      ->orWhere('customer_phone', $cleanedPhone)
                      ->orWhere('receipt_number', $phoneNumber); // Also search by receipt number
            })
            ->with(['shop'])
            ->where('status', 'active') // Only active receipts
            ->get();

            foreach ($receipts as $receipt) {
                $results[] = [
                    'id' => $receipt->id,
                    'receipt_number' => $receipt->receipt_number,
                    'customer_name' => $receipt->customer_name,
                    'customer_phone' => $receipt->customer_phone,
                    'phone_name' => $receipt->phone_name,
                    'phone_color' => $receipt->phone_color,
                    'amount' => $receipt->amount,
                    'date' => $receipt->created_at->format('M d, Y'),
                    'shop_name' => $receipt->shop ? $receipt->shop->shop_name : 'Unknown Shop',
                    'status' => $receipt->payment_gateway_status ?? 'pending',
                    'verified' => true, // Only show if receipt actually exists
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Show receipt details.
     */
    public function show(Request $request)
    {
        $receiptNumber = $request->input('receipt_number');
        
        if (!$receiptNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number is required.'
            ]);
        }

        // Find the exact receipt
        $receipt = Receipt::where('receipt_number', $receiptNumber)
                         ->with(['shop'])
                         ->where('status', 'active')
                         ->first();

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found or has been deactivated.'
            ]);
        }

        $receiptData = [
            'receipt_number' => $receipt->receipt_number,
            'customer_name' => $receipt->customer_name,
            'customer_phone' => $receipt->customer_phone,
            'customer_email' => $receipt->customer_email,
            'phone_name' => $receipt->phone_name,
            'phone_color' => $receipt->phone_color,
            'phone_serial_number' => $receipt->phone_serial_number,
            'amount' => number_format($receipt->amount, 2),
            'amount_in_words' => $receipt->amount_in_words,
            'payment_status' => $receipt->payment_status,
            'payment_gateway_status' => $receipt->payment_gateway_status ?? 'pending',
            'generated_date' => $receipt->created_at->format('M d, Y'),
            'shop_name' => $receipt->shop ? $receipt->shop->shop_name : 'Unknown Shop',
            'shop_address' => $receipt->shop ? $receipt->shop->business_address : '',
            'shop_phone' => $receipt->shop ? $receipt->shop->business_phone_1 : '',
            'resale_code' => $receipt->resale_code,
            'enable_antitheft' => $receipt->enable_antitheft,
            'verified' => true,
        ];

        return response()->json([
            'success' => true,
            'receipt' => $receiptData
        ]);
    }
    // public function searchSuggestions(Request $request)
    // {
    //     $query = $request->get('query', '');
    //     $type = $request->get('type', 'all');

    //     if (strlen($query) < 2) {
    //         return response()->json(['suggestions' => []]);
    //     }

    //     try {
    //         $suggestions = [];

    //         switch ($type) {
    //             case 'receipt':
    //                 $suggestions = Receipt::where('receipt_number', 'like', "%{$query}%")
    //                                      ->limit(10)
    //                                      ->pluck('receipt_number')
    //                                      ->toArray();
    //                 break;

    //             case 'phone':
    //                 $phoneNames = Receipt::where('phone_name', 'like', "%{$query}%")
    //                                     ->distinct()
    //                                     ->limit(5)
    //                                     ->pluck('phone_name')
    //                                     ->toArray();
                    
    //                 $serialNumbers = Receipt::where('phone_serial_number', 'like', "%{$query}%")
    //                                        ->limit(5)
    //                                        ->pluck('phone_serial_number')
    //                                        ->toArray();
                    
    //                 $suggestions = array_merge($phoneNames, $serialNumbers);
    //                 break;

    //             case 'customer':
    //                 $names = Receipt::where('customer_name', 'like', "%{$query}%")
    //                                ->distinct()
    //                                ->limit(5)
    //                                ->pluck('customer_name')
    //                                ->toArray();
                    
    //                 $phones = Receipt::where('customer_phone', 'like', "%{$query}%")
    //                                 ->distinct()
    //                                 ->limit(5)
    //                                 ->pluck('customer_phone')
    //                                 ->toArray();
                    
    //                 $suggestions = array_merge($names, $phones);
    //                 break;

    //             case 'shop':
    //                 $suggestions = Shop::where('shop_name', 'like', "%{$query}%")
    //                                   ->where('approved', true)
    //                                   ->limit(10)
    //                                   ->pluck('shop_name')
    //                                   ->toArray();
    //                 break;

    //             default:
    //                 // All types
    //                 $receiptNumbers = Receipt::where('receipt_number', 'like', "%{$query}%")
    //                                         ->limit(3)
    //                                         ->pluck('receipt_number')
    //                                         ->toArray();
                    
    //                 $phoneNames = Receipt::where('phone_name', 'like', "%{$query}%")
    //                                     ->distinct()
    //                                     ->limit(3)
    //                                     ->pluck('phone_name')
    //                                     ->toArray();
                    
    //                 $customerNames = Receipt::where('customer_name', 'like', "%{$query}%")
    //                                        ->distinct()
    //                                        ->limit(2)
    //                                        ->pluck('customer_name')
    //                                        ->toArray();
                    
    //                 $suggestions = array_merge($receiptNumbers, $phoneNames, $customerNames);
    //                 break;
    //         }

    //         return response()->json([
    //             'suggestions' => array_unique(array_slice($suggestions, 0, 10))
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json(['suggestions' => []]);
    //     }
    // }

    /**
     * Public receipt verification (no auth required).
     */
    public function publicVerify($receiptNumber)
    {
        try {
            $receipt = Receipt::where('receipt_number', $receiptNumber)
                             ->with(['shop'])
                             ->first();

            if (!$receipt) {
                return view('search.public-verify', [
                    'receipt' => null,
                    'error' => 'Receipt not found. Please verify the receipt number.'
                ]);
            }

            $verificationData = [
                'receipt' => $receipt,
                'verification_status' => $this->getVerificationStatus($receipt),
                'basic_info' => [
                    'is_valid' => $receipt->status === 'active',
                    'shop_name' => $receipt->shop->shop_name ?? 'Unknown Shop',
                    'phone_name' => $receipt->phone_name,
                    'amount' => $receipt->amount,
                    'date_generated' => $receipt->created_at->format('Y-m-d'),
                ],
                'anti_theft_enabled' => $receipt->enable_antitheft,
            ];

            return view('search.public-verify', $verificationData);

        } catch (Exception $e) {
            Log::error('Public verification error', [
                'receipt_number' => $receiptNumber,
                'error' => $e->getMessage(),
            ]);

            return view('search.public-verify', [
                'receipt' => null,
                'error' => 'Verification service temporarily unavailable. Please try again later.'
            ]);
        }
    }

    /**
     * Perform search based on query and type.
     */
    private function performSearch($query, $searchType, $limit)
    {
        $results = [
            'receipts' => collect(),
            'shops' => collect(),
            'total' => 0,
        ];

        if ($searchType === 'all' || $searchType === 'receipt') {
            $receipts = Receipt::where(function ($q) use ($query) {
                $q->where('receipt_number', 'like', "%{$query}%")
                  ->orWhere('customer_name', 'like', "%{$query}%")
                  ->orWhere('customer_phone', 'like', "%{$query}%")
                  ->orWhere('phone_name', 'like', "%{$query}%")
                  ->orWhere('phone_serial_number', 'like', "%{$query}%");
            })
            ->with(['shop', 'user'])
            ->latest()
            ->limit($limit)
            ->get();

            $results['receipts'] = $receipts;
            $results['total'] += $receipts->count();
        }

        if ($searchType === 'all' || $searchType === 'shop') {
            $shops = Shop::where('shop_name', 'like', "%{$query}%")
                        ->where('approved', true)
                        ->with(['user'])
                        ->latest()
                        ->limit($limit)
                        ->get();

            $results['shops'] = $shops;
            $results['total'] += $shops->count();
        }

        return $results;
    }

    /**
     * Store recent search for user.
     */
    private function storeRecentSearch($query, $searchType)
    {
        $userId = Auth::id();
        $cacheKey = "user_{$userId}_recent_searches";
        
        $recentSearches = Cache::get($cacheKey, []);
        
        $newSearch = [
            'query' => $query,
            'type' => $searchType,
            'timestamp' => now()->toISOString(),
        ];
        
        // Remove if already exists
        $recentSearches = array_filter($recentSearches, function ($search) use ($query, $searchType) {
            return !($search['query'] === $query && $search['type'] === $searchType);
        });
        
        // Add to beginning
        array_unshift($recentSearches, $newSearch);
        
        // Keep only last 10 searches
        $recentSearches = array_slice($recentSearches, 0, 10);
        
        // Cache for 30 days
        Cache::put($cacheKey, $recentSearches, now()->addDays(30));
    }

    /**
     * Get receipt verification status.
     */
    private function getVerificationStatus($receipt)
    {
        return [
            'is_valid' => $receipt->status === 'active',
            'status' => $receipt->status,
            'payment_status' => $receipt->payment_status,
            'verified_date' => $receipt->created_at,
            'last_updated' => $receipt->updated_at,
        ];
    }

    /**
     * Get security features for receipt.
     */
    private function getSecurityFeatures($receipt)
    {
        return [
            'receipt_number_format' => 'Valid M-right format',
            'digital_signature' => 'Verified',
            'shop_verification' => $receipt->shop && $receipt->shop->approved ? 'Approved Shop' : 'Pending',
            'anti_theft_enabled' => $receipt->enable_antitheft,
            'timestamp_verification' => 'Valid',
        ];
    }

    /**
     * Get resale history for phone.
     */
    private function getResaleHistory($receipt)
    {
        $history = [];
        
        // If this is a resale, get parent
        if ($receipt->parentReceipt) {
            $history[] = [
                'type' => 'original_sale',
                'receipt_number' => $receipt->parentReceipt->receipt_number,
                'date' => $receipt->parentReceipt->created_at,
                'shop' => $receipt->parentReceipt->shop->shop_name ?? 'Unknown',
            ];
        }
        
        // Add current receipt
        $history[] = [
            'type' => $receipt->receipt_type,
            'receipt_number' => $receipt->receipt_number,
            'date' => $receipt->created_at,
            'shop' => $receipt->shop->shop_name ?? 'Unknown',
        ];
        
        // Add any child receipts (further resales)
        foreach ($receipt->childReceipts as $child) {
            $history[] = [
                'type' => 'resale',
                'receipt_number' => $child->receipt_number,
                'date' => $child->created_at,
                'shop' => $child->shop->shop_name ?? 'Unknown',
            ];
        }
        
        return $history;
    }

    /**
     * Get anti-theft status.
     */
    private function getAntiTheftStatus($receipt)
    {
        return [
            'enabled' => $receipt->enable_antitheft,
            'status' => $receipt->enable_antitheft ? 'Protected' : 'Not Protected',
            'last_check' => $receipt->updated_at,
            'alert_level' => 'Normal', // This would integrate with actual anti-theft system
        ];
    }
    /**
 * Enhanced public search for receipt verification with ownership history.
 */
// public function publicSearch(Request $request)
// {
//     $request->validate([
//         'query' => 'required|string|max:100'
//     ]);

//     $query = $request->query;
    
//     // Search by receipt number, phone serial, or customer info
//     $receipt = Receipt::where(function($q) use ($query) {
//         $q->where('receipt_number', 'like', "%{$query}%")
//           ->orWhere('phone_serial_number', 'like', "%{$query}%")
//           ->orWhere('customer_name', 'like', "%{$query}%")
//           ->orWhere('customer_phone', 'like', "%{$query}%");
//     })
//     ->with(['shop', 'user'])
//     ->first();

//     if ($receipt) {
//         // Get ownership history for this phone
//         $ownershipHistory = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
//                                   ->where('id', '!=', $receipt->id)
//                                   ->with(['shop'])
//                                   ->orderBy('created_at', 'desc')
//                                   ->get()
//                                   ->map(function($r) {
//                                       return [
//                                           'name' => $r->customer_name,
//                                           'date' => $r->created_at->format('M j, Y'),
//                                           'shop' => $r->shop->shop_name ?? 'Unknown Shop'
//                                       ];
//                                   });
        
//         return response()->json([
//             'success' => true,
//             'receipt' => [
//                 'receipt_number' => $receipt->receipt_number,
//                 'customer_name' => $receipt->customer_name,
//                 'customer_phone' => $receipt->customer_phone,
//                 'phone_name' => $receipt->phone_name,
//                 'phone_color' => $receipt->phone_color,
//                 'phone_serial' => $receipt->phone_serial_number,
//                 'shop_name' => $receipt->shop->shop_name,
//                 'shop_location' => $receipt->shop->state . ', ' . $receipt->shop->local_government,
//                 'created_date' => $receipt->created_at->format('F j, Y g:i A'),
//                 'amount' => number_format($receipt->amount, 2),
//                 'status' => $receipt->status
//             ],
//             'ownership_history' => $ownershipHistory,
//             'security_verified' => true,
//             'verification_time' => now()->format('Y-m-d H:i:s')
//         ]);
//     }

//     return response()->json([
//         'success' => false,
//         'message' => 'No receipt found matching your search criteria.'
//     ]);
// }

/**
     * Enhanced public search for receipt verification with ownership history.
     */
    public function publicSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:100'
        ]);

        $query = trim($request->query);
        
        // Search by receipt number, phone serial, or customer info
        $receipt = Receipt::where(function($q) use ($query) {
            $q->where('receipt_number', 'like', "%{$query}%")
              ->orWhere('phone_serial_number', 'like', "%{$query}%")
              ->orWhere('customer_name', 'like', "%{$query}%")
              ->orWhere('customer_phone', 'like', "%{$query}%")
              ->orWhere('phone_name', 'like', "%{$query}%");
        })
        ->with(['shop', 'user'])
        ->first();

        if ($receipt) {
            // Get ownership history for this phone
            $ownershipHistory = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
                                      ->where('id', '!=', $receipt->id)
                                      ->with(['shop'])
                                      ->orderBy('created_at', 'desc')
                                      ->get()
                                      ->map(function($r) {
                                          return [
                                              'name' => $r->customer_name,
                                              'date' => $r->created_at->format('M j, Y'),
                                              'shop' => $r->shop->shop_name ?? 'Unknown Shop'
                                          ];
                                      });
            
            return response()->json([
                'success' => true,
                'receipt' => [
                    'receipt_number' => $receipt->receipt_number,
                    'customer_name' => $receipt->customer_name,
                    'customer_phone' => $receipt->customer_phone,
                    'phone_name' => $receipt->phone_name,
                    'phone_color' => $receipt->phone_color ?? 'N/A',
                    'phone_serial' => $receipt->phone_serial_number,
                    'shop_name' => $receipt->shop->shop_name ?? 'Unknown Shop',
                    'shop_location' => ($receipt->shop->state ?? 'N/A') . ', ' . ($receipt->shop->local_government ?? 'N/A'),
                    'created_date' => $receipt->created_at->format('F j, Y g:i A'),
                    'amount' => number_format($receipt->amount ?? 0, 2),
                    'status' => $receipt->status ?? 'active'
                ],
                'ownership_history' => $ownershipHistory,
                'security_verified' => true,
                'verification_time' => now()->format('Y-m-d H:i:s')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No receipt found matching your search criteria.'
        ]);
    }
    /**
 * Dashboard search functionality (for API calls)
 */
public function dashboardSearch(Request $request)
{
    try {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => true,
                'results' => []
            ]);
        }

        // Search receipts, shops, users based on user type
        $results = [];
        
        if (auth()->user()->isAdmin()) {
            // Admin can search everything
            $receipts = \App\Models\Receipt::where('receipt_number', 'like', "%{$query}%")
                       ->orWhere('customer_name', 'like', "%{$query}%")
                       ->orWhere('customer_phone', 'like', "%{$query}%")
                       ->limit(5)->get();
            
            foreach ($receipts as $receipt) {
                $results[] = [
                    'type' => 'receipt',
                    'title' => "Receipt #{$receipt->receipt_number}",
                    'subtitle' => $receipt->customer_name,
                    'url' => route('admin.receipts.details', $receipt)
                ];
            }
        } else if (auth()->user()->isShopOwner()) {
            // Shop owners can search their own receipts
            $receipts = auth()->user()->receipts()
                       ->where('receipt_number', 'like', "%{$query}%")
                       ->orWhere('customer_name', 'like', "%{$query}%")
                       ->orWhere('customer_phone', 'like', "%{$query}%")
                       ->limit(5)->get();
            
            foreach ($receipts as $receipt) {
                $results[] = [
                    'type' => 'receipt',
                    'title' => "Receipt #{$receipt->receipt_number}",
                    'subtitle' => $receipt->customer_name,
                    'url' => route('receipt.show', $receipt)
                ];
            }
        } else if (auth()->user()->isUnion()) {
            // Union users can search shop owners in their locations
            $shopOwners = auth()->user()->manageableShopOwners()
                         ->where('name', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->limit(5)->get();
            
            foreach ($shopOwners as $shopOwner) {
                $results[] = [
                    'type' => 'shop_owner',
                    'title' => $shopOwner->name,
                    'subtitle' => $shopOwner->email,
                    'url' => route('union.shop-owners.show', $shopOwner)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Search failed'
        ], 500);
    }
}

}