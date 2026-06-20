<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class ValidationService
{
    private $antiTheftService;
    
    public function __construct(AntiTheftService $antiTheftService)
    {
        $this->antiTheftService = $antiTheftService;
    }

    /**
     * Comprehensive receipt validation.
     */
    public function validateReceipt(Receipt $receipt): array
    {
        $validationResults = [
            'is_valid' => true,
            'score' => 100,
            'issues' => [],
            'warnings' => [],
            'security_checks' => [],
        ];

        // Basic receipt validation
        $basicValidation = $this->validateBasicReceiptData($receipt);
        $validationResults = array_merge_recursive($validationResults, $basicValidation);

        // Serial number validation
        $serialValidation = $this->validateSerialNumber($receipt);
        $validationResults = array_merge_recursive($validationResults, $serialValidation);

        // Shop validation
        $shopValidation = $this->validateShop($receipt->shop);
        $validationResults = array_merge_recursive($validationResults, $shopValidation);

        // Anti-theft validation
        if ($receipt->enable_antitheft) {
            $antiTheftValidation = $this->validateAntiTheftStatus($receipt);
            $validationResults = array_merge_recursive($validationResults, $antiTheftValidation);
        }

        // Payment validation
        if ($receipt->payments()->exists()) {
            $paymentValidation = $this->validatePayments($receipt);
            $validationResults = array_merge_recursive($validationResults, $paymentValidation);
        }

        // Resale chain validation
        if ($receipt->receipt_type === 'resale') {
            $resaleValidation = $this->validateResaleChain($receipt);
            $validationResults = array_merge_recursive($validationResults, $resaleValidation);
        }

        // Calculate final validation score
        $validationResults['score'] = $this->calculateValidationScore($validationResults);
        $validationResults['is_valid'] = $validationResults['score'] >= 70 && empty($validationResults['issues']);

        return $validationResults;
    }

    /**
     * Validate basic receipt data.
     */
    private function validateBasicReceiptData(Receipt $receipt): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        // Required fields validation
        $requiredFields = [
            'receipt_number' => 'Receipt number is required',
            'customer_name' => 'Customer name is required',
            'customer_phone' => 'Customer phone is required',
            'phone_name' => 'Phone model is required',
            'phone_serial_number' => 'Phone serial number is required',
            'amount' => 'Receipt amount is required',
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($receipt->$field)) {
                $issues[] = $message;
            }
        }

        // Receipt number format validation
        if ($receipt->receipt_number && !$this->isValidReceiptNumberFormat($receipt->receipt_number)) {
            $issues[] = 'Invalid receipt number format';
        }

        // Amount validation
        if ($receipt->amount && ($receipt->amount <= 0 || $receipt->amount > 50000000)) {
            $issues[] = 'Receipt amount is out of valid range';
        }

        // Date validation
        if ($receipt->created_at->isFuture()) {
            $issues[] = 'Receipt date cannot be in the future';
        }

        if ($receipt->created_at->diffInYears() > 10) {
            $warnings[] = 'Receipt is older than 10 years';
        }

        // Phone number validation
        if ($receipt->customer_phone && !$this->isValidPhoneNumber($receipt->customer_phone)) {
            $warnings[] = 'Customer phone number format may be invalid';
        }

        // Email validation
        if ($receipt->customer_email && !filter_var($receipt->customer_email, FILTER_VALIDATE_EMAIL)) {
            $warnings[] = 'Customer email format is invalid';
        }

        // Serial number format validation
        if ($receipt->phone_serial_number && !$this->isValidSerialNumber($receipt->phone_serial_number)) {
            $warnings[] = 'Phone serial number format may be non-standard';
        }

        $securityChecks[] = [
            'check' => 'Basic data integrity',
            'status' => empty($issues) ? 'passed' : 'failed',
            'details' => empty($issues) ? 'All required fields are present and valid' : 'Some required fields are missing or invalid',
        ];

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Validate serial number uniqueness and format.
     */
    private function validateSerialNumber(Receipt $receipt): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        // Check for duplicate serial numbers
        $duplicateCount = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
                                ->where('id', '!=', $receipt->id)
                                ->where('status', 'active')
                                ->count();

        if ($duplicateCount > 0) {
            // Check if it's a legitimate resale
            $isLegitimateResale = Receipt::where('phone_serial_number', $receipt->phone_serial_number)
                                        ->where('id', '!=', $receipt->id)
                                        ->whereHas('childReceipts', function($query) use ($receipt) {
                                            $query->where('id', $receipt->id);
                                        })
                                        ->exists();

            if (!$isLegitimateResale) {
                $issues[] = 'Phone serial number already exists in system without proper resale documentation';
            }
        }

        // Serial number format validation
        $serialFormat = $this->analyzeSerialNumberFormat($receipt->phone_serial_number);
        
        if ($serialFormat['confidence'] < 0.7) {
            $warnings[] = 'Serial number format appears non-standard for ' . $receipt->phone_name;
        }

        // Check serial number length
        $serialLength = strlen($receipt->phone_serial_number);
        if ($serialLength < 8 || $serialLength > 25) {
            $warnings[] = 'Serial number length is unusual';
        }

        $securityChecks[] = [
            'check' => 'Serial number validation',
            'status' => empty($issues) ? 'passed' : 'failed',
            'details' => empty($issues) ? 'Serial number is unique and properly formatted' : 'Serial number validation issues found',
            'metadata' => [
                'format_confidence' => $serialFormat['confidence'],
                'duplicate_count' => $duplicateCount,
                'length' => $serialLength,
            ],
        ];

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Validate shop legitimacy and status.
     */
    private function validateShop(Shop $shop): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        // Shop approval status
        if (!$shop->approved) {
            $issues[] = 'Shop is not approved by administrators';
        }

        // Shop registration age
        if ($shop->created_at->diffInDays() < 7) {
            $warnings[] = 'Shop was registered very recently';
        }

        // Shop activity validation
        $recentReceipts = $shop->receipts()->where('created_at', '>=', now()->subMonth())->count();
        $totalReceipts = $shop->receipts()->count();

        if ($totalReceipts > 0) {
            $activityRatio = $recentReceipts / $totalReceipts;
            if ($activityRatio > 0.8 && $totalReceipts > 50) {
                $warnings[] = 'Unusual spike in recent shop activity';
            }
        }

        // Suspicious patterns
        $suspiciousPatterns = $this->checkShopSuspiciousPatterns($shop);
        if (!empty($suspiciousPatterns)) {
            $warnings = array_merge($warnings, $suspiciousPatterns);
        }

        $securityChecks[] = [
            'check' => 'Shop validation',
            'status' => empty($issues) ? 'passed' : 'failed',
            'details' => empty($issues) ? 'Shop is approved and legitimate' : 'Shop validation issues found',
            'metadata' => [
                'approved' => $shop->approved,
                'registration_age_days' => $shop->created_at->diffInDays(),
                'total_receipts' => $totalReceipts,
                'recent_receipts' => $recentReceipts,
            ],
        ];

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Validate anti-theft status.
     */
    private function validateAntiTheftStatus(Receipt $receipt): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        try {
            // Check with anti-theft service
            $antiTheftStatus = $this->antiTheftService->checkPhoneStatus(
                $receipt->phone_serial_number
            );

            if ($antiTheftStatus['success']) {
                if ($antiTheftStatus['is_stolen']) {
                    $issues[] = 'Phone is reported as stolen in anti-theft database';
                    
                    $securityChecks[] = [
                        'check' => 'Anti-theft status',
                        'status' => 'failed',
                        'details' => 'Phone is reported as stolen',
                        'metadata' => [
                            'report_date' => $antiTheftStatus['report_date'],
                            'report_location' => $antiTheftStatus['report_location'],
                            'confidence_level' => $antiTheftStatus['confidence_level'],
                        ],
                    ];
                } else {
                    $securityChecks[] = [
                        'check' => 'Anti-theft status',
                        'status' => 'passed',
                        'details' => 'Phone is not reported as stolen',
                        'metadata' => [
                            'last_checked' => $antiTheftStatus['last_checked'],
                            'status' => $antiTheftStatus['status'],
                        ],
                    ];
                }
            } else {
                $warnings[] = 'Unable to verify anti-theft status - service unavailable';
                
                $securityChecks[] = [
                    'check' => 'Anti-theft status',
                    'status' => 'warning',
                    'details' => 'Anti-theft service unavailable',
                    'metadata' => ['error' => $antiTheftStatus['error'] ?? 'Unknown error'],
                ];
            }

        } catch (Exception $e) {
            $warnings[] = 'Anti-theft validation failed due to technical error';
            
            Log::error('Anti-theft validation error', [
                'receipt_id' => $receipt->id,
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Validate payment information.
     */
    private function validatePayments(Receipt $receipt): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        $payments = $receipt->payments;
        $successfulPayments = $payments->where('status', 'successful');
        $totalPaid = $successfulPayments->sum('amount');
        $expectedAmount = $receipt->amount + ($receipt->service_fee ?? 0);

        // Payment amount validation
        if ($totalPaid > 0 && abs($totalPaid - $expectedAmount) > 0.01) {
            $issues[] = 'Payment amount mismatch with receipt amount';
        }

        // Multiple payment attempts validation
        $failedPayments = $payments->where('status', 'failed')->count();
        if ($failedPayments > 5) {
            $warnings[] = 'Unusual number of failed payment attempts';
        }

        // Payment method validation
        foreach ($successfulPayments as $payment) {
            if ($payment->payment_method === 'paystack') {
                $gatewayData = $payment->getGatewayData();
                if (empty($gatewayData['reference']) || empty($gatewayData['status'])) {
                    $warnings[] = 'Payment gateway data appears incomplete';
                }
            }
        }

        $securityChecks[] = [
            'check' => 'Payment validation',
            'status' => empty($issues) ? 'passed' : 'failed',
            'details' => empty($issues) ? 'Payment information is valid' : 'Payment validation issues found',
            'metadata' => [
                'total_paid' => $totalPaid,
                'expected_amount' => $expectedAmount,
                'successful_payments' => $successfulPayments->count(),
                'failed_attempts' => $failedPayments,
            ],
        ];

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Validate resale chain integrity.
     */
    private function validateResaleChain(Receipt $receipt): array
    {
        $issues = [];
        $warnings = [];
        $securityChecks = [];

        if ($receipt->receipt_type === 'resale') {
            // Validate parent receipt exists
            if (!$receipt->parentReceipt) {
                $issues[] = 'Resale receipt missing parent receipt reference';
            } else {
                $parent = $receipt->parentReceipt;
                
                // Validate serial number consistency
                if ($parent->phone_serial_number !== $receipt->phone_serial_number) {
                    $issues[] = 'Serial number mismatch between original and resale receipt';
                }

                // Validate resale code
                if (empty($receipt->resale_code) || $parent->resale_code !== $receipt->resale_code) {
                    $issues[] = 'Invalid or missing resale code';
                }

                // Check for multiple resales of same phone
                $resaleCount = Receipt::where('parent_receipt_id', $parent->id)->count();
                if ($resaleCount > 1) {
                    $warnings[] = 'Phone has been resold multiple times';
                }

                // Time validation
                if ($receipt->created_at <= $parent->created_at) {
                    $issues[] = 'Resale date cannot be before original sale date';
                }
            }
        }

        $securityChecks[] = [
            'check' => 'Resale chain validation',
            'status' => empty($issues) ? 'passed' : 'failed',
            'details' => empty($issues) ? 'Resale chain is valid and complete' : 'Resale chain validation issues found',
        ];

        return [
            'issues' => $issues,
            'warnings' => $warnings,
            'security_checks' => $securityChecks,
        ];
    }

    /**
     * Calculate overall validation score.
     */
    private function calculateValidationScore(array $validationResults): int
    {
        $score = 100;
        
        // Deduct points for issues
        $score -= count($validationResults['issues']) * 15;
        
        // Deduct points for warnings
        $score -= count($validationResults['warnings']) * 5;
        
        // Check security checks
        $passedChecks = 0;
        $totalChecks = count($validationResults['security_checks']);
        
        foreach ($validationResults['security_checks'] as $check) {
            if ($check['status'] === 'passed') {
                $passedChecks++;
            }
        }
        
        if ($totalChecks > 0) {
            $checkScore = ($passedChecks / $totalChecks) * 30;
            $score = ($score * 0.7) + $checkScore;
        }
        
        return max(0, min(100, (int)$score));
    }

    /**
     * Validate receipt number format.
     */
    private function isValidReceiptNumberFormat(string $receiptNumber): bool
    {
        // M-RIGHT receipt format: MR-XXX20240731001
        return preg_match('/^MR-[A-Z]{3}\d{8}\d{3,4}$/', $receiptNumber);
    }

    /**
     * Validate phone number format.
     */
    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        // Basic phone number validation
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }

    /**
     * Validate serial number format.
     */
    private function isValidSerialNumber(string $serialNumber): bool
    {
        // Basic serial number validation
        return strlen($serialNumber) >= 8 && strlen($serialNumber) <= 25 && 
               preg_match('/^[A-Z0-9]+$/', strtoupper($serialNumber));
    }

    /**
     * Analyze serial number format and brand compatibility.
     */
    private function analyzeSerialNumberFormat(string $serialNumber): array
    {
        $confidence = 0.5; // Base confidence
        $patterns = [];

        // iPhone patterns
        if (preg_match('/^[A-Z0-9]{10,12}$/', $serialNumber)) {
            $patterns[] = 'iPhone';
            $confidence += 0.3;
        }

        // Samsung patterns
        if (preg_match('/^[A-Z]{2}\d{8}[A-Z]{2}$/', $serialNumber)) {
            $patterns[] = 'Samsung';
            $confidence += 0.3;
        }

        // Generic smartphone pattern
        if (preg_match('/^[A-Z0-9]{8,15}$/', $serialNumber)) {
            $confidence += 0.2;
        }

        return [
            'confidence' => min(1.0, $confidence),
            'possible_brands' => $patterns,
            'length' => strlen($serialNumber),
            'format' => preg_match('/^[A-Z0-9]+$/', strtoupper($serialNumber)) ? 'alphanumeric' : 'mixed',
        ];
    }

    /**
     * Check for suspicious shop patterns.
     */
    private function checkShopSuspiciousPatterns(Shop $shop): array
    {
        $warnings = [];

        // Check for rapid receipt generation
        $recentReceipts = $shop->receipts()
                              ->where('created_at', '>=', now()->subHours(24))
                              ->count();

        if ($recentReceipts > 50) {
            $warnings[] = 'Unusually high receipt generation in 24 hours';
        }

        // Check for duplicate customer patterns
        $duplicateCustomers = $shop->receipts()
                                  ->select('customer_phone')
                                  ->groupBy('customer_phone')
                                  ->havingRaw('COUNT(*) > 10')
                                  ->count();

        if ($duplicateCustomers > 0) {
            $warnings[] = 'Multiple receipts for same customers detected';
        }

        return $warnings;
    }

    /**
     * Generate security report for receipt.
     */
    public function generateSecurityReport(Receipt $receipt): array
    {
        $validation = $this->validateReceipt($receipt);
        
        return [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'validation_score' => $validation['score'],
            'security_level' => $this->getSecurityLevel($validation['score']),
            'is_trusted' => $validation['is_valid'] && $validation['score'] >= 80,
            'issues_count' => count($validation['issues']),
            'warnings_count' => count($validation['warnings']),
            'security_checks_passed' => collect($validation['security_checks'])
                                       ->where('status', 'passed')
                                       ->count(),
            'total_security_checks' => count($validation['security_checks']),
            'validation_timestamp' => now()->toISOString(),
            'details' => $validation,
        ];
    }

    /**
     * Get security level based on validation score.
     */
    private function getSecurityLevel(int $score): string
    {
        return match(true) {
            $score >= 90 => 'high',
            $score >= 70 => 'medium',
            $score >= 50 => 'low',
            default => 'very_low'
        };
    }
}