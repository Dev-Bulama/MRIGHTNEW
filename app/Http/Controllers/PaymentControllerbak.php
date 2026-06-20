<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class PaymentController extends Controller
{
    /**
     * Paystack configuration
     */
    private $paystackSecretKey;
    private $paystackPublicKey;
    private $paystackBaseUrl;

    public function __construct()
    {
        $this->paystackSecretKey = config('services.paystack.secret_key');
        $this->paystackPublicKey = config('services.paystack.public_key');
        $this->paystackBaseUrl = 'https://api.paystack.co';
    }

    /**
     * Display payment page for receipt.
     */
    public function show(Receipt $receipt)
    {
        // Check if receipt belongs to authenticated user or is public
        if (!$this->canAccessReceipt($receipt)) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Check if payment is already successful
        if ($receipt->payment_status === 'paid') {
            return redirect()->route('receipt.show', $receipt)
                ->with('info', 'This receipt has already been paid for.');
        }

        // Get or create pending payment record
        $payment = $this->getOrCreatePayment($receipt);

        return view('payment.show', compact('receipt', 'payment'));
    }

    /**
     * Initialize payment with Paystack.
     */
    /**
 * Initialize payment with Paystack.
 */

    
/**
 * Initialize payment with Paystack (FIXED - Include all required fields).
 */
// public function initialize(Request $request, Receipt $receipt)
// {
//     $validated = $request->validate([
//         'email' => ['required', 'email'],
//         'phone' => ['nullable', 'string'],
//     ]);

//     try {
//         DB::beginTransaction();

//         // Check if receipt can be paid
//         if ($receipt->payment_gateway_status === 'successful') {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Receipt has already been paid for.'
//             ]);
//         }

//         // Use service fee (admin-set amount) NOT the item amount
//         $amountToCharge = $receipt->service_fee * 100; // Convert to kobo
        
//         // Create unique reference
//         $reference = 'MR_' . $receipt->id . '_' . uniqid() . '_' . time();
        
//         // Create or update payment record (INCLUDE ALL REQUIRED FIELDS)
//         $payment = Payment::updateOrCreate(
//             [
//                 'receipt_id' => $receipt->id,
//                 'status' => 'pending'
//             ],
//             [
//                 'user_id' => Auth::id(),
//                 'amount' => $receipt->service_fee, // Service fee, not item cost
//                 'currency' => 'NGN',
//                 'customer_email' => $validated['email'], // FIX: Include customer_email
//                 'customer_phone' => $validated['phone'], // FIX: Include customer_phone
//                 'payment_method' => 'paystack',
//                 'reference' => $reference,
//                 'status' => 'pending',
//                 'metadata' => json_encode([
//                     'receipt_number' => $receipt->receipt_number,
//                     'customer_name' => $receipt->customer_name,
//                     'service_type' => $receipt->receipt_type === 'resale' ? 'Resale Processing' : 'Receipt Generation',
//                     'item_amount' => $receipt->amount, // For reference only
//                     'service_fee' => $receipt->service_fee, // What's being charged
//                 ])
//             ]
//         );

//         // Initialize payment with Paystack
//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . $this->paystackSecretKey,
//             'Content-Type' => 'application/json',
//         ])->post($this->paystackBaseUrl . '/transaction/initialize', [
//             'email' => $validated['email'],
//             'amount' => $amountToCharge, // Service fee in kobo
//             'reference' => $payment->reference,
//             'callback_url' => route('payment.verify', $payment->reference),
//             'metadata' => [
//                 'receipt_id' => $receipt->id,
//                 'payment_id' => $payment->id,
//                 'service_type' => $receipt->receipt_type === 'resale' ? 'Resale Processing Fee' : 'Receipt Generation Fee',
//                 'item_amount' => number_format($receipt->amount, 2),
//                 'service_fee' => number_format($receipt->service_fee, 2),
//                 'customer_name' => $receipt->customer_name,
//                 'receipt_number' => $receipt->receipt_number,
//             ],
//             'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
//         ]);

//         if (!$response->successful()) {
//             throw new Exception('Payment initialization failed: ' . $response->body());
//         }

//         $responseData = $response->json();

//         // Update payment with Paystack details
//         $payment->update([
//             'paystack_reference' => $responseData['data']['reference'],
//             'authorization_url' => $responseData['data']['authorization_url'],
//             'access_code' => $responseData['data']['access_code'],
//             'status' => 'pending',
//         ]);

//         DB::commit();

//         return response()->json([
//             'success' => true,
//             'data' => [
//                 'authorization_url' => $responseData['data']['authorization_url'],
//                 'reference' => $payment->reference,
//                 'amount' => $receipt->service_fee, // Show service fee
//                 'item_amount' => $receipt->amount, // Show item cost for reference
//             ]
//         ]);

//     } catch (Exception $e) {
//         DB::rollback();
        
//         Log::error('Payment initialization failed: ' . $e->getMessage(), [
//             'receipt_id' => $receipt->id,
//             'user_id' => Auth::id(),
//             'error_details' => $e->getTraceAsString(),
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'Payment initialization failed. Please try again.',
//             'error' => app()->environment('local') ? $e->getMessage() : null
//         ], 500);
//     }
// }
/**
 * Initialize payment with Paystack (DEBUGGED VERSION).
 */
/**
 * Initialize payment with Paystack (FIXED - Only charge service fee).
 */
/**
 * Initialize payment with Paystack only (SIMPLIFIED).
 */
public function initialize(Request $request, Receipt $receipt)
{
    \Log::info('Payment initialization started', [
        'receipt_id' => $receipt->id,
        'request_data' => $request->all()
    ]);

    $validated = $request->validate([
        'email' => ['required', 'email'],
        'phone' => ['nullable', 'string'],
        // Remove payment_method validation since it's always paystack
    ]);

    try {
        DB::beginTransaction();

        // Check if receipt can be paid
        if ($receipt->payment_gateway_status === 'successful') {
            return response()->json([
                'success' => false,
                'message' => 'Receipt has already been paid for.'
            ], 422);
        }

        // Calculate service fee
        $serviceFee = $receipt->is_resale 
            ? (float) config('services.app.resale_fee', 200)
            : (float) config('services.app.receipt_generation_fee', 500);

        // Create or update payment record
        $payment = Payment::updateOrCreate(
            ['receipt_id' => $receipt->id],
            [
                'user_id' => Auth::id(),
                'reference' => 'PAY_' . strtoupper(uniqid()) . '_' . time(),
                'amount' => $serviceFee,
                'currency' => 'NGN',
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'description' => $receipt->is_resale ? 'Resale Processing Fee' : 'Receipt Generation Fee',
                'status' => 'pending',
                'payment_method' => 'paystack', // Always paystack
            ]
        );

        // Initialize Paystack payment
        $paystackResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'reference' => $payment->reference,
            'amount' => $serviceFee * 100, // Convert to kobo
            'email' => $validated['email'],
            'currency' => 'NGN',
            'callback_url' => route('payment.verify', $payment->reference),
            'metadata' => [
                'receipt_id' => $receipt->id,
                'user_id' => Auth::id(),
                'payment_type' => $receipt->is_resale ? 'Resale Processing Fee' : 'Receipt Generation Fee',
                'item_amount' => number_format($receipt->amount, 2),
                'service_fee' => number_format($serviceFee, 2),
                'customer_name' => $receipt->customer_name,
                'receipt_number' => $receipt->receipt_number,
            ],
            'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money'],
        ]);

        if (!$paystackResponse->successful()) {
            throw new Exception('Payment initialization failed: ' . $paystackResponse->body());
        }

        $responseData = $paystackResponse->json();

        // Update payment with Paystack details
        $payment->update([
            'paystack_reference' => $responseData['data']['reference'],
            'paystack_access_code' => $responseData['data']['access_code'],
            'paystack_response' => $responseData,
            'transaction_id' => $responseData['data']['reference'],
            'status' => 'pending',
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'data' => [
                'authorization_url' => $responseData['data']['authorization_url'],
                'reference' => $payment->reference,
                'amount' => $serviceFee,
                'item_amount' => $receipt->amount,
                'breakdown' => [
                    'item_cost' => '₦' . number_format($receipt->amount, 2) . ' (for records)',
                    'service_fee' => '₦' . number_format($serviceFee, 2) . ' (amount charged)'
                ]
            ]
        ]);

    } catch (Exception $e) {
        DB::rollback();
        
        Log::error('Payment initialization failed: ' . $e->getMessage(), [
            'receipt_id' => $receipt->id,
            'user_id' => Auth::id(),
            'error_details' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Payment initialization failed. Please try again.',
            'error' => app()->environment('local') ? $e->getMessage() : 'Payment system temporarily unavailable.'
        ], 500);
    }
}
    /**
     * Verify payment status.
     */
    /**
 * Verify payment status (supports both API and browser requests).
 */
