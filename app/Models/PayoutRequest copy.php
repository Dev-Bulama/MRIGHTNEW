<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'shop_id',
        'amount_requested',
        'amount_approved',
        'commission_balance',
        'status',
        'reason',
        'admin_notes',
        'rejection_reason',
        'bank_name',
        'account_number',
        'account_name',
        'approved_by',
        'rejected_by',
        'paid_by',
        'approved_at',
        'rejected_at',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'commission_balance' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Minimum payout amount
     */
    public static function getMinimumPayoutAmount(): float
    {
        return 5000.00; // ₦5,000 minimum
    }

    /**
     * Maximum payout percentage (of available balance)
     */
    public static function getMaximumPayoutPercentage(): float
    {
        return 0.90; // 90% of available balance
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber(): string
    {
        $prefix = 'PR-' . date('Ymd') . '-';
        $lastRequest = self::where('request_number', 'like', $prefix . '%')
                          ->orderBy('request_number', 'desc')
                          ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Approve payout request
     */
    public function approve(User $approvedBy, ?float $approvedAmount = null, ?string $notes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'amount_approved' => $approvedAmount ?? $this->amount_requested,
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Reject payout request
     */
    public function reject(User $rejectedBy, string $reason): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_by' => $rejectedBy->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(User $paidBy, string $paymentReference): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_PAID,
            'paid_by' => $paidBy->id,
            'paid_at' => now(),
            'payment_reference' => $paymentReference,
        ]);

        return true;
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_PAID => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request can be marked as paid
     */
    public function canBeMarkedAsPaid(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountRequestedAttribute(): string
    {
        return '₦' . number_format($this->amount_requested, 2);
    }

    public function getFormattedAmountApprovedAttribute(): string
    {
        return $this->amount_approved ? '₦' . number_format($this->amount_approved, 2) : 'N/A';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Calculate available payout amount for a shop
     */
    public static function getAvailablePayoutAmount(Shop $shop): float
    {
        // Get total commission earned
        $totalCommission = $shop->total_commission_earned ?? 0;
        
        // Subtract already paid amounts
        $totalPaid = self::where('shop_id', $shop->id)
                        ->whereIn('status', [self::STATUS_PAID])
                        ->sum('amount_approved');
        
        // Subtract pending/approved amounts
        $totalPending = self::where('shop_id', $shop->id)
                           ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
                           ->sum('amount_requested');

        return max(0, $totalCommission - $totalPaid - $totalPending);
    }

    /**
     * Validate payout request amount
     */
    public static function validatePayoutAmount(Shop $shop, float $requestedAmount): array
    {
        $errors = [];
        $available = self::getAvailablePayoutAmount($shop);
        $minimum = self::getMinimumPayoutAmount();
        $maxPercentage = self::getMaximumPayoutPercentage();
        $maxAmount = $available * $maxPercentage;

        if ($requestedAmount < $minimum) {
            $errors[] = "Minimum payout amount is ₦" . number_format($minimum, 2);
        }

        if ($requestedAmount > $available) {
            $errors[] = "Requested amount exceeds available balance of ₦" . number_format($available, 2);
        }

        if ($requestedAmount > $maxAmount) {
            $errors[] = "Maximum payout is " . ($maxPercentage * 100) . "% of available balance (₦" . number_format($maxAmount, 2) . ")";
        }

        return $errors;
    }
    /**
 * Scopes for filtering payout requests
 */
// public function scopePending($query)
// {
//     return $query->where('status', self::STATUS_PENDING);
// }

// public function scopeApproved($query)
// {
//     return $query->where('status', self::STATUS_APPROVED);
// }

// public function scopePaid($query)
// {
//     return $query->where('status', self::STATUS_PAID);
// }

// public function scopeRejected($query)
// {
//     return $query->where('status', self::STATUS_REJECTED);
// }

public function scopeRecent($query)
{
    return $query->where('created_at', '>=', now()->subDays(30));
}

/**
 * Helper method to get status options
 */
public static function statuses(): array
{
    return self::getStatuses();
}
}