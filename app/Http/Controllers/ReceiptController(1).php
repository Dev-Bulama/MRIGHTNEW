<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Shop;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReceiptController extends Controller
{
    /**
     * Display receipt listing for shop owner.
     */
    public function index(Request $request)
{
    $user = Auth::user();
    
    // Check if user has a shop
    if (!$user->shop || !$user->shop->approved) {
        return redirect()->route('shop.create')
            ->with('warning', 'Please complete shop registration and wait for approval to manage receipts.');
    }

    // Show only receipts where payment has been successfully processed
    $query = $user->receipts()->with(['shop', 'payments']);
    
    // Filter to show only paid receipts OR offline payments
    $query->where(function($q) {
        $q->where(function($subQuery) {
            // Online payments that are successful
            $subQuery->where('payment_status', 'paid')
                     ->where('payment_gateway_status', 'successful');
        })->orWhere(function($subQuery) {
            // Offline payments that are marked as paid
            $subQuery->where('payment_status', 'paid')
                     ->where('payment_gateway_status', 'offline');
        })->orWhere(function($subQuery) {
            // Legacy receipts without payment tracking (show them)
            $subQuery->whereNull('payment_gateway_status')
                     ->where('payment_status', 'paid');
        });
    });
    
    // Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('receipt_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('phone_name', 'like', "%{$search}%")
              ->orWhere('phone_serial_number', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $receipts = $query->latest()->paginate(15);
    
    // Get summary statistics (count all paid receipts)
    $allPaidReceipts = $user->receipts()
                           ->where(function($q) {
                               $q->where(function($subQuery) {
                                   $subQuery->where('payment_status', 'paid')
                                            ->where('payment_gateway_status', 'successful');
                               })->orWhere(function($subQuery) {
                                   $subQuery->where('payment_status', 'paid')
                                            ->where('payment_gateway_status', 'offline');
                               })->orWhere(function($subQuery) {
                                   $subQuery->whereNull('payment_gateway_status')
                                            ->where('payment_status', 'paid');
                               });
                           });
    
    $stats = [
        'total' => $allPaidReceipts->count(),
        'active' => $allPaidReceipts->where('status', 'active')->count(),
        'paid' => $allPaidReceipts->count(),
        'pending' => $user->receipts()->where('payment_status', 'pending')->count(),
        'monthly_total' => $allPaidReceipts->whereMonth('created_at', now()->month)->count(),
        'monthly_revenue' => $allPaidReceipts->whereMonth('created_at', now()->month)->sum('service_fee'),
    ];

    return view('receipt.index', compact('receipts', 'stats'));
}
/**
 * Display unpaid receipts for shop owner (for reference/retry payment).
 */
public function unpaidReceipts(Request $request)
{
    $user = Auth::user();
    
    // Check if user has a shop
    if (!$user->shop || !$user->shop->approved) {
        return redirect()->route('shop.create')
            ->with('warning', 'Please complete shop registration and wait for approval to manage receipts.');
    }

    // Only show receipts that have NOT been paid for
    $query = $user->receipts()
                  ->with(['shop', 'payments'])
                  ->where(function($q) {
                      $q->where('payment_status', 'pending')
                        ->orWhere('payment_status', 'failed')
                        ->orWhere(function($subQ) {
                            $subQ->where('payment_status', 'paid')
                                 ->where('payment_gateway_status', 'pending');
                        });
                  });
    
    // Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('receipt_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('phone_name', 'like', "%{$search}%")
              ->orWhere('phone_serial_number', 'like', "%{$search}%");
        });
    }

    $unpaidReceipts = $query->latest()->paginate(15);
    
    return view('receipt.unpaid', compact('unpaidReceipts'));
}
    /**
     * Show receipt creation form.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user has an approved shop
        if (!$user->shop || !$user->shop->approved) {
            return redirect()->route('shop.create')
                ->with('warning', 'Please complete shop registration and wait for approval to generate receipts.');
        }

        $shop = $user->shop;
        
        // Get next receipt number
        $nextReceiptNumber = $this->generateReceiptNumber($shop);
        
        return view('receipt.create', compact('shop', 'nextReceiptNumber'));
    }

    /**
     * Store new receipt.
     */
    /**
 * Store new receipt with payment integration.
 */