public function verify(Request $request, $reference)
{
    try {
        $payment = Payment::where('gateway_reference', $reference)
                 ->orWhere('paystack_reference', $reference)
                 ->orWhere('reference', $reference)
                 ->first();

if (!$payment) {
    Log::error('Payment verification failed - not found', [
        'reference' => $reference
    ]);
    
    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => false,
            'message' => 'Payment record not found',
        ], 404);
    } else {
        return redirect()->route('dashboard')
            ->with('error', 'Payment record not found');
    }
}
        
        if ($payment->payment_method === 'paystack') {
            $verification = $this->verifyPaystackTransaction($reference);
            
            if ($verification['success'] && $verification['data']['status'] === 'success') {
                $this->updatePaymentSuccess($payment, $verification['data']);
                
                // Check if this is an API request (AJAX) or browser request
                if ($request->expectsJson() || $request->ajax()) {
                    // Return JSON for API requests
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment verified successfully',
                        'receipt_url' => route('receipt.show', $payment->receipt),
                    ]);
                } else {
                    // Redirect browser requests to success page
                    return redirect()->route('payment.success', $reference);
                }
                
            } else {
                Log::warning('Payment verification failed', [
                    'reference' => $reference,
                    'verification_response' => $verification
                ]);
                
                // Check if this is an API request (AJAX) or browser request
                if ($request->expectsJson() || $request->ajax()) {
                    // Return JSON for API requests
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed',
                    ]);
                } else {
                    // Redirect browser requests to failed page
                    return redirect()->route('payment.failed', $reference);
                }
            }
        } else {
            // Non-Paystack payments
            $message = 'Bank transfer payments require manual verification';
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ]);
            } else {
                return redirect()->route('dashboard')
                    ->with('info', $message);
            }
        }

    } catch (Exception $e) {
        Log::error('Payment verification failed', [
            'reference' => $reference,
            'error' => $e->getMessage(),
        ]);

        $message = 'Payment verification failed';
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        } else {
            return redirect()->route('payment.failed', $reference);
        }
    }
}

    /**
     * Handle Paystack webhook.
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $body = $request->getContent();
        
        if (!$this->verifyWebhookSignature($signature, $body)) {
            Log::warning('Invalid webhook signature', ['signature' => $signature]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->all();
        
        try {
            switch ($event['event']) {
                case 'charge.success':
                    $this->handleSuccessfulCharge($event['data']);
                    break;
                    
                case 'charge.failed':
                    $this->handleFailedCharge($event['data']);
                    break;
                    
                case 'transfer.success':
                case 'transfer.failed':
                    // Handle transfer events if needed
                    break;
                    
                default:
                    Log::info('Unhandled webhook event', ['event' => $event['event']]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);

        } catch (Exception $e) {
            Log::error('Webhook processing failed', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Get payment history for user.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = Payment::with(['receipt', 'user'])
                       ->where('user_id', $user->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);

        return view('payment.history', compact('payments'));
    }
/**
     * Display payment overview for shop owner.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has a shop
        if (!$user->shop || !$user->shop->approved) {
            return redirect()->route('shop.create')
                ->with('warning', 'Please complete shop registration and wait for approval to manage payments.');
        }

        $query = $user->receipts()->with(['shop']);
        
        // Apply filters
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $receipts = $query->latest()->paginate(15);
        
        // Payment statistics
        $stats = [
            'total_receipts' => $user->receipts()->count(),
            'paid' => $user->receipts()->where('payment_status', 'paid')->count(),
            'partial' => $user->receipts()->where('payment_status', 'partial')->count(),
            'pending' => $user->receipts()->where('payment_status', 'pending')->count(),
            'total_revenue' => $user->receipts()->whereIn('payment_status', ['paid', 'partial'])->sum('amount'),
            'pending_revenue' => $user->receipts()->where('payment_status', 'pending')->sum('amount'),
        ];

        return view('payment.index', compact('receipts', 'stats'));
    }

    /**
     * Update payment status for a receipt.
     */
    public function updateStatus(Request $request, Receipt $receipt)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($receipt->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $validated = $request->validate([
            'payment_status' => ['required', 'in:paid,partial,pending'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            // Update receipt payment status
            $receipt->update([
                'payment_status' => $validated['payment_status'],
                'payment_notes' => $validated['notes'] ?? null,
                'payment_updated_at' => now(),
            ]);

            // Log the payment status change
            Log::info('Payment status updated', [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'old_status' => $receipt->getOriginal('payment_status'),
                'new_status' => $validated['payment_status'],
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully!',
                'new_status' => $validated['payment_status'],
                'status_badge' => $this->getStatusBadge($validated['payment_status']),
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Payment status update failed', [
                'receipt_id' => $receipt->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk update payment status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'receipt_ids' => ['required', 'array'],
            'receipt_ids.*' => ['integer', 'exists:receipts,id'],
            'payment_status' => ['required', 'in:paid,partial,pending'],
        ]);

        try {
            DB::beginTransaction();

            $updated = $user->receipts()
                ->whereIn('id', $validated['receipt_ids'])
                ->update([
                    'payment_status' => $validated['payment_status'],
                    'payment_updated_at' => now(),
                ]);

            Log::info('Bulk payment status update', [
                'receipt_ids' => $validated['receipt_ids'],
                'new_status' => $validated['payment_status'],
                'updated_count' => $updated,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Updated {$updated} receipt(s) successfully!",
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk payment status update failed', [
                'receipt_ids' => $validated['receipt_ids'] ?? [],
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status. Please try again.'
            ], 500);
        }
    }
    /**
     * Show payment success page.
     */
    /**
 * Show payment success page with real-time verification.
 */
public function success(Request $request, $reference)
{
    try {
        $payment = $this->findPaymentByReference($reference);

        if (!$payment) {
            Log::error('Payment not found with any reference field', [
                'reference_searched' => $reference
            ]);
            
            return redirect()->route('dashboard')
                ->with('error', 'Payment record not found. Reference: ' . $reference);
        }

        // If payment is already marked as successful, show success page
        if ($payment->status === 'successful') {
            return view('payment.success', compact('payment'));
        }

        // If payment is still pending, verify with Paystack directly
        if ($payment->payment_method === 'paystack') {
            Log::info('Payment status pending, verifying with Paystack API', [
                'payment_id' => $payment->id,
                'reference' => $reference,
                'current_status' => $payment->status
            ]);

            $verification = $this->verifyPaystackTransaction($reference);
            
            if ($verification['success'] && $verification['data']['status'] === 'success') {
                // Payment is actually successful, update our records
                Log::info('Paystack verification successful, updating payment status', [
                    'payment_id' => $payment->id,
                    'reference' => $reference
                ]);

                $this->updatePaymentSuccess($payment, $verification['data']);
                
                // Refresh the payment model to get updated status
                $payment->refresh();
                
                return view('payment.success', compact('payment'));
            } else {
                // Payment verification failed with Paystack
                Log::warning('Paystack verification failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'verification_response' => $verification
                ]);

                return redirect()->route('payment.failed', $reference);
            }
        }

        // For non-Paystack payments, redirect to pending page
        return redirect()->route('dashboard')
            ->with('warning', 'Payment verification pending. Please check back later.');

    } catch (Exception $e) {
        Log::error('Payment success page error', [
            'reference' => $reference,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('dashboard')
            ->with('error', 'Payment record not found.');
    }
}
    /**
     * Show payment failure page.
     */
   /**
 * Show payment failure page with verification.
 */
public function failed(Request $request, $reference)
{
    try {
        $payment = Payment::where('gateway_reference', $reference)
                 ->orWhere('paystack_reference', $reference)
                 ->orWhere('reference', $reference)
                 ->with(['receipt', 'user'])
                 ->first();

        if (!$payment) {
            return redirect()->route('dashboard')
                ->with('error', 'Payment record not found.');
        }

        // Double-check with Paystack in case webhook failed but payment was successful
        if ($payment->payment_method === 'paystack' && $payment->status !== 'successful') {
            Log::info('Double-checking payment status with Paystack for failed page', [
                'payment_id' => $payment->id,
                'reference' => $reference
            ]);

            $verification = $this->verifyPaystackTransaction($reference);
            
            if ($verification['success'] && $verification['data']['status'] === 'success') {
                Log::info('Payment was actually successful, redirecting to success page', [
                    'payment_id' => $payment->id,
                    'reference' => $reference
                ]);

                // Payment is actually successful, update records and redirect
                $this->updatePaymentSuccess($payment, $verification['data']);
                
                return redirect()->route('payment.success', $reference);
            }
        }

        return view('payment.failed', compact('payment'));

    } catch (Exception $e) {
        Log::error('Payment failed page error', [
            'reference' => $reference,
            'error' => $e->getMessage()
        ]);

        return redirect()->route('dashboard')
            ->with('error', 'Unable to load payment information.');
    }
}
    /**
     * Retry failed payment.
     */
    public function retry(Payment $payment)
    {
        if ($payment->status === 'successful') {
            return redirect()->route('receipt.show', $payment->receipt)
                ->with('info', 'This payment has already been completed.');
        }

        // Reset payment status
        $payment->update([
            'status' => 'pending',
            'gateway_reference' => null,
            'gateway_response' => null,
        ]);

        return redirect()->route('payment.show', $payment->receipt)
            ->with('info', 'Payment reset. You can try again.');
    }

    /**
     * Initialize Paystack transaction.
     */
    private function initializePaystackTransaction($payment, $receipt)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->paystackSecretKey,
            'Content-Type' => 'application/json',
        ])->post($this->paystackBaseUrl . '/transaction/initialize', [
            'email' => $payment->customer_email,
            'amount' => $payment->amount * 100, // Convert to kobo
            'currency' => $payment->currency,
            'reference' => $payment->reference,
            'callback_url' => route('payment.success', $payment->reference),
            'metadata' => [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'customer_name' => $receipt->customer_name,
                'phone_serial' => $receipt->phone_serial_number,
                'payment_id' => $payment->id,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => $data['status'],
                'data' => $data['data'],
                'message' => $data['message'],
            ];
        } else {
            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Payment initialization failed',
                'data' => null,
            ];
        }
    }

    /**
     * Verify Paystack transaction.
     */
    private function verifyPaystackTransaction($reference)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->paystackSecretKey,
        ])->get($this->paystackBaseUrl . '/transaction/verify/' . $reference);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => $data['status'],
                'data' => $data['data'],
                'message' => $data['message'],
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Verification request failed',
                'data' => null,
            ];
        }
    }
