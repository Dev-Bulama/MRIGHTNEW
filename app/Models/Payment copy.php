<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'receipt_id',
        'reference',
        'amount',
        'service_fee',
        'currency',
        'payment_method',
        'status',
        'customer_email',
        'customer_phone',
        'gateway_reference',
        'gateway_response',
        'paid_at',
        'failed_at',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Payment status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Payment method constants.
     */
    const METHOD_PAYSTACK = 'paystack';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';
    const METHOD_POS = 'pos';

    /**
     * Currency constants.
     */
    const CURRENCY_NGN = 'NGN';
    const CURRENCY_USD = 'USD';
    const CURRENCY_GHS = 'GHS';

    /**
     * Get all available payment statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESSFUL => 'Successful',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    /**
     * Get all available payment methods.
     */
    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_PAYSTACK => 'Paystack (Card/Bank)',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_CASH => 'Cash',
            self::METHOD_POS => 'POS Terminal',
        ];
    }

    /**
     * Get all available currencies.
     */
    public static function getCurrencies(): array
    {
        return [
            self::CURRENCY_NGN => 'Nigerian Naira (₦)',
            self::CURRENCY_USD => 'US Dollar ($)',
            self::CURRENCY_GHS => 'Ghanaian Cedi (₵)',
        ];
    }

    /**
     * Get the user that made this payment.
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
     * Scope for successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for payments by method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Get payment status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucwords($this->status);
    }

    /**
     * Get payment method display name.
     */
    public function getMethodDisplayAttribute(): string
    {
        return self::getPaymentMethods()[$this->payment_method] ?? ucwords(str_replace('_', ' ', $this->payment_method));
    }

    /**
     * Get currency display name.
     */
    public function getCurrencyDisplayAttribute(): string
    {
        return self::getCurrencies()[$this->currency] ?? $this->currency;
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SUCCESSFUL => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_REFUNDED => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = match($this->currency) {
            'NGN' => '₦',
            'USD' => '$',
            'GHS' => '₵',
            default => $this->currency . ' '
        };

        return $symbol . number_format($this->amount, 2);
    }

    /**
     * Get formatted service fee with currency symbol.
     */
    public function getFormattedServiceFeeAttribute(): string
    {
        $symbol = match($this->currency) {
            'NGN' => '₦',
            'USD' => '$',
            'GHS' => '₵',
            default => $this->currency . ' '
        };

        return $symbol . number_format($this->service_fee ?? 0, 2);
    }

    /**
     * Get net amount (amount - service fee).
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - ($this->service_fee ?? 0);
    }

    /**
     * Get formatted net amount.
     */
    public function getFormattedNetAmountAttribute(): string
    {
        $symbol = match($this->currency) {
            'NGN' => '₦',
            'USD' => '$',
            'GHS' => '₵',
            default => $this->currency . ' '
        };

        return $symbol . number_format($this->net_amount, 2);
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
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment has failed.
     */
    public function hasFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if payment can be retried.
     */
    public function canBeRetried(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if payment is refundable.
     */
    public function isRefundable(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL && 
               $this->payment_method === self::METHOD_PAYSTACK &&
               $this->created_at->diffInDays() <= 30; // 30 days refund policy
    }

    /**
     * Get gateway response data safely.
     */
    public function getGatewayData($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->gateway_response ?? [];
        }

        return data_get($this->gateway_response, $key, $default);
    }

    /**
     * Get payment processing time.
     */
    public function getProcessingTimeAttribute(): ?int
    {
        if ($this->paid_at) {
            return $this->created_at->diffInMinutes($this->paid_at);
        }

        return null;
    }

    /**
     * Get human readable processing time.
     */
    public function getHumanProcessingTimeAttribute(): ?string
    {
        $minutes = $this->processing_time;
        
        if (is_null($minutes)) {
            return null;
        }

        if ($minutes < 1) {
            return 'Less than a minute';
        } elseif ($minutes < 60) {
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            $timeString = $hours . ' hour' . ($hours > 1 ? 's' : '');
            
            if ($remainingMinutes > 0) {
                $timeString .= ' and ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
            }
            
            return $timeString;
        }
    }

    /**
     * Mark payment as successful.
     */
    public function markAsSuccessful($gatewayData = null): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESSFUL,
            'paid_at' => now(),
            'gateway_response' => $gatewayData ?? $this->gateway_response,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed($reason = null, $gatewayData = null): void
    {
        $updateData = [
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        if ($gatewayData) {
            $updateData['gateway_response'] = $gatewayData;
        }

        $this->update($updateData);
    }

    /**
     * Cancel payment.
     */
    public function cancel($reason = null): void
    {
        $updateData = [
            'status' => self::STATUS_CANCELLED,
            'failed_at' => now(),
        ];

        if ($reason) {
            $updateData['notes'] = $reason;
        }

        $this->update($updateData);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate reference if not provided
        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'PAY-' . strtoupper(uniqid()) . '-' . time();
            }
        });

        // Update receipt payment status when payment status changes
        static::updated(function ($payment) {
            if ($payment->isDirty('status') && $payment->receipt) {
                $receiptStatus = match($payment->status) {
                    self::STATUS_SUCCESSFUL => 'paid',
                    self::STATUS_FAILED, self::STATUS_CANCELLED => 'failed',
                    default => 'pending'
                };

                $payment->receipt->update([
                    'payment_status' => $receiptStatus,
                    'payment_gateway_status' => $payment->status,
                    'payment_reference' => $payment->reference,
                    'payment_confirmed_at' => $payment->status === self::STATUS_SUCCESSFUL ? now() : null,
                ]);
            }
        });
    }
}