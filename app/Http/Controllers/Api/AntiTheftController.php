<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AntiTheftPhone;
use App\Models\Receipt;
use App\Http\Resources\AntiTheftPhoneResource;
use App\Services\AntiTheftService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AntiTheftController extends Controller
{
    protected $antiTheftService;

    public function __construct(AntiTheftService $antiTheftService)
    {
        $this->antiTheftService = $antiTheftService;
    }

    /**
     * API Health check endpoint
     * GET /api/antitheft/health
     */
    public function health(): JsonResponse
    {
        try {
            $healthData = [
                'status' => 'operational',
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
                'database' => [
                    'connected' => true,
                    'total_phones' => AntiTheftPhone::count(),
                ],
                'external_services' => [
                    'antitheft_api' => $this->antiTheftService->isApiAvailable(),
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'API is healthy and operational',
                'data' => $healthData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('AntiTheft API: Health check failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API health check failed',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * List all phones with pagination and search
     * GET /api/antitheft/phones
     */
    /**
 * List all phones with pagination and search
 * GET /api/antitheft/phones
 */
/**
 * List all phones with pagination and search
 * GET /api/antitheft/phones
 */
public function index(Request $request): JsonResponse
{
    try {
        $perPage = min($request->get('per_page', 15), 100);
        
        // Query ALL receipts (removed antitheft restriction)
        $query = Receipt::with(['shop', 'user']);

        // Search by serial number
        if ($request->filled('serial_number')) {
            $query->where('phone_serial_number', 'like', '%' . $request->serial_number . '%');
        }

        // Filter by phone name (brand/model)
        if ($request->filled('phone_brand')) {
            $query->where('phone_name', 'like', '%' . $request->phone_brand . '%');
        }

        // Filter by customer name
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by antitheft status (optional)
        if ($request->filled('antitheft_enabled')) {
            $query->where('enable_antitheft', $request->boolean('antitheft_enabled'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['phone_serial_number', 'customer_name', 'amount', 'created_at', 'payment_status'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $receipts = $query->paginate($perPage);

        // Transform receipts to phone format
        $phoneData = $receipts->getCollection()->map(function ($receipt) {
            return [
                'serial_number' => $receipt->phone_serial_number,
                'phone_name' => $receipt->phone_name,
                'phone_brand' => $this->extractBrand($receipt->phone_name),
                'phone_color' => $receipt->phone_color,
                'customer_name' => $receipt->customer_name,
                'customer_phone' => $receipt->customer_phone,
                'shop_name' => $receipt->shop?->shop_name,
                'amount' => $receipt->amount,
                'payment_status' => $receipt->payment_status,
                'antitheft_enabled' => (bool) $receipt->enable_antitheft,
                'created_at' => $receipt->created_at?->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Phones retrieved successfully',
            'data' => $phoneData,
            'pagination' => [
                'current_page' => $receipts->currentPage(),
                'last_page' => $receipts->lastPage(),
                'per_page' => $receipts->perPage(),
                'total' => $receipts->total(),
                'from' => $receipts->firstItem(),
                'to' => $receipts->lastItem(),
            ],
            'filters_applied' => $request->only(['serial_number', 'phone_brand', 'customer_name', 'payment_status', 'antitheft_enabled', 'date_from', 'date_to']),
        ], 200);

    } catch (\Exception $e) {
        Log::error('AntiTheft API: Failed to retrieve phones list', [
            'error' => $e->getMessage(),
            'request_data' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve phones',
            'error' => 'Internal server error',
            'debug' => app()->environment('local') ? $e->getMessage() : null,
        ], 500);
    }
}
    /**
     * Get a specific phone by serial number
     * GET /api/antitheft/phones/{serial}
     */
    
    /**
 * Get a specific phone by serial number
 * GET /api/antitheft/phones/{serial}
 */
/**
 * Get a specific phone by serial number
 * GET /api/antitheft/phones/{serial}
 */
public function show(string $serial): JsonResponse
{
    try {
        // Query receipts table using phone_serial_number (NO antitheft restriction)
        $receipt = Receipt::with(['shop', 'user'])
                          ->where('phone_serial_number', $serial)
                          ->first();

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not found',
                'error' => 'No phone found with the provided serial number',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Phone retrieved successfully',
            'data' => [
                'id' => $receipt->id,
                'serial_number' => $receipt->phone_serial_number,
                
                // Phone Information (using user-entered data)
                'phone_details' => [
                    'brand' => $this->extractBrand($receipt->phone_name),
                    'model' => $this->extractModel($receipt->phone_name),
                    'color' => $receipt->phone_color,
                    'full_name' => $receipt->phone_name, // Exactly as user entered
                ],
                
                // Status Information  
                'status' => [
                    'code' => $receipt->status,
                    'display_name' => ucwords(str_replace('_', ' ', $receipt->status)),
                    'payment_status' => $receipt->payment_status,
                    'receipt_type' => $receipt->receipt_type,
                    'antitheft_enabled' => (bool) $receipt->enable_antitheft, // Show true/false
                ],
                
                // Current Owner Information
                'current_owner' => [
                    'name' => $receipt->customer_name,
                    'phone' => $receipt->customer_phone,
                    'email' => $receipt->customer_email,
                    'address' => $receipt->customer_address,
                    'sex' => $receipt->customer_sex,
                    'receipt_id' => $receipt->id,
                    'receipt_number' => $receipt->receipt_number,
                ],
                
                // Shop Information
                'shop_details' => [
                    'id' => $receipt->shop?->id,
                    'name' => $receipt->shop?->shop_name,
                    'location' => $receipt->shop?->shop_location,
                    'owner_name' => $receipt->shop?->owner_name,
                    'owner_phone' => $receipt->shop?->owner_phone,
                    'status' => $receipt->shop?->status,
                ],
                
                // Shop Owner Information
                'shop_owner' => [
                    'id' => $receipt->user?->id,
                    'name' => $receipt->user?->first_name . ' ' . $receipt->user?->last_name,
                    'email' => $receipt->user?->email,
                    'phone' => $receipt->user?->phone,
                    'user_type' => $receipt->user?->user_type,
                ],
                
                // Purchase Information
                'purchase_details' => [
                    'amount' => $receipt->amount,
                    'formatted_amount' => '₦' . number_format($receipt->amount, 2),
                    'amount_in_words' => $receipt->amount_in_words,
                    'payment_status' => $receipt->payment_status,
                    'receipt_type' => $receipt->receipt_type,
                    'resale_code' => $receipt->resale_code,
                ],
                
                // Timestamps
                'dates' => [
                    'purchased_at' => $receipt->created_at?->toISOString(),
                    'generated_at' => $receipt->generated_at?->toISOString(),
                    'payment_confirmed_at' => $receipt->payment_confirmed_at?->toISOString(),
                    'updated_at' => $receipt->updated_at?->toISOString(),
                ],
                
                // Additional Information
                'notes' => $receipt->notes,
                'metadata' => $receipt->metadata,
                
                // API Metadata
                'api_info' => [
                    'resource_type' => 'phone_from_receipt',
                    'api_version' => '1.0',
                    'data_source' => 'receipts_table',
                    'antitheft_enabled' => (bool) $receipt->enable_antitheft,
                    'query_timestamp' => now()->toISOString(),
                ],
            ],
        ], 200);

    } catch (\Exception $e) {
        Log::error('AntiTheft API: Failed to retrieve phone details', [
            'serial_number' => $serial,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve phone details',
            'error' => 'Internal server error',
            'debug' => app()->environment('local') ? $e->getMessage() : null,
        ], 500);
    }
}

/**
 * Intelligently extract brand from user-entered phone name
 */
private function extractBrand(string $phoneName): string
{
    if (empty($phoneName)) {
        return 'Unknown';
    }

    // Common brand patterns (case-insensitive)
    $brandPatterns = [
        // Apple variations
        '/\b(iphone|apple)\b/i' => 'Apple',
        
        // Samsung variations
        '/\b(samsung|galaxy)\b/i' => 'Samsung',
        
        // Other brands - look for these words anywhere in the name
        '/\b(techno)\b/i' => 'Techno',
        '/\b(infinix)\b/i' => 'Infinix',
        '/\b(redmi)\b/i' => 'Redmi',
        '/\b(xiaomi)\b/i' => 'Xiaomi',
        '/\b(oppo)\b/i' => 'Oppo',
        '/\b(vivo)\b/i' => 'Vivo',
        '/\b(huawei)\b/i' => 'Huawei',
        '/\b(honor)\b/i' => 'Honor',
        '/\b(oneplus|one\s*plus)\b/i' => 'OnePlus',
        '/\b(nokia)\b/i' => 'Nokia',
        '/\b(motorola|moto)\b/i' => 'Motorola',
        '/\b(google|pixel)\b/i' => 'Google',
        '/\b(sony)\b/i' => 'Sony',
        '/\b(lg)\b/i' => 'LG',
        '/\b(htc)\b/i' => 'HTC',
        '/\b(itel)\b/i' => 'Itel',
        '/\b(gionee)\b/i' => 'Gionee',
    ];
    
    // Check each pattern
    foreach ($brandPatterns as $pattern => $brand) {
        if (preg_match($pattern, $phoneName)) {
            return $brand;
        }
    }
    
    // If no pattern matches, use the first word as brand
    $words = preg_split('/\s+/', trim($phoneName));
    return ucfirst(strtolower($words[0])) ?? 'Unknown';
}

/**
 * Extract model from user-entered phone name
 */
private function extractModel(string $phoneName): string
{
    if (empty($phoneName)) {
        return 'Unknown Model';
    }
    
    $brand = $this->extractBrand($phoneName);
    
    // Remove brand from phone name to get model
    $model = preg_replace('/\b' . preg_quote($brand, '/') . '\b/i', '', $phoneName);
    $model = trim($model);
    
    // If model is empty after brand removal, return the original name
    if (empty($model)) {
        return $phoneName;
    }
    
    return $model;
}
    // public function show(string $serial): JsonResponse
    // {
    //     try {
    //         $phone = AntiTheftPhone::with('currentReceipt')
    //                                ->where('serial_number', $serial)
    //                                ->first();

    //         if (!$phone) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Phone not found',
    //                 'error' => 'No phone found with the provided serial number',
    //             ], 404);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Phone retrieved successfully',
    //             'data' => new AntiTheftPhoneResource($phone),
    //         ], 200);

    //     } catch (\Exception $e) {
    //         Log::error('AntiTheft API: Failed to retrieve phone details', [
    //             'serial_number' => $serial,
    //             'error' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve phone details',
    //             'error' => 'Internal server error',
    //             'debug' => app()->environment('local') ? $e->getMessage() : null,
    //         ], 500);
    //     }
    // }

    /**
     * Create/Register a new phone
     * POST /api/antitheft/phones
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|string|max:255|unique:anti_theft_phones,serial_number',
            'phone_model' => 'required|string|max:255',
            'phone_brand' => 'required|string|max:255',
            'phone_color' => 'required|string|max:255',
            'current_owner_name' => 'nullable|string|max:255',
            'current_owner_phone' => 'nullable|string|max:20',
            'external_api_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:' . implode(',', array_keys(AntiTheftPhone::getStatuses())),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $phoneData = $validator->validated();
            $phoneData['status'] = $phoneData['status'] ?? AntiTheftPhone::STATUS_AWAITING_VERIFICATION;
            $phoneData['registered_at'] = now();
            $phoneData['last_api_sync'] = now();
            $phoneData['api_response_data'] = [
                'source' => 'antitheft_system',
                'registered_via_api' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            $phone = AntiTheftPhone::create($phoneData);

            // Update status history
            $phone->updateStatus(
                $phone->status,
                'Phone registered via AntiTheft API',
                'antitheft_system'
            );

            DB::commit();

            Log::info('AntiTheft API: Phone registered successfully', [
                'serial_number' => $phone->serial_number,
                'phone_id' => $phone->id,
                'external_api_id' => $phone->external_api_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone registered successfully',
                'data' => new AntiTheftPhoneResource($phone),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('AntiTheft API: Failed to register phone', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register phone',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update phone information
     * PUT /api/antitheft/phones/{serial}
     */
    public function update(Request $request, string $serial): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_model' => 'sometimes|string|max:255',
            'phone_brand' => 'sometimes|string|max:255',
            'phone_color' => 'sometimes|string|max:255',
            'current_owner_name' => 'nullable|string|max:255',
            'current_owner_phone' => 'nullable|string|max:20',
            'external_api_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|string|in:' . implode(',', array_keys(AntiTheftPhone::getStatuses())),
            'update_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $phone = AntiTheftPhone::where('serial_number', $serial)->first();

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone not found',
                    'error' => 'No phone found with the provided serial number',
                ], 404);
            }

            DB::beginTransaction();

            $updateData = $validator->validated();
            $oldStatus = $phone->status;
            $newStatus = $updateData['status'] ?? $phone->status;
            $updateReason = $updateData['update_reason'] ?? 'Updated via AntiTheft API';

            // Remove update_reason from the update data
            unset($updateData['update_reason']);

            // Update API sync information
            $updateData['last_api_sync'] = now();
            $updateData['api_response_data'] = array_merge(
                $phone->api_response_data ?? [],
                [
                    'last_update_source' => 'antitheft_system',
                    'last_update_ip' => $request->ip(),
                    'last_update_timestamp' => now()->toISOString(),
                ]
            );

            $phone->update($updateData);

            // Update status history if status changed
            if ($oldStatus !== $newStatus) {
                $phone->updateStatus($newStatus, $updateReason, 'antitheft_system');
            }

            DB::commit();

            Log::info('AntiTheft API: Phone updated successfully', [
                'serial_number' => $phone->serial_number,
                'phone_id' => $phone->id,
                'updated_fields' => array_keys($updateData),
                'status_changed' => $oldStatus !== $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone updated successfully',
                'data' => new AntiTheftPhoneResource($phone->fresh()),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('AntiTheft API: Failed to update phone', [
                'serial_number' => $serial,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update phone',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Delete/Remove a phone record
     * DELETE /api/antitheft/phones/{serial}
     */
    public function destroy(string $serial): JsonResponse
    {
        try {
            $phone = AntiTheftPhone::where('serial_number', $serial)->first();

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone not found',
                    'error' => 'No phone found with the provided serial number',
                ], 404);
            }

            DB::beginTransaction();

            // Store phone data for logging
            $phoneData = $phone->toArray();

            $phone->delete();

            DB::commit();

            Log::info('AntiTheft API: Phone deleted successfully', [
                'serial_number' => $serial,
                'phone_data' => $phoneData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone deleted successfully',
                'data' => [
                    'serial_number' => $serial,
                    'deleted_at' => now()->toISOString(),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('AntiTheft API: Failed to delete phone', [
                'serial_number' => $serial,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete phone',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Bulk operations - Check multiple phones
     * POST /api/antitheft/phones/bulk-check
     */
    public function bulkCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serial_numbers' => 'required|array|min:1|max:100',
            'serial_numbers.*' => 'required|string|max:255',
            'include_details' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $serialNumbers = $validator->validated()['serial_numbers'];
            $includeDetails = $request->get('include_details', false);

            $phones = AntiTheftPhone::whereIn('serial_number', $serialNumbers)
                                   ->when($includeDetails, function ($query) {
                                       return $query->with('currentReceipt');
                                   })
                                   ->get()
                                   ->keyBy('serial_number');

            $results = [];
            foreach ($serialNumbers as $serial) {
                if (isset($phones[$serial])) {
                    $phone = $phones[$serial];
                    $results[] = [
                        'serial_number' => $serial,
                        'found' => true,
                        'status' => $phone->status,
                        'is_stolen_or_blacklisted' => $phone->isStolenOrBlacklisted(),
                        'is_available_for_ownership' => $phone->isAvailableForOwnership(),
                        'phone_info' => [
                            'brand' => $phone->phone_brand,
                            'model' => $phone->phone_model,
                            'color' => $phone->phone_color,
                        ],
                        'registration_date' => $phone->registered_at?->toISOString(),
                        'last_verified' => $phone->last_verified_at?->toISOString(),
                        'details' => $includeDetails ? new AntiTheftPhoneResource($phone) : null,
                    ];
                } else {
                    $results[] = [
                        'serial_number' => $serial,
                        'found' => false,
                        'status' => null,
                        'is_stolen_or_blacklisted' => false,
                        'is_available_for_ownership' => true,
                        'phone_info' => null,
                        'registration_date' => null,
                        'last_verified' => null,
                        'details' => null,
                    ];
                }
            }

            Log::info('AntiTheft API: Bulk check completed', [
                'total_requested' => count($serialNumbers),
                'found_count' => $phones->count(),
                'include_details' => $includeDetails,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bulk check completed successfully',
                'data' => [
                    'total_requested' => count($serialNumbers),
                    'found_count' => $phones->count(),
                    'not_found_count' => count($serialNumbers) - $phones->count(),
                    'results' => $results,
                ],
                'checked_at' => now()->toISOString(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('AntiTheft API: Bulk check failed', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk check failed',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get statistics and summary information
     * GET /api/antitheft/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
            $dateTo = $request->get('date_to', now()->toDateString());

            $stats = [
                'total_phones' => AntiTheftPhone::count(),
                'by_status' => AntiTheftPhone::groupBy('status')
                                           ->selectRaw('status, count(*) as count')
                                           ->pluck('count', 'status')
                                           ->toArray(),
                'date_range_stats' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'registered_in_period' => AntiTheftPhone::whereBetween('registered_at', [$dateFrom, $dateTo])->count(),
                    'verified_in_period' => AntiTheftPhone::whereBetween('last_verified_at', [$dateFrom, $dateTo])->count(),
                ],
                'top_brands' => AntiTheftPhone::groupBy('phone_brand')
                                            ->selectRaw('phone_brand, count(*) as count')
                                            ->orderByDesc('count')
                                            ->limit(10)
                                            ->pluck('count', 'phone_brand')
                                            ->toArray(),
                'recent_activity' => [
                    'last_24_hours' => AntiTheftPhone::where('registered_at', '>=', now()->subDay())->count(),
                    'last_7_days' => AntiTheftPhone::where('registered_at', '>=', now()->subWeek())->count(),
                    'last_30_days' => AntiTheftPhone::where('registered_at', '>=', now()->subMonth())->count(),
                ],
                'api_health' => [
                    'last_sync_phones' => AntiTheftPhone::whereNotNull('last_api_sync')
                                                       ->where('last_api_sync', '>=', now()->subHour())
                                                       ->count(),
                    'phones_needing_sync' => AntiTheftPhone::needsApiSync()->count(),
                    'external_api_status' => $this->antiTheftService->isApiAvailable(),
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats,
                'generated_at' => now()->toISOString(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('AntiTheft API: Failed to retrieve statistics', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => 'Internal server error',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}