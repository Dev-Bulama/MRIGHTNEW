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
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
            $query = AntiTheftPhone::query()->with('currentReceipt');

            // Search by serial number
            if ($request->filled('serial_number')) {
                $query->where('serial_number', 'like', '%' . $request->serial_number . '%');
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by phone brand
            if ($request->filled('phone_brand')) {
                $query->where('phone_brand', 'like', '%' . $request->phone_brand . '%');
            }

            // Filter by phone model
            if ($request->filled('phone_model')) {
                $query->where('phone_model', 'like', '%' . $request->phone_model . '%');
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('registered_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('registered_at', '<=', $request->date_to);
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'registered_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            
            $allowedSortFields = ['serial_number', 'phone_brand', 'phone_model', 'status', 'registered_at', 'last_verified_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection);
            }

            $phones = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Phones retrieved successfully',
                'data' => AntiTheftPhoneResource::collection($phones->items()),
                'pagination' => [
                    'current_page' => $phones->currentPage(),
                    'last_page' => $phones->lastPage(),
                    'per_page' => $phones->perPage(),
                    'total' => $phones->total(),
                    'from' => $phones->firstItem(),
                    'to' => $phones->lastItem(),
                ],
                'filters_applied' => $request->only(['serial_number', 'status', 'phone_brand', 'phone_model', 'date_from', 'date_to']),
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
    public function show(string $serial): JsonResponse
{
    try {
        $phone = AntiTheftPhone::with([
                    'currentReceipt.shop.user', 
                    'currentReceipt.user'
                ])
                ->where('serial_number', $serial)
                ->first();

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone not found',
                'error' => 'No phone found with the provided serial number',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Phone retrieved successfully',
            'data' => new AntiTheftPhoneResource($phone),
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