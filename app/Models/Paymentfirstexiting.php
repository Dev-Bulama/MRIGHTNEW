<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'reference',
        'user_id',
        'receipt_id',
        'amount',
        'currency',
        'payment_method',
        'gateway',
        'paystack_reference',
        'paystack_access_code',
        'paystack_response',
        'authorization_code',
        'card_type',
        'last4',
        'exp_month',
        'exp_year',
        'bank',
        'status',
        'initiated_at',
        'completed_at',
        'failed_at',
        'failure_reason',
        'customer_email',
        'customer_phone',
        'description',
        'metadata',
        'refund_amount',
        'refunded_at',
        'refund_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paystack_response' => 'array',
        'metadata' => 'array',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        
    ];

    /**
     * Payment status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Service fee amount.
     */
    const SERVICE_FEE = 500.00;

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SUCCESSFUL => 'Successful',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    /**
     * Get the user who made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the receipt this payment is for.
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * Generate unique transaction ID.
     */
    public static function generateTransactionId(): string
    {
        do {
            $id = 'TXN-' . date('YmdHis') . '-' . strtoupper(Str::random(6));
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }

    /**
     * Generate unique payment reference.
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'MR-' . time() . '-' . strtoupper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get formatted amount for display.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucwords($this->status);
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_SUCCESSFUL => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_REFUNDED => 'dark',
            default => 'primary'
        };
    }

    /**
     * Get payment method display name.
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'card' => 'Debit/Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'ussd' => 'USSD',
            'qr' => 'QR Code',
            'mobile_money' => 'Mobile Money',
            default => ucwords(str_replace('_', ' ', $this->payment_method ?? 'Unknown'))
        };
    }

    /**
     * Get card info if available.
     */
    public function getCardInfoAttribute(): ?string
    {
        if ($this->card_type && $this->last4) {
            return ucfirst($this->card_type) . ' ending in ' . $this->last4;
        }
        
        return null;
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if payment failed.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if payment can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isSuccessful() && $this->refund_amount == 0;
    }

    /**
     * Mark payment as successful.
     */
    public function markAsSuccessful(array $paystackData = []): void
    {
        $updateData = [
            'status' => self::STATUS_SUCCESSFUL,
            'completed_at' => now(),
        ];

        if (!empty($paystackData)) {
            $updateData['paystack_response'] = $paystackData;
            
            // Extract useful information from Paystack response
            if (isset($paystackData['authorization'])) {
                $auth = $paystackData['authorization'];
                $updateData['authorization_code'] = $auth['authorization_code'] ?? null;
                $updateData['card_type'] = $auth['card_type'] ?? null;
                $updateData['last4'] = $auth['last4'] ?? null;
                $updateData['exp_month'] = $auth['exp_month'] ?? null;
                $updateData['exp_year'] = $auth['exp_year'] ?? null;
                $updateData['bank'] = $auth['bank'] ?? null;
            }
            
            if (isset($paystackData['channel'])) {
                $updateData['payment_method'] = $paystackData['channel'];
            }
        }

        $this->update($updateData);

        // Update related receipt payment status
        if ($this->receipt) {
            $this->receipt->update([
                'payment_gateway_status' => 'successful',
                'payment_confirmed_at' => now(),
                'payment_reference' => $this->reference,
            ]);
        }
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $reason = null, array $paystackData = []): void
    {
        $updateData = [
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'failure_reason' => $reason,
        ];

        if (!empty($paystackData)) {
            $updateData['paystack_response'] = $paystackData;
        }

        $this->update($updateData);

        // Update related receipt payment status
        if ($this->receipt) {
            $this->receipt->update([
                'payment_gateway_status' => 'failed',
            ]);
        }
    }

    /**
     * Process refund.
     */
    public function processRefund(float $amount, string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'refund_amount' => $amount,
            'refunded_at' => now(),
            'refund_reason' => $reason,
        ]);
    }

    /**
     * Scope for successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Scope for payments by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for payments by reference.
     */
    public function scopeByReference($query, $reference)
    {
        return $query->where('reference', $reference)
                    ->orWhere('paystack_reference', $reference);
    }

    /**
     * Scope for today's payments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this month's payments.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
            
            if (empty($payment->reference)) {
                $payment->reference = self::generateReference();
            }
            
            if (empty($payment->initiated_at)) {
                $payment->initiated_at = now();
            }
            
            if (empty($payment->amount)) {
                $payment->amount = self::SERVICE_FEE;
            }
            
            if (empty($payment->currency)) {
                $payment->currency = 'NGN';
            }
            
            if (empty($payment->gateway)) {
                $payment->gateway = 'paystack';
            }
        });
    }
}