/**
 * Find payment by any reference field.
 */
private function findPaymentByReference($reference)
{
    return Payment::where('gateway_reference', $reference)
                  ->orWhere('paystack_reference', $reference)
                  ->orWhere('reference', $reference)
                  ->first();
}
    /**
     * Update payment to successful status.
     */
    private function updatePaymentSuccess($payment, $gatewayData)
    {
        DB::beginTransaction();

        try {
            // Update payment
            $payment->update([
                'status' => 'successful',
                'paid_at' => now(),
                'gateway_response' => $gatewayData,
                'amount' => $gatewayData['amount'] / 100, // Convert from kobo
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
            // Send receipt email after successful payment
            // Send receipt email after successful Paystack payment - ALWAYS to SHOP OWNER
try {
    // Load receipt with all needed relationships for email template
    $receipt->load(['shop', 'user', 'parentReceipt.shop', 'parentReceipt.user']);
    
    // Get shop owner email
    $shopOwnerEmail = $receipt->user->email ?? $receipt->shop->user->email ?? null;
    
    if ($shopOwnerEmail) {
        // Send to SHOP OWNER (for both resale and regular receipts)
        \Mail::to($shopOwnerEmail)->send(new \App\Mail\ReceiptGenerated($receipt));

        Log::info('Paystack payment receipt email sent to SHOP OWNER', [
            'payment_id' => $payment->id,
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'receipt_type' => $receipt->receipt_type ?? 'regular',
            'shop_owner_email' => $shopOwnerEmail,
            'customer_email' => $receipt->customer_email ?? 'N/A',
            'payment_amount' => $payment->amount,
        ]);
    } else {
        Log::warning('No shop owner email found - Paystack receipt email skipped', [
            'payment_id' => $payment->id,
            'receipt_id' => $receipt->id,
            'receipt_type' => $receipt->receipt_type ?? 'regular',
        ]);
    }
} catch (\Exception $e) {
    // Don't fail payment on email error - just log it
    Log::error('Failed to send Paystack receipt email after payment success', [
        'payment_id' => $payment->id,
        'receipt_id' => $receipt->id,
        'receipt_type' => $receipt->receipt_type ?? 'unknown',
        'error' => $e->getMessage(),
    ]);
}
// try {
//     if ($receipt->customer_email) {
//         // Load receipt with all needed relationships for email template
//         $receipt->load(['shop', 'user', 'parentReceipt.shop', 'parentReceipt.user']);
        
//         // Use existing ReceiptGenerated mailable (original functionality)
//         \Mail::to($receipt->customer_email)->send(new \App\Mail\ReceiptGenerated($receipt));

//         Log::info('Receipt email sent after successful payment', [
//             'payment_id' => $payment->id,
//             'receipt_id' => $receipt->id,
//             'receipt_number' => $receipt->receipt_number,
//             'customer_email' => $receipt->customer_email,
//             'payment_amount' => $payment->amount,
//         ]);
//     } else {
//         Log::info('No customer email - receipt email skipped', [
//             'payment_id' => $payment->id,
//             'receipt_id' => $receipt->id,
//         ]);
//     }
// } catch (\Exception $e) {
//     // Don't fail payment on email error - just log it
//     Log::error('Failed to send receipt email after payment success', [
//         'payment_id' => $payment->id,
//         'receipt_id' => $receipt->id,
//         'error' => $e->getMessage(),
//         'customer_email' => $receipt->customer_email ?? 'unknown'
//     ]);
// }

            // Log successful payment
            Log::info('Payment successful', [
                'payment_id' => $payment->id,
                'receipt_id' => $receipt->id,
                'amount' => $payment->amount,
                'reference' => $payment->reference,
            ]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle successful charge webhook.
     */
    private function handleSuccessfulCharge($data)
    {
        $payment = Payment::where('gateway_reference', $data['reference'])
                 ->orWhere('paystack_reference', $data['reference'])
                 ->orWhere('reference', $data['reference'])
                 ->first();
        
        if ($payment && $payment->status !== 'successful') {
            $this->updatePaymentSuccess($payment, $data);
        }
    }

    /**
     * Handle failed charge webhook.
     */
    private function handleFailedCharge($data)
    {
        $payment = Payment::where('gateway_reference', $data['reference'])
                 ->orWhere('paystack_reference', $data['reference'])
                 ->orWhere('reference', $data['reference'])
                 ->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $data,
            ]);

            // Update receipt
            $payment->receipt->update([
                'payment_gateway_status' => 'failed',
            ]);

            Log::info('Payment failed', [
                'payment_id' => $payment->id,
                'reference' => $data['reference'],
                'reason' => $data['gateway_response'] ?? 'Unknown',
            ]);
        }
    }

    /**
     * Verify webhook signature.
     */
    private function verifyWebhookSignature($signature, $body)
    {
        $hash = hash_hmac('sha512', $body, $this->paystackSecretKey);
        return hash_equals($signature, $hash);
    }

    /**
     * Generate bank transfer details.
     */
    private function generateBankTransferDetails($payment)
    {
        // In production, you would integrate with your bank's API
        // For now, return static account details
        return [
            'bank_name' => 'First Bank of Nigeria',
            'account_number' => '1234567890',
            'account_name' => 'M-RIGHT DIGITAL SERVICES',
            'amount' => number_format($payment->amount, 2),
            'reference' => $payment->reference,
            'instructions' => 'Please use the payment reference as your transaction narration',
        ];
    }

    /**
     * Get or create payment record.
     */
    /**
 * Get or create payment record (FIXED - Only service fee).
 */
private function getOrCreatePayment($receipt)
{
    // Get service fee from admin settings (what gets charged)
    $serviceFee = $receipt->service_fee ?? config('services.app.receipt_generation_fee', 500);
    
    return Payment::firstOrCreate([
        'receipt_id' => $receipt->id,
        'user_id' => $receipt->user_id,
    ], [
        'reference' => 'PAY-' . strtoupper(Str::random(10)) . '-' . time(),
        'amount' => $serviceFee, // ONLY service fee, NOT receipt amount
        'currency' => 'NGN',
        'customer_email' => $receipt->customer_email ?: 'noemail@example.com',
        'customer_phone' => $receipt->customer_phone ?: '',
        'payment_method' => 'paystack',
        'gateway' => 'paystack',
        'status' => 'pending',
        'description' => $receipt->receipt_type === 'resale' ? 'Resale Processing Fee' : 'Receipt Generation Fee',
        'initiated_at' => now(),
        'metadata' => [
            'item_amount' => $receipt->amount, // For records only
            'service_fee' => $serviceFee, // What's being charged
            'receipt_number' => $receipt->receipt_number,
        ]
    ]);
}

    /**
     * Check if user can access receipt payment.
     */
    private function canAccessReceipt($receipt)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Receipt owner can access
            if ($receipt->user_id === $user->id) {
                return true;
            }
            
            // Admin can access
            if ($user->isAdmin()) {
                return true;
            }
            
            // Customer with matching details can access
            if ($receipt->customer_email === $user->email || 
                $receipt->customer_phone === $user->phone_number) {
                return true;
            }
        }
        
        return false;
    }
    /**
     * Get status badge HTML.
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'paid' => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning">Part Payment</span>',
            'pending' => '<span class="badge bg-secondary">Pending</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}