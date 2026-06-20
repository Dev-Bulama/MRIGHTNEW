<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Receipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'receipt_number',
        'shop_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_sex',
        'phone_name',
        'phone_color',
        'phone_serial_number',
        'phone_serial_confirmation',
        'amount',
        'amount_in_words',
        'payment_status',        
        'payment_notes', 
        'payment_updated_at',
        'resale_code',
        'resale_code_confirmation',
        'enable_antitheft',
        'receipt_type',
        'parent_receipt_id',
        'status',
        'service_fee',
        'payment_gateway_status',
        'payment_reference',
        'payment_confirmed_at',
        'notes',
        'metadata',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'enable_antitheft' => 'boolean',
        'metadata' => 'array',
        'payment_confirmed_at' => 'datetime',
        'generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shop that owns this receipt.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user (shop owner) who created this receipt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent receipt (for resale tracking).
     */
    public function parentReceipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'parent_receipt_id');
    }

    /**
     * Get child receipts (resales of this phone).
     */
    public function childReceipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'parent_receipt_id');
    }

    /**
     * Get the anti-theft phone record.
     */
    public function antiTheftPhone(): HasOne
    {
        return $this->hasOne(AntiTheftPhone::class, 'current_receipt_id');
    }

    /**
     * Get payments for this receipt.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get successful payment for this receipt.
     */
    public function successfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'successful');
    }

    /**
     * Generate unique receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        do {
            $number = 'MR-' . date('Y') . date('m') . '-' . strtoupper(Str::random(6));
        } while (self::where('receipt_number', $number)->exists());

        return $number;
    }

    /**
     * Generate unique resale code.
     */
    public static function generateResaleCode(): string
    {
        do {
            $code = strtoupper(Str::random(4));
        } while (self::where('resale_code', $code)->exists());

        return $code;
    }

    /**
     * Get formatted receipt number for display.
     */
    public function getFormattedReceiptNumberAttribute(): string
    {
        return 'Receipt No: ' . $this->receipt_number;
    }

    /**
     * Get formatted amount for display.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get customer full info.
     */
    public function getCustomerFullInfoAttribute(): string
    {
        return $this->customer_name . ' (' . ucfirst($this->customer_sex) . ')' . 
               "\nPhone: " . $this->customer_phone . 
               "\nEmail: " . $this->customer_email .
               "\nAddress: " . $this->customer_address;
    }

    /**
     * Get phone full info.
     */
    public function getPhoneFullInfoAttribute(): string
    {
        return $this->phone_name . ' (' . $this->phone_color . ')' .
               "\nSerial No: " . $this->phone_serial_number;
    }

    /**
     * Scope for new phone receipts.
     */
    public function scopeNewPhone($query)
    {
        return $query->where('receipt_type', 'new_phone');
    }

    /**
     * Scope for resale receipts.
     */
    public function scopeResale($query)
    {
        return $query->where('receipt_type', 'resale');
    }

    /**
     * Scope for active receipts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for paid receipts.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_gateway_status', 'successful');
    }

    /**
     * Scope for current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for current year.
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    /**
     * Scope for search by serial number.
     */
    public function scopeBySerialNumber($query, $serialNumber)
    {
        return $query->where('phone_serial_number', $serialNumber);
    }

    /**
     * Scope for search by resale code.
     */
    public function scopeByResaleCode($query, $resaleCode)
    {
        return $query->where('resale_code', $resaleCode);
    }

    /**
     * Check if receipt is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_gateway_status === 'successful';
    }

    /**
     * Check if receipt can be resold.
     */
    public function canBeResold(): bool
    {
        return $this->status === 'active' && $this->isPaid();
    }

    /**
     * Mark as transferred (when resold).
     */
    public function markAsTransferred(): void
    {
        $this->update(['status' => 'transferred']);
    }

    /**
     * Convert amount to words.
     */
   public static function convertAmountToWords($amount): string
{
    // Check if NumberFormatter class exists (requires PHP intl extension)
    if (class_exists('\NumberFormatter')) {
        try {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $words = $formatter->format($amount);
            return 'Naira ' . ucwords($words) . ' Only';
        } catch (\Exception $e) {
            // Fall back to manual conversion if NumberFormatter fails
        }
    }
    
    // Manual conversion fallback
    return self::convertAmountToWordsManual($amount);
}

/**
 * Manual conversion fallback for amount to words.
 */
private static function convertAmountToWordsManual($amount): string
{
    $amount = floor($amount);
    
    if ($amount == 0) return 'Zero Naira Only';
    
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    $words = '';
    
    if ($amount >= 1000000) {
        $millions = floor($amount / 1000000);
        $words .= self::convertHundreds($millions, $ones, $teens, $tens) . ' Million ';
        $amount %= 1000000;
    }
    
    if ($amount >= 1000) {
        $thousands = floor($amount / 1000);
        $words .= self::convertHundreds($thousands, $ones, $teens, $tens) . ' Thousand ';
        $amount %= 1000;
    }
    
    if ($amount > 0) {
        $words .= self::convertHundreds($amount, $ones, $teens, $tens);
    }
    
    return 'Naira ' . trim($words) . ' Only';
}

/**
 * Convert hundreds to words helper.
 */
private static function convertHundreds($number, $ones, $teens, $tens): string
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
            
            if (empty($receipt->resale_code)) {
                $receipt->resale_code = self::generateResaleCode();
                $receipt->resale_code_confirmation = $receipt->resale_code;
            }
            
            if (empty($receipt->amount_in_words) && $receipt->amount) {
                $receipt->amount_in_words = self::convertAmountToWords($receipt->amount);
            }
            
            if (empty($receipt->generated_at)) {
                $receipt->generated_at = now();
            }
        });

        static::created(function ($receipt) {
            // Update shop stats
            $receipt->shop->updateReceiptStats();
        });
    }

/**
 * Scope for paid receipts.
 */
// public function scopePaid($query)
// {
//     return $query->where('payment_status', 'paid');
// }

/**
 * Scope for partial payment receipts.
 */
public function scopePartial($query)
{
    return $query->where('payment_status', 'partial');
}

/**
 * Scope for pending payment receipts.
 */
public function scopePending($query)
{
    return $query->where('payment_status', 'pending');
}

/**
 * Get formatted payment status.
 */
public function getFormattedPaymentStatusAttribute()
{
    $statuses = [
        'paid' => 'Paid',
        'partial' => 'Part Payment',
        'pending' => 'Pending',
    ];
    
    return $statuses[$this->payment_status] ?? 'Unknown';
}

/**
 * Get payment status badge HTML.
 */
public function getPaymentStatusBadgeAttribute()
{
    $badges = [
        'paid' => '<span class="badge bg-success">Paid</span>',
        'partial' => '<span class="badge bg-warning">Part Payment</span>',
        'pending' => '<span class="badge bg-secondary">Pending</span>',
    ];
    
    return $badges[$this->payment_status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
}