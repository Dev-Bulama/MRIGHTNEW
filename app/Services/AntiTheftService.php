<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\AntiTheftReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class AntiTheftService
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;
    private $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.antitheft.api_url');
        $this->apiKey = config('services.antitheft.api_key');
        $this->apiSecret = config('services.antitheft.api_secret');
        $this->timeout = 30; // 30 seconds timeout
    }

    /**
     * Check if a phone is reported as stolen.
     */
    public function checkPhoneStatus(string $serialNumber, string $imei = null): array
    {
        try {
            // Check cache first to avoid repeated API calls
            $cacheKey = 'antitheft_check_' . md5($serialNumber . ($imei ?? ''));
            $cachedResult = Cache::get($cacheKey);
            
            if ($cachedResult !== null) {
                return $cachedResult;
            }

            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->post($this->apiUrl . '/api/v1/check-phone', [
                              'serial_number' => $serialNumber,
                              'imei' => $imei,
                              'source' => 'M-RIGHT',
                          ]);

            if ($response->successful()) {
                $data = $response->json();
                $result = [
                    'success' => true,
                    'is_stolen' => $data['is_stolen'] ?? false,
                    'report_date' => $data['report_date'] ?? null,
                    'report_location' => $data['report_location'] ?? null,
                    'report_reference' => $data['report_reference'] ?? null,
                    'status' => $data['status'] ?? 'clean',
                    'confidence_level' => $data['confidence_level'] ?? 'high',
                    'last_checked' => now()->toISOString(),
                ];

                // Cache for 1 hour to reduce API calls
                Cache::put($cacheKey, $result, now()->addHour());

                return $result;
            } else {
                throw new Exception('API request failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft check failed', [
                'serial_number' => $serialNumber,
                'imei' => $imei,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'is_stolen' => false, // Default to safe assumption
                'status' => 'unknown',
                'last_checked' => now()->toISOString(),
            ];
        }
    }

    /**
     * Report a phone as stolen.
     */
    public function reportStolenPhone(array $phoneData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->post($this->apiUrl . '/api/v1/report-stolen', [
                              'serial_number' => $phoneData['serial_number'],
                              'imei' => $phoneData['imei'] ?? null,
                              'phone_model' => $phoneData['phone_model'],
                              'phone_color' => $phoneData['phone_color'] ?? null,
                              'owner_name' => $phoneData['owner_name'],
                              'owner_phone' => $phoneData['owner_phone'],
                              'owner_email' => $phoneData['owner_email'] ?? null,
                              'incident_date' => $phoneData['incident_date'],
                              'incident_location' => $phoneData['incident_location'],
                              'police_report_number' => $phoneData['police_report_number'] ?? null,
                              'additional_details' => $phoneData['additional_details'] ?? null,
                              'source' => 'M-RIGHT',
                              'receipt_reference' => $phoneData['receipt_reference'] ?? null,
                          ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Clear any cached status for this phone
                $cacheKey = 'antitheft_check_' . md5($phoneData['serial_number'] . ($phoneData['imei'] ?? ''));
                Cache::forget($cacheKey);

                return [
                    'success' => true,
                    'report_id' => $data['report_id'],
                    'report_reference' => $data['report_reference'],
                    'status' => $data['status'] ?? 'reported',
                    'message' => $data['message'] ?? 'Phone reported successfully',
                ];
            } else {
                throw new Exception('Report submission failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft report failed', [
                'phone_data' => $phoneData,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update phone status (e.g., found, recovered).
     */
    public function updatePhoneStatus(string $reportReference, string $status, array $additionalData = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->put($this->apiUrl . '/api/v1/update-status', [
                              'report_reference' => $reportReference,
                              'status' => $status,
                              'additional_data' => $additionalData,
                              'updated_by' => 'M-RIGHT',
                          ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'],
                    'message' => $data['message'] ?? 'Status updated successfully',
                ];
            } else {
                throw new Exception('Status update failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft status update failed', [
                'report_reference' => $reportReference,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get phone theft statistics.
     */
    public function getTheftStatistics(array $filters = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->get($this->apiUrl . '/api/v1/statistics', $filters);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                throw new Exception('Statistics request failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft statistics failed', [
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Bulk check multiple phones.
     */
    public function bulkCheckPhones(array $phones): array
    {
        try {
            $response = Http::timeout(60) // Longer timeout for bulk operations
                          ->withHeaders($this->getHeaders())
                          ->post($this->apiUrl . '/api/v1/bulk-check', [
                              'phones' => $phones,
                              'source' => 'M-RIGHT',
                          ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'results' => $response->json()['results'] ?? [],
                ];
            } else {
                throw new Exception('Bulk check failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft bulk check failed', [
                'phone_count' => count($phones),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => [],
            ];
        }
    }

    /**
     * Register a phone for monitoring.
     */
    public function registerPhoneForMonitoring(Receipt $receipt): array
    {
        if (!$receipt->enable_antitheft) {
            return [
                'success' => false,
                'error' => 'Anti-theft protection not enabled for this receipt',
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->post($this->apiUrl . '/api/v1/register-phone', [
                              'serial_number' => $receipt->phone_serial_number,
                              'phone_model' => $receipt->phone_name,
                              'phone_color' => $receipt->phone_color,
                              'owner_name' => $receipt->customer_name,
                              'owner_phone' => $receipt->customer_phone,
                              'owner_email' => $receipt->customer_email,
                              'receipt_reference' => $receipt->receipt_number,
                              'shop_name' => $receipt->shop->shop_name ?? null,
                              'sale_date' => $receipt->created_at->toISOString(),
                              'monitoring_level' => 'standard',
                              'source' => 'M-RIGHT',
                          ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'monitoring_id' => $data['monitoring_id'],
                    'status' => $data['status'] ?? 'active',
                    'message' => $data['message'] ?? 'Phone registered for monitoring',
                ];
            } else {
                throw new Exception('Registration failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Anti-theft registration failed', [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate phone ownership using receipt.
     */
    public function validateOwnership(string $serialNumber, string $receiptNumber): array
    {
        try {
            $response = Http::timeout($this->timeout)
                          ->withHeaders($this->getHeaders())
                          ->post($this->apiUrl . '/api/v1/validate-ownership', [
                              'serial_number' => $serialNumber,
                              'receipt_reference' => $receiptNumber,
                              'source' => 'M-RIGHT',
                          ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'is_valid_owner' => $data['is_valid_owner'] ?? false,
                    'ownership_confidence' => $data['confidence_level'] ?? 'medium',
                    'last_valid_transaction' => $data['last_transaction'] ?? null,
                ];
            } else {
                throw new Exception('Ownership validation failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Ownership validation failed', [
                'serial_number' => $serialNumber,
                'receipt_number' => $receiptNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'is_valid_owner' => false,
            ];
        }
    }

    /**
     * Get API request headers.
     */
    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-API-Secret' => hash_hmac('sha256', time(), $this->apiSecret),
            'X-Timestamp' => time(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'M-RIGHT/1.0',
        ];
    }

    /**
     * Check if API is available.
     */
    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(10)
                          ->withHeaders($this->getHeaders())
                          ->get($this->apiUrl . '/api/v1/health');

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get API status and information.
     */
    public function getApiStatus(): array
    {
        try {
            $response = Http::timeout(10)
                          ->withHeaders($this->getHeaders())
                          ->get($this->apiUrl . '/api/v1/status');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'available' => true,
                    'version' => $data['version'] ?? '1.0',
                    'status' => $data['status'] ?? 'operational',
                    'message' => $data['message'] ?? 'API is operational',
                    'features' => $data['features'] ?? [],
                ];
            } else {
                return [
                    'available' => false,
                    'error' => 'API not responding',
                ];
            }
        } catch (Exception $e) {
            return [
                'available' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}