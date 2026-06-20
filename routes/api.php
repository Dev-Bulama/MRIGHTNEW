<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AntiTheftController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/*
|--------------------------------------------------------------------------
| AntiTheft API Landing Page (Public Access)
|--------------------------------------------------------------------------
|
| This route provides information when someone visits the base API URL
| in a browser. No authentication required.
|
*/

// API Information Landing Page (No authentication required)
Route::get('/antitheft', function () {
    return response()->json([
        'success' => true,
        'message' => 'Welcome to M-Right Digital AntiTheft API',
        'api_info' => [
            'name' => 'M-Right Digital AntiTheft Integration API',
            'version' => '1.0.0',
            'status' => 'operational',
            'description' => 'API for communication between AntiTheft systems and Phone Anti-Theft Digital Receipt platform',
            'last_updated' => '2025-01-20',
        ],
        'authentication' => [
            'type' => 'Bearer Token',
            'header' => 'Authorization: Bearer YOUR_API_KEY',
            'alternative' => 'X-API-Key: YOUR_API_KEY',
            'note' => 'API key required for all endpoints except this information page',
        ],
        'available_endpoints' => [
            'health_check' => [
                'method' => 'GET',
                'path' => '/api/antitheft/health',
                'description' => 'Check API health and connectivity',
                'authentication' => 'required',
            ],
            'list_phones' => [
                'method' => 'GET', 
                'path' => '/api/antitheft/phones',
                'description' => 'List all phones with pagination and filters',
                'authentication' => 'required',
            ],
            'get_phone' => [
                'method' => 'GET',
                'path' => '/api/antitheft/phones/{serial}',
                'description' => 'Get specific phone details by serial number',
                'authentication' => 'required',
            ],
            'register_phone' => [
                'method' => 'POST',
                'path' => '/api/antitheft/phones',
                'description' => 'Register a new phone in the system',
                'authentication' => 'required',
            ],
            'update_phone' => [
                'method' => 'PUT',
                'path' => '/api/antitheft/phones/{serial}',
                'description' => 'Update existing phone information',
                'authentication' => 'required',
            ],
            'delete_phone' => [
                'method' => 'DELETE',
                'path' => '/api/antitheft/phones/{serial}',
                'description' => 'Remove phone record from system',
                'authentication' => 'required',
            ],
            'bulk_check' => [
                'method' => 'POST',
                'path' => '/api/antitheft/phones/bulk-check',
                'description' => 'Check multiple phones at once (up to 100)',
                'authentication' => 'required',
            ],
            'statistics' => [
                'method' => 'GET',
                'path' => '/api/antitheft/statistics',
                'description' => 'Get system statistics and analytics',
                'authentication' => 'required',
            ],
            'public_verify' => [
                'method' => 'POST',
                'path' => '/api/public/antitheft/verify-phone',
                'description' => 'Public phone verification (limited info)',
                'authentication' => 'not_required',
            ],
        ],
        'sample_requests' => [
            'health_check' => [
                'curl' => 'curl -X GET "' . url('/api/antitheft/health') . '" -H "Authorization: Bearer YOUR_API_KEY"',
                'description' => 'Test API connectivity',
            ],
            'register_phone' => [
                'curl' => 'curl -X POST "' . url('/api/antitheft/phones') . '" -H "Authorization: Bearer YOUR_API_KEY" -H "Content-Type: application/json" -d \'{"serial_number":"ABC123","phone_model":"iPhone 15","phone_brand":"Apple","phone_color":"Black"}\'',
                'description' => 'Register a new phone',
            ],
        ],
        'rate_limits' => [
            'authenticated_endpoints' => '1000 requests/hour, 200 requests/minute',
            'public_endpoints' => '100 requests/hour, 10 requests/minute',
            'bulk_operations' => '50 requests/hour, 5 requests/minute',
        ],
        'support' => [
            'email' => 'admin@mright.com.ng',
            'documentation' => 'Contact support for detailed integration guide',
            'api_status' => url('/api/antitheft/health'),
        ],
        'security' => [
            'https_only' => true,
            'api_key_required' => true,
            'rate_limiting' => true,
            'cors_enabled' => true,
        ],
        'integration_info' => [
            'target_systems' => 'AntiTheft systems and related platforms',
            'data_format' => 'JSON',
            'response_format' => 'Standardized success/error responses',
            'phone_statuses' => [
                'awaiting_ownership_verification',
                'in_use', 
                'reported_stolen',
                'recovered',
                'blacklisted',
                'inactive',
            ],
        ],
        'meta' => [
            'generated_at' => now()->toISOString(),
            'server_time' => now()->format('Y-m-d H:i:s T'),
            'api_base_url' => url('/api/antitheft'),
            'public_api_base_url' => url('/api/public/antitheft'),
            'environment' => app()->environment(),
        ],
    ], 200, [], JSON_PRETTY_PRINT);
})->name('api.antitheft.info');
/*
|--------------------------------------------------------------------------
| AntiTheft API Routes
|--------------------------------------------------------------------------
|
| These routes handle all AntiTheft system integration endpoints.
| They are protected by API key authentication and rate limiting.
|
*/