public function store(Request $request)
{
    $user = Auth::user();
    
    // Validate user has approved shop
    if (!$user->shop || !$user->shop->approved) {
        return redirect()->route('shop.create')
            ->with('error', 'Shop must be approved to generate receipts.');
    }

    $validated = $request->validate([
        'customer_name' => ['required', 'string', 'max:255'],
        'customer_phone' => ['required', 'string', 'max:20'],
        'customer_email' => ['nullable', 'email', 'max:255'],
        'customer_address' => ['nullable', 'string', 'max:500'],
        'customer_sex' => ['nullable', 'in:male,female,other'],
        'phone_name' => ['required', 'string', 'max:255'],
        'phone_color' => ['required', 'string', 'max:100'],
        'phone_serial_number' => ['required', 'string', 'max:255', 'unique:receipts,phone_serial_number'],
        'phone_serial_confirmation' => ['required', 'string', 'same:phone_serial_number'],
        'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
        'payment_status' => ['required', 'in:paid,pending,partial'],
        'resale_code' => ['nullable', 'string', 'max:100'],
        'resale_code_confirmation' => ['nullable', 'string', 'same:resale_code'],
        'enable_antitheft' => ['boolean'],
        'receipt_type' => ['required', 'in:sale,resale'],
        'parent_receipt_id' => ['nullable', 'exists:receipts,id'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_method' => ['required', 'in:paystack,offline'],
    ]);

    try {
        DB::beginTransaction();

        // Generate receipt number
        $receiptNumber = $this->generateReceiptNumber($user->shop);
        
        // Convert amount to words
        $amountInWords = $this->convertAmountToWords($validated['amount']);
        
        // Get service fee from admin settings
        $serviceFee = config('services.app.receipt_generation_fee', 500);

        // Create receipt
        $receipt = $user->receipts()->create([
            'shop_id' => $user->shop->id,
            'receipt_number' => $receiptNumber,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_sex' => $validated['customer_sex'] ?? null,
            'phone_name' => $validated['phone_name'],
            'phone_color' => $validated['phone_color'],
            'phone_serial_number' => $validated['phone_serial_number'],
            'phone_serial_confirmation' => $validated['phone_serial_number'],
            'amount' => $validated['amount'], // Item cost (for records)
            'amount_in_words' => $amountInWords,
            'payment_status' => 'pending', // Always start as pending
            'resale_code' => $validated['resale_code'] ? strtoupper($validated['resale_code']) : null,
            'resale_code_confirmation' => $validated['resale_code'] ? strtoupper($validated['resale_code']) : null,
            'enable_antitheft' => $validated['enable_antitheft'] ?? false,
            'receipt_type' => $validated['receipt_type'],
            'parent_receipt_id' => $validated['parent_receipt_id'] ?? null,
            'status' => 'active',
            'service_fee' => $serviceFee, // Admin-set fee (what gets charged)
            'payment_gateway_status' => 'pending',
            'notes' => $validated['notes'],
            'generated_at' => now(),
        ]);

        DB::commit();

        // If payment method is Paystack, redirect to payment
        if ($validated['payment_method'] === 'paystack') {
            return redirect()->route('payment.show', $receipt)
                ->with('success', 'Receipt created! Please complete payment to finalize.');
        }

        // If offline payment, mark as completed
        $receipt->update([
            'payment_status' => $validated['payment_status'],
            'payment_gateway_status' => 'offline',
        ]);

        // Update shop statistics
        $user->shop->increment('total_receipts_generated');

        return redirect()->route('receipt.show', $receipt)
            ->with('success', 'Receipt generated successfully! Receipt Number: ' . $receiptNumber);

    } catch (Exception $e) {
        DB::rollback();
        
        return back()->withInput()
            ->with('error', 'Failed to generate receipt: ' . $e->getMessage());
    }
}
    /**
     * Display receipt details.
     */
   public function show(Receipt $receipt)
{
    // Check authorization
    if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized access to receipt.');
    }

    // For shop owners, check if receipt payment has been completed
    if (!Auth::user()->isAdmin() && $receipt->user_id === Auth::id()) {
        $isPaid = $receipt->payment_status === 'paid' && 
                 ($receipt->payment_gateway_status === 'successful' || 
                  $receipt->payment_gateway_status === 'offline' ||
                  $receipt->payment_gateway_status === null); // Legacy receipts
        
        if (!$isPaid) {
            // Check if there's a payment in progress
            $hasPayment = $receipt->payments()->exists();
            
            if ($hasPayment) {
                return redirect()->route('payment.show', $receipt)
                    ->with('warning', 'Please complete payment to view this receipt.');
            } else {
                // No payment record exists, might be legacy receipt - allow viewing
                \Log::info('Showing receipt without payment record (legacy)', [
                    'receipt_id' => $receipt->id,
                    'payment_status' => $receipt->payment_status,
                    'gateway_status' => $receipt->payment_gateway_status
                ]);
            }
        }
    }

    $receipt->load(['shop', 'user', 'parentReceipt', 'childReceipts', 'payments']);
    
    return view('receipt.show', compact('receipt'));
}
    /**
     * Display receipt for viewing/printing.
     */
   public function view(Receipt $receipt)
{
    // Check authorization
    if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized access to receipt.');
    }

    // Load relationships including parent receipt for resale receipts
    $receipt->load(['shop', 'user', 'parentReceipt.shop', 'parentReceipt.user']);
    
    // Add phone masking helper method
    $receipt->masked_phone = $this->maskPhoneNumber($receipt->customer_phone);
    
    return view('receipt.view', compact('receipt'));
}

    /**
     * Download receipt as PDF.
     */
    // public function download(Receipt $receipt)
    // {
    //     // Check authorization
    //     if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
    //         abort(403, 'Unauthorized access to receipt.');
    //     }

    //     $receipt->load(['shop', 'user']);
        
    //     $pdf = Pdf::loadView('receipt.pdf', compact('receipt'))
    //               ->setPaper('a4', 'portrait')
    //               ->setOptions([
    //                   'dpi' => 150,
    //                   'defaultFont' => 'sans-serif',
    //                   'isHtml5ParserEnabled' => true,
    //                   'isRemoteEnabled' => true,
    //               ]);

    //     $filename = 'receipt-' . $receipt->receipt_number . '.pdf';
        
    //     return $pdf->download($filename);
    // }

    /**
     * Print receipt view.
     */
  /**
 * Print receipt view.
 */
