<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    private $provider;
    private $apiKey;
    private $senderId;
    private $apiUrl;

    public function __construct()
    {
        $this->provider = config('services.sms.provider');
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id');
        $this->apiUrl = config('services.sms.api_url');
    }

    /**
     * Send receipt notification SMS.
     */
    public function sendReceiptNotification(Receipt $receipt): bool
    {
        if (!$receipt->customer_phone) {
            return false;
        }

        $message = $this->buildReceiptMessage($receipt);
        return $this->sendSms($receipt->customer_phone, $message, 'receipt_notification');
    }

    /**
     * Send payment confirmation SMS.
     */
    public function sendPaymentConfirmation(Payment $payment): bool
    {
        if (!$payment->customer_phone) {
            return false;
        }

        $message = $this->buildPaymentMessage($payment);
        return $this->sendSms($payment->customer_phone, $message, 'payment_confirmation');
    }

    /**
     * Send anti-theft alert SMS.
     */
    public function sendAntiTheftAlert(Receipt $receipt, array $alertData): bool
    {
        if (!$receipt->customer_phone || !$receipt->enable_antitheft) {
            return false;
        }

        $message = $this->buildAntiTheftMessage($receipt, $alertData);
        return $this->sendSms($receipt->customer_phone, $message, 'antitheft_alert');
    }

    /**
     * Send resale notification SMS.
     */
    public function sendResaleNotification(Receipt $originalReceipt, Receipt $resaleReceipt): bool
    {
        if (!$originalReceipt->customer_phone) {
            return false;
        }

        $message = $this->buildResaleMessage($originalReceipt, $resaleReceipt);
        return $this->sendSms($originalReceipt->customer_phone, $message, 'resale_notification');
    }

    /**
     * Send shop approval notification SMS.
     */
    public function sendShopApprovalNotification($shopOwner, $shop): bool
    {
        if (!$shopOwner->phone_number) {
            return false;
        }

        $message = "Hello {$shopOwner->name}, your shop '{$shop->shop_name}' has been approved! You can now start generating digital receipts. Visit " . route('dashboard') . " to get started. - M-RIGHT";
        
        return $this->sendSms($shopOwner->phone_number, $message, 'shop_approval');
    }

    /**
     * Send generic SMS.
     */
    public function sendSms(string $phoneNumber, string $message, string $type = 'general'): bool
    {
        try {
            // Normalize phone number
            $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
            
            if (!$phoneNumber) {
                Log::warning('Invalid phone number provided for SMS', ['phone' => $phoneNumber]);
                return false;
            }

            // Choose provider method
            $result = match($this->provider) {
                'termii' => $this->sendViaTermii($phoneNumber, $message),
                'twilio' => $this->sendViaTwilio($phoneNumber, $message),
                'nexmo' => $this->sendViaNexmo($phoneNumber, $message),
                default => $this->sendViaTermii($phoneNumber, $message) // Default to Termii
            };

            if ($result) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'type' => $type,
                    'provider' => $this->provider,
                ]);
            }

            return $result;

        } catch (Exception $e) {
            Log::error('SMS sending failed', [
                'phone' => $phoneNumber,
                'type' => $type,
                'provider' => $this->provider,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send SMS via Termii.
     */
    private function sendViaTermii(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(30)->post($this->apiUrl . '/api/sms/send', [
                'to' => $phoneNumber,
                'from' => $this->senderId,
                'sms' => $message,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['message_id']) || isset($data['sms_id']);
            }

            Log::error('Termii SMS API error', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return false;

        } catch (Exception $e) {
            Log::error('Termii SMS exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send SMS via Twilio.
     */
    private function sendViaTwilio(string $phoneNumber, string $message): bool
    {
        try {
            // Implement Twilio SMS sending
            // This is a placeholder for Twilio integration
            $response = Http::withBasicAuth(config('services.twilio.sid'), config('services.twilio.token'))
                          ->asForm()
                          ->post("https://api.twilio.com/2010-04-01/Accounts/" . config('services.twilio.sid') . "/Messages.json", [
                              'From' => config('services.twilio.from'),
                              'To' => $phoneNumber,
                              'Body' => $message,
                          ]);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('Twilio SMS exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send SMS via Nexmo (Vonage).
     */
    private function sendViaNexmo(string $phoneNumber, string $message): bool
    {
        try {
            // Implement Nexmo SMS sending
            // This is a placeholder for Nexmo integration
            $response = Http::post('https://rest.nexmo.com/sms/json', [
                'api_key' => config('services.nexmo.key'),
                'api_secret' => config('services.nexmo.secret'),
                'from' => $this->senderId,
                'to' => $phoneNumber,
                'text' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return isset($data['messages'][0]['status']) && $data['messages'][0]['status'] === '0';
            }

            return false;

        } catch (Exception $e) {
            Log::error('Nexmo SMS exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Build receipt notification message.
     */
    private function buildReceiptMessage(Receipt $receipt): string
    {
        $shopName = $receipt->shop->shop_name ?? 'M-RIGHT Partner';
        $verifyUrl = route('public.receipt', $receipt->receipt_number);
        
        return "Hello {$receipt->customer_name}, your digital receipt {$receipt->receipt_number} for {$receipt->phone_name} has been generated by {$shopName}. Amount: ₦" . number_format($receipt->amount, 2) . ". Verify: {$verifyUrl} - M-RIGHT";
    }

    /**
     * Build payment confirmation message.
     */
    private function buildPaymentMessage(Payment $payment): string
    {
        $receipt = $payment->receipt;
        
        return "Payment confirmed! ₦" . number_format($payment->amount, 2) . " payment for receipt {$receipt->receipt_number} ({$receipt->phone_name}) has been successfully processed. Ref: {$payment->reference} - M-RIGHT";
    }

    /**
     * Build anti-theft alert message.
     */
    private function buildAntiTheftMessage(Receipt $receipt, array $alertData): string
    {
        $alertType = $alertData['type'] ?? 'general';
        
        switch($alertType) {
            case 'theft_report':
                return "ALERT: Your phone {$receipt->phone_name} (S/N: {$receipt->phone_serial_number}) has been reported as stolen. If this is incorrect, please contact us immediately. Receipt: {$receipt->receipt_number} - M-RIGHT";
                
            case 'suspicious_activity':
                return "ALERT: Suspicious activity detected on your phone {$receipt->phone_name} (Receipt: {$receipt->receipt_number}). Please verify your phone's security. Contact support if needed. - M-RIGHT";
                
            case 'ownership_dispute':
                return "NOTICE: Ownership verification requested for your phone {$receipt->phone_name} (Receipt: {$receipt->receipt_number}). Please keep your receipt safe as proof of purchase. - M-RIGHT";
                
            default:
                return "ALERT: Security notification for your phone {$receipt->phone_name} (Receipt: {$receipt->receipt_number}). Please contact M-RIGHT support for details. - M-RIGHT";
        }
    }

    /**
     * Build resale notification message.
     */
    private function buildResaleMessage(Receipt $originalReceipt, Receipt $resaleReceipt): string
    {
        return "Your phone {$originalReceipt->phone_name} (S/N: {$originalReceipt->phone_serial_number}) has been resold. New receipt: {$resaleReceipt->receipt_number}. If this wasn't authorized by you, please report to M-RIGHT immediately.";
    }

    /**
     * Normalize phone number to international format.
     */
    private function normalizePhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        // If phone starts with 0, replace with +234 (Nigeria)
        if (str_starts_with($phone, '0')) {
            $phone = '+234' . substr($phone, 1);
        }
        
        // If phone doesn't start with +, assume Nigeria and add +234
        if (!str_starts_with($phone, '+')) {
            // If it's 10 digits, assume it's Nigerian without leading 0
            if (strlen($phone) === 10) {
                $phone = '+234' . $phone;
            }
            // If it's 11 digits starting with 234, add +
            elseif (strlen($phone) === 13 && str_starts_with($phone, '234')) {
                $phone = '+' . $phone;
            }
            // If it's 11 digits starting with 1, might be US number
            elseif (strlen($phone) === 11 && str_starts_with($phone, '1')) {
                $phone = '+' . $phone;
            }
        }
        
        // Validate phone number length (minimum 10 digits after country code)
        if (strlen($phone) < 10 || !str_starts_with($phone, '+')) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Get SMS delivery status.
     */
    public function getDeliveryStatus(string $messageId): array
    {
        try {
            switch($this->provider) {
                case 'termii':
                    $response = Http::get($this->apiUrl . '/api/sms/inbox', [
                        'api_key' => $this->apiKey,
                        'message_id' => $messageId,
                    ]);
                    break;
                    
                default:
                    return ['status' => 'unknown', 'delivered' => null];
            }

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => $data['status'] ?? 'unknown',
                    'delivered' => $data['delivered'] ?? null,
                    'delivered_at' => $data['delivered_at'] ?? null,
                ];
            }

            return ['status' => 'unknown', 'delivered' => null];

        } catch (Exception $e) {
            Log::error('SMS status check failed', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'error', 'delivered' => null];
        }
    }

    /**
     * Send bulk SMS messages.
     */
    public function sendBulkSms(array $recipients, string $message): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $phoneNumber = is_array($recipient) ? $recipient['phone'] : $recipient;
            $customMessage = is_array($recipient) && isset($recipient['message']) ? $recipient['message'] : $message;
            
            $results[] = [
                'phone' => $phoneNumber,
                'sent' => $this->sendSms($phoneNumber, $customMessage, 'bulk'),
            ];
        }
        
        return $results;
    }

    /**
     * Check if SMS service is available.
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiUrl);
    }

    /**
     * Get SMS service configuration.
     */
    public function getConfiguration(): array
    {
        return [
            'provider' => $this->provider,
            'sender_id' => $this->senderId,
            'available' => $this->isAvailable(),
            'features' => [
                'delivery_reports' => true,
                'bulk_messaging' => true,
                'international' => true,
            ],
        ];
    }
}