// AntiTheft API Routes with Authentication and Rate Limiting
Route::prefix('antitheft')->name('api.antitheft.')->middleware([
    // 'throttle:antitheft', // Custom rate limiting for antitheft endpoints
    ApiKeyMiddleware::class, // API key authentication
])->group(function () {
    
    // Health Check (no additional middleware)
    Route::get('/health', [AntiTheftController::class, 'health'])
          ->name('health');
    
    // Statistics and Analytics
    Route::get('/statistics', [AntiTheftController::class, 'statistics'])
          ->name('statistics');
    
    // Phone Management Routes
    Route::prefix('phones')->name('phones.')->group(function () {
        
        // List all phones with pagination and search
        Route::get('/', [AntiTheftController::class, 'index'])
              ->name('index');
        
        // Bulk operations
        Route::post('/bulk-check', [AntiTheftController::class, 'bulkCheck'])
              ->name('bulk-check');
        
        // Individual phone operations
        Route::get('/{serial}', [AntiTheftController::class, 'show'])
              ->name('show')
              ->where('serial', '[A-Za-z0-9\-_]+'); // Allow alphanumeric, hyphens, underscores
        
        Route::post('/', [AntiTheftController::class, 'store'])
              ->name('store');
        
        Route::put('/{serial}', [AntiTheftController::class, 'update'])
              ->name('update')
              ->where('serial', '[A-Za-z0-9\-_]+');
        
        Route::delete('/{serial}', [AntiTheftController::class, 'destroy'])
              ->name('destroy')
              ->where('serial', '[A-Za-z0-9\-_]+');
    });
});

/*
|--------------------------------------------------------------------------
| Public AntiTheft API Routes (No Authentication Required)
|--------------------------------------------------------------------------
|
| These routes are for public access and don't require API keys.
| They have stricter rate limiting.
|
*/

Route::prefix('public/antitheft')->name('api.public.antitheft.')->middleware([
    'throttle:public-antitheft', // Stricter rate limiting for public endpoints
])->group(function () {
    
    // Public phone verification (limited information)
    Route::post('/verify-phone', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $phone = \App\Models\AntiTheftPhone::where('serial_number', $request->serial_number)->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Phone verification completed',
                'data' => [
                    'serial_number' => $request->serial_number,
                    'found' => $phone ? true : false,
                    'is_stolen_or_blacklisted' => $phone ? $phone->isStolenOrBlacklisted() : false,
                    'status' => $phone ? $phone->status : 'not_found',
                    'verified_at' => now()->toISOString(),
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
                'error' => 'Internal server error',
            ], 500);
        }
    })->name('verify-phone');
    
    // Public API status
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'message' => 'AntiTheft API is operational',
            'data' => [
                'status' => 'operational',
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
                'endpoints' => [
                    'public_verification' => '/api/public/antitheft/verify-phone',
                    'authenticated_api' => '/api/antitheft/*',
                ],
                'documentation' => 'Contact system administrator for API access',
            ],
        ]);
    })->name('status');
});

/*
|--------------------------------------------------------------------------
| Legacy Sanctum-based Routes (Optional - for backward compatibility)
|--------------------------------------------------------------------------
|
| If you want to keep Sanctum authentication as an alternative,
| uncomment these routes. However, the API key routes above are recommended.
|
*/

/*
Route::middleware(['auth:sanctum'])->prefix('antitheft/sanctum')->group(function () {
    Route::get('/phones', [AntiTheftController::class, 'index']);
    Route::post('/phones', [AntiTheftController::class, 'store']);
    Route::get('/phones/{serial_number}', [AntiTheftController::class, 'show']);
    Route::put('/phones/{serial_number}', [AntiTheftController::class, 'update']);
    Route::delete('/phones/{serial_number}', [AntiTheftController::class, 'destroy']);
});
*/