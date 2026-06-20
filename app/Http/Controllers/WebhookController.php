<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use App\Services\SmsService;
use App\Services\AntiTheftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class WebhookController extends Controller
{
    private $smsService;
    private $antiTheftService;

    public function __construct(SmsService $smsService, AntiTheftService $antiTheftService)
    {
        $this->smsService = $smsService;
        $this->antiTheftService = $antiTheftService;
    }

    /**
     * Handle Paystack webhook events.
     */
    public function paystack(Request $request)
    {
        try {
            // Verify webhook signature
            $signature = $request->header('x-paystack-signature');
            $body = $request->getContent();
            
            if (!$this->verifyPaystackSignature($signature, $body)) {
                Log::warning('Invalid Paystack webhook signature', [
                    'signature' => $signature,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            $event = $request->all();
            Log::info('Paystack webhook received', ['event' => $event['event'], 'data' => $event['data']]);

            switch ($event['event']) {
                case 'charge.success':
                    return $this->handlePaystackChargeSuccess($event['data']);
                    
                case 'charge.failed':
                    return $this->handlePaystackChargeFailed($event['data']);
                    
                case 'transfer.success':
                    return $this->handlePaystackTransferSuccess($event['data']);
                    
                case 'transfer.failed':
                    return $this->handlePaystackTransferFailed($event['data']);
                    
                case 'customeridentification.success':
                    return $this->handlePaystackCustomerIdentification($event['data']);
                    
                default:
                    Log::info('Unhandled Paystack webhook event', ['event' => $event['event']]);
                    return response()->json(['message' => 'Event acknowledged'], 200);
            }

        } catch (Exception $e) {
            Log::error('Paystack webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle anti-theft API webhook events.
     */
    public function antitheft(Request $request)
    {
        try {
            // Verify webhook signature
            $signature = $request->header('x-antitheft-signature');
            $body = $request->getContent();
            
            if (!$this->verifyAntiTheftSignature($signature, $body)) {
                Log::warning('Invalid anti-theft webhook signature', [
                    'signature' => $signature,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            $event = $request->all();
            Log::info('Anti-theft webhook received', ['event_type' => $event['event_type']]);

            switch ($event['event_type']) {
                case 'phone_reported_stolen':
                    return $this->handlePhoneReportedStolen($event['data']);
                    
                case 'phone_found':
                    return $this->handlePhoneFound($event['data']);
                    
                case 'ownership_disputed':
                    return $this->handleOwnershipDisputed($event['data']);
                    
                case 'status_updated':
                    return $this->handleAntiTheftStatusUpdated($event['data']);
                    
                default:
                    Log::info('Unhandled anti-theft webhook event', ['event_type' => $event['event_type']]);
                    return response()->json(['message' => 'Event acknowledged'], 200);
            }

        } catch (Exception $e) {
            Log::error('Anti-theft webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle SMS delivery status webhooks.
     */
    public function smsDelivery(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('SMS delivery webhook received', [
                'message_id' => $data['message_id'] ?? null,
                'status' => $data['status'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            // Update SMS delivery status in database if needed
            // This can be used for tracking SMS delivery rates
            
            return response()->json(['message' => 'Delivery status updated'], 200);

        } catch (Exception $e) {
            Log::error('SMS delivery webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle successful Paystack charge.
     */
    private function handlePaystackChargeSuccess($data)
    {
        try {
            $payment = Payment::where('gateway_reference', $data['reference'])->first();
            
            if (!$payment) {
                Log::warning('Payment not found for Paystack charge success', ['reference' => $data['reference']]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            if ($payment->status === 'successful') {
                return response()->json(['message' => 'Payment already processed'], 200);
            }

            DB::beginTransaction();

            // Update payment
            $payment->update([
                'status' => 'successful',
                'paid_at' => now(),
                'gateway_response' => $data,
                'amount' => $data['amount'] / 100, // Convert from kobo
            ]);

            // Update receipt
            $receipt = $payment->receipt;
            $receipt->update([
                'payment_status' => 'paid',
                'payment_gateway_status' => 'successful',
                'payment_reference' => $payment->reference,
                'payment_confirmed_at' => now(),
            ]);

            // Update shop earnings
            if ($receipt->shop) {
                $receipt->shop->increment('total_commission_earned', $payment->service_fee);
            }

            DB::commit();

            // Send notifications
            $this->sendPaymentSuccessNotifications($payment);

            Log::info('Paystack payment processed successfully', [
                'payment_id' => $payment->id,
                'reference' => $data['reference'],
                'amount' => $payment->amount,
            ]);

            return response()->json(['message' => 'Payment processed successfully'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process Paystack charge success', [
                'reference' => $data['reference'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Handle failed Paystack charge.
     */
    private function handlePaystackChargeFailed($data)
    {
        try {
            $payment = Payment::where('gateway_reference', $data['reference'])->first();
            
            if (!$payment) {
                Log::warning('Payment not found for Paystack charge failed', ['reference' => $data['reference']]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'gateway_response' => $data,
                'notes' => $data['gateway_response'] ?? 'Payment failed',
            ]);

            // Update receipt
            $payment->receipt->update([
                'payment_gateway_status' => 'failed',
            ]);

            // Send failure notification
            $this->sendPaymentFailedNotification($payment);

            Log::info('Paystack payment failure processed', [
                'payment_id' => $payment->id,
                'reference' => $data['reference'],
                'reason' => $data['gateway_response'] ?? 'Unknown',
            ]);

            return response()->json(['message' => 'Payment failure processed'], 200);

        } catch (Exception $e) {
            Log::error('Failed to process Paystack charge failure', [
                'reference' => $data['reference'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failure processing failed'], 500);
        }
    }

    /**
     * Handle phone reported stolen.
     */
    private function handlePhoneReportedStolen($data)
    {
        try {
            $serialNumber = $data['serial_number'];
            $reportDetails = $data['report_details'] ?? [];

            // Find receipts with this serial number
            $receipts = Receipt::where('phone_serial_number', $serialNumber)
                             ->where('enable_antitheft', true)
                             ->get();

            foreach ($receipts as $receipt) {
                // Send alert to customer
                $alertData = [
                    'type' => 'theft_report',
                    'report_date' => $data['report_date'] ?? now(),
                    'report_location' => $data['report_location'] ?? 'Unknown',
                    'report_reference' => $data['report_reference'] ?? null,
                ];

                $this->smsService->sendAntiTheftAlert($receipt, $alertData);

                // Send email notification
                if ($receipt->customer_email) {
                    Mail::send('emails.antitheft-alert', [
                        'receipt' => $receipt,
                        'alertData' => $alertData,
                    ], function($message) use ($receipt) {
                        $message->to($receipt->customer_email, $receipt->customer_name)
                               ->subject('ALERT: Phone Theft Report - ' . $receipt->receipt_number);
                    });
                }

                Log::info('Anti-theft alert sent for stolen phone', [
                    'receipt_id' => $receipt->id,
                    'serial_number' => $serialNumber,
                    'report_reference' => $data['report_reference'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Theft alerts sent'], 200);

        } catch (Exception $e) {
            Log::error('Failed to process phone theft report', [
                'serial_number' => $data['serial_number'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Theft report processing failed'], 500);
        }
    }

    /**
     * Handle phone found notification.
     */
    private function handlePhoneFound($data)
    {
        try {
            $serialNumber = $data['serial_number'];
            
            // Find receipts with this serial number
            $receipts = Receipt::where('phone_serial_number', $serialNumber)
                             ->where('enable_antitheft', true)
                             ->get();

            foreach ($receipts as $receipt) {
                // Send good news to customer
                $message = "Good news! Your phone {$receipt->phone_name} (S/N: {$serialNumber}) has been found and recovered. Receipt: {$receipt->receipt_number}. Please contact authorities for retrieval. - M-RIGHT";
                
                $this->smsService->sendSms($receipt->customer_phone, $message, 'phone_found');

                if ($receipt->customer_email) {
                    Mail::send('emails.phone-found', [
                        'receipt' => $receipt,
                        'foundData' => $data,
                    ], function($message) use ($receipt) {
                        $message->to($receipt->customer_email, $receipt->customer_name)
                               ->subject('Good News: Phone Found - ' . $receipt->receipt_number);
                    });
                }

                Log::info('Phone found notification sent', [
                    'receipt_id' => $receipt->id,
                    'serial_number' => $serialNumber,
                ]);
            }

            return response()->json(['message' => 'Found notifications sent'], 200);

        } catch (Exception $e) {
            Log::error('Failed to process phone found notification', [
                'serial_number' => $data['serial_number'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Found notification processing failed'], 500);
        }
    }

    /**
     * Handle ownership dispute.
     */
    private function handleOwnershipDisputed($data)
    {
        try {
            $serialNumber = $data['serial_number'];
            $disputeDetails = $data['dispute_details'] ?? [];

            // Find receipts with this serial number
            $receipts = Receipt::where('phone_serial_number', $serialNumber)->get();

            foreach ($receipts as $receipt) {
                // Notify all parties about ownership dispute
                $alertData = [
                    'type' => 'ownership_dispute',
                    'dispute_reference' => $data['dispute_reference'] ?? null,
                    'disputed_by' => $data['disputed_by'] ?? 'Unknown',
                ];

                $this->smsService->sendAntiTheftAlert($receipt, $alertData);

                Log::info('Ownership dispute notification sent', [
                    'receipt_id' => $receipt->id,
                    'serial_number' => $serialNumber,
                    'dispute_reference' => $data['dispute_reference'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Dispute notifications sent'], 200);

        } catch (Exception $e) {
            Log::error('Failed to process ownership dispute', [
                'serial_number' => $data['serial_number'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Dispute processing failed'], 500);
        }
    }

    /**
     * Handle anti-theft status update.
     */
    private function handleAntiTheftStatusUpdated($data)
    {
        try {
            Log::info('Anti-theft status updated', [
                'serial_number' => $data['serial_number'] ?? null,
                'new_status' => $data['new_status'] ?? null,
                'previous_status' => $data['previous_status'] ?? null,
            ]);

            // Clear cache for this phone's status
            if (isset($data['serial_number'])) {
                $cacheKey = 'antitheft_check_' . md5($data['serial_number']);
                \Cache::forget($cacheKey);
            }

            return response()->json(['message' => 'Status update processed'], 200);

        } catch (Exception $e) {
            Log::error('Failed to process anti-theft status update', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Status update processing failed'], 500);
        }
    }

    /**
     * Send payment success notifications.
     */
    private function sendPaymentSuccessNotifications(Payment $payment)
    {
        try {
            // Send SMS notification
            $this->smsService->sendPaymentConfirmation($payment);

            // Send email notification
            if ($payment->customer_email) {
                Mail::send('emails.payment-success', [
                    'payment' => $payment,
                ], function($message) use ($payment) {
                    $message->to($payment->customer_email)
                           ->subject('Payment Confirmed - Receipt ' . $payment->receipt->receipt_number);
                });
            }

        } catch (Exception $e) {
            Log::error('Failed to send payment success notifications', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment failed notification.
     */
    private function sendPaymentFailedNotification(Payment $payment)
    {
        try {
            if ($payment->customer_email) {
                Mail::send('emails.payment-failed', [
                    'payment' => $payment,
                ], function($message) use ($payment) {
                    $message->to($payment->customer_email)
                           ->subject('Payment Failed - Receipt ' . $payment->receipt->receipt_number);
                });
            }

        } catch (Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify Paystack webhook signature.
     */
    private function verifyPaystackSignature($signature, $body): bool
    {
        $hash = hash_hmac('sha512', $body, config('services.paystack.secret_key'));
        return hash_equals($signature, $hash);
    }

    /**
     * Verify anti-theft webhook signature.
     */
    private function verifyAntiTheftSignature($signature, $body): bool
    {
        $hash = hash_hmac('sha256', $body, config('services.antitheft.api_secret'));
        return hash_equals($signature, $hash);
    }

    /**
     * Handle Paystack transfer success.
     */
    private function handlePaystackTransferSuccess($data)
    {
        Log::info('Paystack transfer successful', ['data' => $data]);
        return response()->json(['message' => 'Transfer success processed'], 200);
    }

    /**
     * Handle Paystack transfer failed.
     */
    private function handlePaystackTransferFailed($data)
    {
        Log::info('Paystack transfer failed', ['data' => $data]);
        return response()->json(['message' => 'Transfer failure processed'], 200);
    }

    /**
     * Handle Paystack customer identification.
     */
    private function handlePaystackCustomerIdentification($data)
    {
        Log::info('Paystack customer identification completed', ['data' => $data]);
        return response()->json(['message' => 'Customer identification processed'], 200);
    }
}