public function print(Receipt $receipt)
{
    // Check authorization
    if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized access to receipt.');
    }

    $receipt->load(['shop', 'user']);
    
    return view('receipt.print', compact('receipt'));
}

    /**
     * Show resale form.
     */
    public function resaleForm()
    {
        $user = Auth::user();
        
        if (!$user->shop || !$user->shop->approved) {
            return redirect()->route('shop.create')
                ->with('warning', 'Please complete shop registration and wait for approval.');
        }

        return view('receipt.resale');
    }

    /**
     * Search for original receipt during resale process.
     */
    public function searchResale(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
            'resale_code' => ['required', 'string'],
        ]);

        try {
            // Find original receipt
            $originalReceipt = Receipt::where('phone_serial_number', $validated['serial_number'])
                                   ->where('resale_code', strtoupper($validated['resale_code']))
                                   ->with(['shop', 'user', 'childReceipts'])
                                   ->first();

            if (!$originalReceipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not found. Please verify the serial number and resale code.'
                ]);
            }

            // Check if phone is already resold
            if ($originalReceipt->childReceipts()->exists()) {
                $latestResale = $originalReceipt->childReceipts()->latest()->first();
                return response()->json([
                    'success' => false,
                    'message' => "This phone has already been resold to {$latestResale->customer_name} on " . $latestResale->created_at->format('M j, Y') . "."
                ]);
            }

            // Send notification to original owner
            $this->sendResaleSearchNotification($originalReceipt);

            return response()->json([
                'success' => true,
                'message' => 'Original receipt found! You can proceed with the resale.',
                'receipt' => [
                    'id' => $originalReceipt->id,
                    'receipt_number' => $originalReceipt->receipt_number,
                    'customer_name' => $originalReceipt->customer_name,
                    'customer_phone' => $originalReceipt->customer_phone,
                    'phone_name' => $originalReceipt->phone_name,
                    'phone_color' => $originalReceipt->phone_color,
                    'phone_serial_number' => $originalReceipt->phone_serial_number,
                    'original_amount' => $originalReceipt->amount,
                    'shop_name' => $originalReceipt->shop->name ?? 'Unknown',
                    'original_date' => $originalReceipt->created_at->format('M d, Y'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process resale receipt.
     */
    /**
 * Process resale with payment integration.
 */
public function processResale(Request $request)
{
    $validated = $request->validate([
        'original_serial_number' => ['required', 'string'],
        'resale_code' => ['required', 'string'],
        'new_customer_name' => ['required', 'string', 'max:255'],
        'new_customer_phone' => ['required', 'string', 'max:20'],
        'new_customer_email' => ['required', 'email', 'max:255'],
        'new_customer_address' => ['nullable', 'string', 'max:500'],
        'new_amount' => ['required', 'numeric', 'min:1'],
        'payment_status' => ['required', 'in:paid,pending,partial'],
        'new_resale_code' => ['required', 'string', 'min:4', 'max:20'],
        'new_resale_code_confirmation' => ['required', 'string', 'same:new_resale_code'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_method' => ['required', 'in:paystack,offline'],
    ]);

    try {
        // Find original receipt
        $originalReceipt = Receipt::where('phone_serial_number', $validated['original_serial_number'])
                                 ->where('resale_code', strtoupper($validated['resale_code']))
                                 ->first();

        if (!$originalReceipt) {
            return back()->withInput()
                ->with('error', 'Invalid serial number or resale code. Please verify and try again.');
        }

        // Check if phone is already resold
        if ($originalReceipt->childReceipts()->exists()) {
            return back()->withInput()
                ->with('error', 'This phone has already been resold.');
        }

        DB::beginTransaction();

        $user = Auth::user();
        $receiptNumber = $this->generateReceiptNumber($user->shop);
        $amountInWords = $this->convertAmountToWords($validated['new_amount']);
        
        // Get resale fee from admin settings
        $serviceFee = config('services.app.resale_fee', 300);

        // Create resale receipt
        $resaleReceipt = $user->receipts()->create([
            'shop_id' => $user->shop->id,
            'receipt_number' => $receiptNumber,
            'customer_name' => $validated['new_customer_name'],
            'customer_phone' => $validated['new_customer_phone'],
            'customer_email' => $validated['new_customer_email'],
            'customer_address' => $validated['new_customer_address'],
            'phone_name' => $originalReceipt->phone_name,
            'phone_color' => $originalReceipt->phone_color,
            'phone_serial_number' => $originalReceipt->phone_serial_number,
            'phone_serial_confirmation' => $originalReceipt->phone_serial_number,
            'amount' => $validated['new_amount'], // New sale amount (for records)
            'amount_in_words' => $amountInWords,
            'payment_status' => 'pending', // Always start as pending
            'resale_code' => strtoupper($validated['new_resale_code']),
            'resale_code_confirmation' => strtoupper($validated['new_resale_code']),
            'enable_antitheft' => $originalReceipt->enable_antitheft,
            'receipt_type' => 'resale',
            'parent_receipt_id' => $originalReceipt->id,
            'status' => 'active',
            'service_fee' => $serviceFee, // Admin-set resale fee (what gets charged)
            'payment_gateway_status' => 'pending',
            'notes' => $validated['notes'],
            'generated_at' => now(),
        ]);

        DB::commit();

        // If payment method is Paystack, redirect to payment
        if ($validated['payment_method'] === 'paystack') {
            return redirect()->route('payment.show', $resaleReceipt)
                ->with('success', 'Resale receipt created! Please complete payment to finalize.');
        }

        // If offline payment, mark as completed
        $resaleReceipt->update([
            'payment_status' => $validated['payment_status'],
            'payment_gateway_status' => 'offline',
        ]);

        // Update shop statistics
        $user->shop->increment('total_receipts_generated');

        return redirect()->route('receipt.show', $resaleReceipt)
            ->with('success', 'Resale receipt generated successfully! Receipt Number: ' . $receiptNumber);

    } catch (Exception $e) {
        DB::rollback();
        
        return back()->withInput()
            ->with('error', 'Failed to process resale: ' . $e->getMessage());
    }
}
/**
 * Process resale with payment integration.
 */
public function processResaleWithPayment(Request $request)
{
    $validated = $request->validate([
        'original_serial_number' => ['required', 'string'],
        'resale_code' => ['required', 'string'],
        'new_customer_name' => ['required', 'string', 'max:255'],
        'new_customer_phone' => ['required', 'string', 'max:20'],
        'new_customer_email' => ['required', 'email', 'max:255'],
        'new_customer_address' => ['nullable', 'string', 'max:500'],
        'new_amount' => ['required', 'numeric', 'min:1'],
        'payment_status' => ['required', 'in:paid,pending,partial'],
        'new_resale_code' => ['required', 'string', 'min:4', 'max:20'],
        'new_resale_code_confirmation' => ['required', 'string', 'same:new_resale_code'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_method' => ['required', 'in:paystack,offline'],
    ]);

    try {
        // Find original receipt
        $originalReceipt = Receipt::where('phone_serial_number', $validated['original_serial_number'])
                                 ->where('resale_code', strtoupper($validated['resale_code']))
                                 ->first();

        if (!$originalReceipt) {
            return back()->withInput()
                ->with('error', 'Invalid serial number or resale code. Please verify and try again.');
        }

        // Check if phone is already resold
        if ($originalReceipt->childReceipts()->exists()) {
            return back()->withInput()
                ->with('error', 'This phone has already been resold.');
        }

        DB::beginTransaction();

        $user = Auth::user();
        $receiptNumber = $this->generateReceiptNumber($user->shop);
        $amountInWords = $this->convertAmountToWords($validated['new_amount']);
        
        // Get resale fee from admin settings
        $serviceFee = config('services.app.resale_fee', 300);

        // Create resale receipt
        $resaleReceipt = $user->receipts()->create([
            'shop_id' => $user->shop->id,
            'receipt_number' => $receiptNumber,
            'customer_name' => $validated['new_customer_name'],
            'customer_phone' => $validated['new_customer_phone'],
            'customer_email' => $validated['new_customer_email'],
            'customer_address' => $validated['new_customer_address'],
            'phone_name' => $originalReceipt->phone_name,
            'phone_color' => $originalReceipt->phone_color,
            'phone_serial_number' => $originalReceipt->phone_serial_number,
            'phone_serial_confirmation' => $originalReceipt->phone_serial_number,
            'amount' => $validated['new_amount'], // New sale amount (for records)
            'amount_in_words' => $amountInWords,
            'payment_status' => 'pending', // Always start as pending
            'resale_code' => strtoupper($validated['new_resale_code']),
            'resale_code_confirmation' => strtoupper($validated['new_resale_code']),
            'enable_antitheft' => $originalReceipt->enable_antitheft,
            'receipt_type' => 'resale',
            'parent_receipt_id' => $originalReceipt->id,
            'status' => 'active',
            'service_fee' => $serviceFee, // Admin-set resale fee (what gets charged)
            'payment_gateway_status' => 'pending',
            'notes' => $validated['notes'],
            'generated_at' => now(),
        ]);

        DB::commit();

        // If payment method is Paystack, redirect to payment
        if ($validated['payment_method'] === 'paystack') {
            return redirect()->route('payment.show', $resaleReceipt)
                ->with('success', 'Resale receipt created! Please complete payment to finalize.');
        }

        // If offline payment, mark as completed and send email
        $resaleReceipt->update([
            'payment_status' => $validated['payment_status'],
            'payment_gateway_status' => 'offline',
        ]);

        // Send email notification for offline payments
        if ($validated['new_customer_email']) {
            try {
                $this->sendReceiptEmail($resaleReceipt);
                $emailMessage = ' Receipt copy sent to customer email.';
            } catch (Exception $e) {
                \Log::error('Failed to send resale receipt email: ' . $e->getMessage());
                $emailMessage = ' Email could not be sent.';
            }
        } else {
            $emailMessage = '';
        }

        // Update shop statistics
        $user->shop->increment('total_receipts_generated');

        return redirect()->route('receipt.show', $resaleReceipt)
            ->with('success', 'Resale receipt generated successfully! Receipt Number: ' . $receiptNumber . $emailMessage);

    } catch (Exception $e) {
        DB::rollback();
        
        return back()->withInput()
            ->with('error', 'Failed to process resale: ' . $e->getMessage());
    }
}

    /**
     * Update receipt status.
     */
    public function updateStatus(Request $request, Receipt $receipt)
    {
        // Check authorization
        if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,cancelled,void'],
            'payment_status' => ['nullable', 'in:paid,pending,partial,failed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $receipt->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receipt status updated successfully.',
            'receipt' => $receipt->fresh()
        ]);
    }

    /**
     * Get customer receipts.
     */
    public function customerReceipts(Request $request)
    {
        $user = Auth::user();
        
        // Get receipts where customer email or phone matches user
        $receipts = Receipt::where(function($query) use ($user) {
                               $query->where('customer_email', $user->email)
                                     ->orWhere('customer_phone', $user->phone_number);
                           })
                           ->with(['shop', 'user'])
                           ->latest()
                           ->paginate(15);

        return view('customer.receipts', compact('receipts'));
    }

    /**
     * Public receipt view (no auth required).
     */
    public function publicView($receiptNumber)
    {
        $receipt = Receipt::where('receipt_number', $receiptNumber)
                         ->with(['shop'])
                         ->firstOrFail();

        return view('receipt.public', compact('receipt'));
    }

    /**
     * Generate unique receipt number.
     */
    private function generateReceiptNumber(Shop $shop): string
    {
        $prefix = 'MR-' . strtoupper(substr($shop->shop_name, 0, 3));
        $date = now()->format('Ymd');
        
        // Get last receipt number for today
        $lastReceipt = Receipt::where('shop_id', $shop->id)
                            ->where('receipt_number', 'like', $prefix . $date . '%')
                            ->orderBy('receipt_number', 'desc')
                            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Convert amount to words.
     */
    private function convertAmountToWords(float $amount): string
{
    // Check if NumberFormatter class exists (requires PHP intl extension)
    if (class_exists('\NumberFormatter')) {
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $words = $formatter->format($amount);
        return 'Naira ' . ucwords($words) . ' Only';
    }
    
    // Fallback method without NumberFormatter
    return $this->convertAmountToWordsManual($amount);
}

/**
 * Manual conversion fallback for amount to words.
 */
private function convertAmountToWordsManual(float $amount): string
{
    $amount = floor($amount);
    
    if ($amount == 0) return 'Zero Naira Only';
    
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    $words = '';
    
    if ($amount >= 1000000) {
        $millions = floor($amount / 1000000);
        $words .= $this->convertHundreds($millions, $ones, $teens, $tens) . ' Million ';
        $amount %= 1000000;
    }
    
    if ($amount >= 1000) {
        $thousands = floor($amount / 1000);
        $words .= $this->convertHundreds($thousands, $ones, $teens, $tens) . ' Thousand ';
        $amount %= 1000;
    }
    
    if ($amount > 0) {
        $words .= $this->convertHundreds($amount, $ones, $teens, $tens);
    }
    
    return 'Naira ' . trim($words) . ' Only';
}

/**
 * Convert hundreds to words helper.
 */
private function convertHundreds($number, $ones, $teens, $tens): string
{
    $result = '';
    
    if ($number >= 100) {
        $result .= $ones[floor($number / 100)] . ' Hundred ';
        $number %= 100;
    }
    
    if ($number >= 20) {
        $result .= $tens[floor($number / 10)] . ' ';
        $number %= 10;
    } elseif ($number >= 10) {
        $result .= $teens[$number - 10] . ' ';
        return $result;
    }
    
    if ($number > 0) {
        $result .= $ones[$number] . ' ';
    }
    
    return $result;
}

    /**
     * Send receipt email to customer.
     */
  private function sendReceiptEmail(Receipt $receipt)
{
    if (!$receipt->customer_email) {
        \Log::info('No customer email provided for receipt: ' . $receipt->receipt_number);
        return false;
    }

    try {
        // Load receipt with all needed relationships
        $receipt->load(['shop', 'user', 'parentReceipt.shop', 'parentReceipt.user']);
        
        // Use the Mailable class (like original working version)
        Mail::to($receipt->customer_email)->send(new \App\Mail\ReceiptGenerated($receipt));

        \Log::info('Receipt email sent successfully', [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'customer_email' => $receipt->customer_email
        ]);

        return true;
    } catch (Exception $e) {
        \Log::error('Failed to send receipt email', [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number ?? 'unknown',
            'customer_email' => $receipt->customer_email ?? 'unknown',
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

    /**
     * Send receipt SMS to customer.
     */
    private function sendReceiptSMS(Receipt $receipt)
    {
        if (!$receipt->customer_phone) {
            return false;
        }

        try {
            $message = "Hello {$receipt->customer_name}, your digital receipt {$receipt->receipt_number} for {$receipt->phone_name} has been generated. Amount: ₦" . number_format($receipt->amount, 2) . ". View: " . route('public.receipt', $receipt->receipt_number);
            
            // Implement SMS sending logic here
            // This would typically integrate with SMS service like Twilio, Nexmo, etc.
            
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to send receipt SMS: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resend receipt email.
     */
    public function resendEmail(Receipt $receipt)
    {
        // Check authorization
        if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$receipt->customer_email) {
            return response()->json([
                'success' => false,
                'message' => 'No email address found for this receipt.'
            ]);
        }

        $sent = $this->sendReceiptEmail($receipt);

        return response()->json([
            'success' => $sent,
            'message' => $sent ? 'Email sent successfully.' : 'Failed to send email.'
        ]);
    }

    /**
     * Get receipt status (AJAX).
     */
    public function getStatus(Receipt $receipt)
    {
        // Check authorization
        if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        return response()->json([
            'success' => true,
            'receipt' => [
                'id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'status' => $receipt->status,
                'payment_status' => $receipt->payment_status,
                'payment_gateway_status' => $receipt->payment_gateway_status,
                'amount' => $receipt->amount,
                'created_at' => $receipt->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $receipt->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }
    /**
     * Download receipt as PDF.
     */
    public function download(Receipt $receipt)
    {
        // Check authorization
        if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to receipt.');
        }

        $receipt->load(['shop', 'user']);
        
        try {
            // Generate PDF using DomPDF
            $pdf = Pdf::loadView('receipt.pdf', compact('receipt'));
            
            $filename = 'receipt_' . $receipt->receipt_number . '.pdf';
            
            return $pdf->download($filename);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate PDF. Please try again.');
        }
    }
    /**
     * Public verification of receipt (no auth required).
     */
    public function publicVerify($receiptNumber)
    {
        $receipt = Receipt::where('receipt_number', $receiptNumber)
                         ->with(['shop', 'user'])
                         ->first();

        if (!$receipt) {
            return view('public.verify-result', [
                'found' => false,
                'receiptNumber' => $receiptNumber,
                'message' => 'Receipt not found or invalid receipt number.'
            ]);
        }

        return view('public.verify-result', [
            'found' => true,
            'receipt' => $receipt,
            'receiptNumber' => $receiptNumber,
            'message' => 'Receipt verified successfully!'
        ]);
    }
    /**
     * Search for original receipt by serial and resale code.
     */

    /**
     * Search for original receipt by serial and resale code (AJAX).
     */
    public function searchForResale(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'resale_code' => 'required|string',
        ]);

        try {
            $serialNumber = trim($request->serial_number);
            $resaleCode = strtoupper(trim($request->resale_code));

            // Search for the actual receipt with these exact criteria
            $originalReceipt = Receipt::where('phone_serial_number', $serialNumber)
                                   ->where('resale_code', $resaleCode)
                                   ->where('status', 'active')
                                   ->with(['shop', 'user'])
                                   ->first();

            if (!$originalReceipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'No receipt found with serial number "' . $serialNumber . '" and resale code "' . $resaleCode . '". Please verify both values are correct.'
                ]);
            }

            // Check if already resold
            $alreadyResold = Receipt::where('parent_receipt_id', $originalReceipt->id)->exists();
            
            if ($alreadyResold) {
                $resaleReceipt = Receipt::where('parent_receipt_id', $originalReceipt->id)->first();
                return response()->json([
                    'success' => false,
                    'message' => 'This phone has already been resold on ' . $resaleReceipt->created_at->format('M d, Y') . ' to ' . $resaleReceipt->customer_name . '. Each phone can only be resold once.'
                ]);
            }

            // Send notification email to original owner
            $this->sendResaleSearchNotification($originalReceipt);

           return response()->json([
    'success' => true,
    'receipt' => [
        'id' => $originalReceipt->id,
        'receipt_number' => $originalReceipt->receipt_number,
        'customer_name' => $originalReceipt->customer_name,
        'customer_phone' => $originalReceipt->customer_phone,
        'customer_email' => $originalReceipt->customer_email,
        'phone_name' => $originalReceipt->phone_name,
        'phone_color' => $originalReceipt->phone_color,
        'phone_serial_number' => $originalReceipt->phone_serial_number,
        'resale_code' => $originalReceipt->resale_code,
        'original_amount' => $originalReceipt->amount,
        'shop_name' => $originalReceipt->shop ? $originalReceipt->shop->shop_name : 'Unknown Shop',
        'original_date' => $originalReceipt->created_at->format('M d, Y'),
        'enable_antitheft' => $originalReceipt->enable_antitheft,
    ]
]);

        } catch (\Exception $e) {
            \Log::error('Resale search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed due to a system error. Please try again.'
            ]);
        }
    }

    /**
     * Send notification to original owner when resale code is searched.
     */
    /**
     * Send notification to original owner when resale code is searched.
     */
    private function sendResaleSearchNotification($originalReceipt)
{
    if (!$originalReceipt->customer_email) {
        \Log::info('No email address for original owner - notification skipped', [
            'receipt_id' => $originalReceipt->id,
            'receipt_number' => $originalReceipt->receipt_number
        ]);
        return;
    }

    try {
        $searchingShop = auth()->user()->shop;
        $searchTime = now();
        
        // Prepare email content
        $emailData = [
            'customer_name' => $originalReceipt->customer_name,
            'phone_name' => $originalReceipt->phone_name,
            'phone_serial' => $originalReceipt->phone_serial_number,
            'receipt_number' => $originalReceipt->receipt_number,
            'original_date' => $originalReceipt->created_at->format('M j, Y'),
            'searching_shop_name' => $searchingShop->shop_name ?? 'Unknown Shop',
            'searching_shop_address' => $searchingShop->address ?? 'Address not available',
            'searching_shop_phone' => $searchingShop->business_phone_1 ?? 'Phone not available',
            'search_time' => $searchTime->format('M j, Y g:i A'),
            'search_location' => request()->ip(),
        ];

        // Use existing receipt template for resale notification
        Mail::send('emails.receipt', ['receipt' => $originalReceipt, 'isResaleNotification' => true, 'searchData' => $emailData], function ($message) use ($originalReceipt) {
            $message->to($originalReceipt->customer_email, $originalReceipt->customer_name)
                    ->subject('🔍 Your Phone Resale Code is Being Searched - M-right Digital')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        \Log::info('Resale search notification sent successfully', [
            'receipt_id' => $originalReceipt->id,
            'customer_email' => $originalReceipt->customer_email,
            'searching_shop' => $searchingShop->shop_name ?? 'Unknown'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to send resale search notification', [
            'receipt_id' => $originalReceipt->id ?? 'unknown',
            'error' => $e->getMessage(),
            'customer_email' => $originalReceipt->customer_email ?? 'unknown'
        ]);
    }
}
    /**
 * Send resale code reminder to original owner.
 */
// public function forgotResaleCode(Request $request)
// {
//     $validated = $request->validate([
//         'phone_serial_number' => ['required', 'string'],
//         'customer_email' => ['required', 'email'],
//     ]);

//     try {
//         $receipt = Receipt::where('phone_serial_number', $validated['phone_serial_number'])
//                          ->where('customer_email', $validated['customer_email'])
//                          ->where('receipt_type', 'sale') // Only original receipts
//                          ->first();

//         if (!$receipt || !$receipt->resale_code) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'No receipt found with the provided details.'
//             ]);
//         }

//         // Mask the resale code (e.g., "Ti***5")
//         $maskedCode = $this->maskResaleCode($receipt->resale_code);

//         // Send reminder email
//         $this->sendResaleCodeReminder($receipt, $maskedCode);

//         return response()->json([
//             'success' => true,
//             'message' => 'A resale code reminder has been sent to your email with partial code: ' . $maskedCode
//         ]);

//     } catch (Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to send reminder. Please try again.'
//         ], 500);
//     }
// }

// /**
//  * Mask resale code for security (e.g., "ABC123" -> "Ab***3").
//  */
// private function maskResaleCode($code)
// {
//     $length = strlen($code);
    
//     if ($length <= 2) {
//         return str_repeat('*', $length);
//     }
    
//     $firstChar = substr($code, 0, 1);
//     $lastChar = substr($code, -1);
//     $middleStars = str_repeat('*', max(1, $length - 2));
    
//     return $firstChar . $middleStars . $lastChar;
// }

// /**
//  * Send resale code reminder email.
//  */
// private function sendResaleCodeReminder($receipt, $maskedCode)
// {
//     // Email content
//     $emailData = [
//         'customer_name' => $receipt->customer_name,
//         'phone_name' => $receipt->phone_name,
//         'phone_serial' => $receipt->phone_serial_number,
//         'receipt_number' => $receipt->receipt_number,
//         'masked_code' => $maskedCode,
//         'shop_name' => $receipt->shop->shop_name ?? 'M-right Digital',
//     ];

//     // Send email
//     Mail::send('emails.resale-code-reminder', $emailData, function ($message) use ($receipt) {
//         $message->to($receipt->customer_email, $receipt->customer_name)
//                 ->subject('🔐 Resale Code Reminder - M-right Digital')
//                 ->from(config('mail.from.address'), config('mail.from.name'));
//     });

//     Log::info('Resale code reminder sent', [
//         'receipt_id' => $receipt->id,
//         'customer_email' => $receipt->customer_email,
//         'masked_code' => $maskedCode,
//     ]);
// }
    /**
 * Send email notification to original owner when resale code is searched
 */
// private function sendResaleSearchNotification($originalReceipt)
// {
//     try {
//         if ($originalReceipt->customer_email) {
//             $currentShop = Auth::user()->shop;
            
//             Mail::to($originalReceipt->customer_email)->send(new \App\Mail\ResaleCodeSearchNotification(
//                 $originalReceipt,
//                 $currentShop
//             ));
            
//             \Log::info('Resale search notification sent to: ' . $originalReceipt->customer_email);
//         }
//     } catch (\Exception $e) {
//         \Log::error('Failed to send resale search notification: ' . $e->getMessage());
//         // Don't fail the main operation if email fails
//     }
// }

/**
 * Send receipt email notification
 */
// private function sendReceiptEmail($receipt)
// {
//     try {
//         if ($receipt->customer_email) {
//             Mail::to($receipt->customer_email)->send(new \App\Mail\ReceiptGenerated($receipt));
//             \Log::info('Receipt email sent to: ' . $receipt->customer_email);
//         }
//     } catch (\Exception $e) {
//         \Log::error('Failed to send receipt email: ' . $e->getMessage());
//     }
// }

/**
 * Send receipt SMS notification
 */
// private function sendReceiptSMS($receipt)
// {
//     try {
//         // SMS implementation would go here
//         \Log::info('SMS notification would be sent to: ' . $receipt->customer_phone);
//     } catch (\Exception $e) {
//         \Log::error('Failed to send receipt SMS: ' . $e->getMessage());
//     }
// }

/**
 * Convert amount to words
 */
// private function convertAmountToWords($amount)
// {
//     // Simple implementation - you can enhance this
//     return 'Nigerian Naira ' . number_format($amount, 2);
// }

/**
 * Generate unique receipt number
 */
// private function generateReceiptNumber($shop)
// {
//     $prefix = $shop ? strtoupper(substr($shop->shop_name ?? 'SHOP', 0, 3)) : 'RCP';
//     $timestamp = now()->format('YmdHis');
//     $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    
//     return $prefix . '-' . $timestamp . '-' . $random;
// }

/**
 * Update payment status via AJAX.
 */
public function updatePaymentStatus(Request $request, Receipt $receipt)
{
    // Check authorization
    if ($receipt->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access.'
        ], 403);
    }

    $request->validate([
        'payment_status' => 'required|in:paid,pending,partial'
    ]);

    try {
        // Update the receipt payment status
        $receipt->update([
            'payment_status' => $request->payment_status,
            'payment_gateway_status' => $request->payment_status === 'paid' ? 'successful' : 'pending',
            'updated_at' => now()
        ]);

        $statusText = $request->payment_status === 'paid' ? 'Paid in Full' : 
                     ($request->payment_status === 'partial' ? 'Part Payment' : 'Payment Pending');

        return response()->json([
            'success' => true,
            'message' => "Payment status updated to: {$statusText}",
            'new_status' => $request->payment_status
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update payment status: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Send resale code reminder to original owner.
 */
public function forgotResaleCode(Request $request)
{
    $validated = $request->validate([
        'phone_serial_number' => ['required', 'string'],
        'customer_email' => ['required', 'email'],
    ]);

    try {
        // Find the original receipt
        $receipt = Receipt::where('phone_serial_number', $validated['phone_serial_number'])
                         ->where('customer_email', $validated['customer_email'])
                         ->where('receipt_type', 'sale') // Only original receipts
                         ->first();

        if (!$receipt || !$receipt->resale_code) {
            return response()->json([
                'success' => false,
                'message' => 'No receipt found with the provided details or no resale code available.'
            ]);
        }

        // Check if phone was already resold
        if ($receipt->childReceipts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This phone has already been resold and the resale code is no longer valid.'
            ]);
        }

        // Mask the resale code (e.g., "ABC123" -> "A****3")
        $maskedCode = $this->maskResaleCode($receipt->resale_code);

        // Send reminder email
        $this->sendResaleCodeReminder($receipt, $maskedCode);

        // Log the request for security
        \Log::info('Resale code reminder requested', [
            'receipt_id' => $receipt->id,
            'customer_email' => $receipt->customer_email,
            'requester_shop' => auth()->user()->shop->shop_name ?? 'Unknown',
            'masked_code' => $maskedCode,
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'A resale code reminder has been sent to the original owner\'s email with partial code: ' . $maskedCode
        ]);

    } catch (Exception $e) {
        \Log::error('Forgot resale code error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send reminder. Please try again.'
        ], 500);
    }
}

/**
 * Mask resale code for security (e.g., "ABC123" -> "A****3").
 */
private function maskResaleCode($code)
{
    $length = strlen($code);
    
    if ($length <= 2) {
        return str_repeat('*', $length);
    }
    
    $firstChar = substr($code, 0, 1);
    $lastChar = substr($code, -1);
    $middleStars = str_repeat('*', max(1, $length - 2));
    
    return $firstChar . $middleStars . $lastChar;
}

/**
 * Send resale code reminder email.
 */
private function sendResaleCodeReminder($receipt, $maskedCode)
{
    try {
        // Email content
        $emailData = [
            'customer_name' => $receipt->customer_name,
            'phone_name' => $receipt->phone_name,
            'phone_serial' => $receipt->phone_serial_number,
            'receipt_number' => $receipt->receipt_number,
            'masked_code' => $maskedCode,
            'shop_name' => $receipt->shop->shop_name ?? 'M-right Digital',
        ];

        // Send email using Laravel Mail
        Mail::send('emails.resale-code-reminder', $emailData, function ($message) use ($receipt) {
            $message->to($receipt->customer_email, $receipt->customer_name)
                    ->subject('🔐 Resale Code Reminder - M-right Digital')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        \Log::info('Resale code reminder sent', [
            'receipt_id' => $receipt->id,
            'customer_email' => $receipt->customer_email,
            'masked_code' => $maskedCode,
        ]);

    } catch (Exception $e) {
        \Log::error('Failed to send resale code reminder email: ' . $e->getMessage());
        throw $e; // Re-throw to be caught by calling method
    }
}
/**
 * Mask phone number for privacy (show only last 3 digits).
 */
private function maskPhoneNumber($phoneNumber)
{
    if (!$phoneNumber) {
        return 'N/A';
    }
    
    // Remove any non-digit characters
    $cleanPhone = preg_replace('/\D/', '', $phoneNumber);
    
    // If phone is too short, just mask it completely
    if (strlen($cleanPhone) < 7) {
        return str_repeat('*', strlen($cleanPhone));
    }
    
    // Show format like: 080123***45 (mask middle digits, show first 6 and last 2)
    if (strlen($cleanPhone) >= 10) {
        $masked = substr($cleanPhone, 0, 6) . str_repeat('*', strlen($cleanPhone) - 8) . substr($cleanPhone, -2);
        
        // Format Nigerian numbers
        if (strlen($cleanPhone) == 11 && $cleanPhone[0] == '0') {
            return substr($masked, 0, 4) . ' ' . substr($masked, 4, 3) . ' ' . substr($masked, 7);
        }
        
        return $masked;
    }
    
    // Fallback for shorter numbers
    return substr($cleanPhone, 0, 3) . str_repeat('*', strlen($cleanPhone) - 5) . substr($cleanPhone, -2);
}







}