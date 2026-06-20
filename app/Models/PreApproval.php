<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PreApproval extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'state',
        'local_government',
        'shop_name',
        'business_address',
        'approval_code',
        'status',
        'expires_at',
        'used_at',
        'used_by',
        'created_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($preApproval) {
            if (empty($preApproval->approval_code)) {
                $preApproval->approval_code = static::generateApprovalCode();
            }
            
            if (empty($preApproval->expires_at)) {
                $preApproval->expires_at = Carbon::now()->addMonths(6);
            }
        });

        static::saving(function ($preApproval) {
            // Auto-update status based on conditions
            if ($preApproval->used_at && $preApproval->status !== self::STATUS_USED) {
                $preApproval->status = self::STATUS_USED;
            } elseif ($preApproval->expires_at && $preApproval->expires_at->isPast() && $preApproval->status === self::STATUS_PENDING) {
                $preApproval->status = self::STATUS_EXPIRED;
            }
        });
    }

    /**
     * Generate a unique approval code
     */
    public static function generateApprovalCode($prefix = 'PA')
    {
        do {
            $code = $prefix . '-' . strtoupper(uniqid());
        } while (static::where('approval_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who created this pre-approval
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who used this pre-approval
     */
    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Scope for pending pre-approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for used pre-approvals
     */
    public function scopeUsed($query)
    {
        return $query->where('status', self::STATUS_USED);
    }

    /**
     * Scope for expired pre-approvals
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope for valid (not expired and not used) pre-approvals
     */
    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope for specific state
     */
    public function scopeInState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope for specific LGA
     */
    public function scopeInLga($query, $lga)
    {
        return $query->where('local_government', $lga);
    }

    /**
     * Check if pre-approval is valid
     */
    public function isValid()
    {
        return $this->status === self::STATUS_PENDING && 
               $this->expires_at && 
               $this->expires_at->isFuture();
    }

    /**
     * Check if pre-approval is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if pre-approval is used
     */
    public function isUsed()
    {
        return $this->status === self::STATUS_USED;
    }

    /**
     * Mark as used
     */
    public function markAsUsed($userId = null)
    {
        $this->update([
            'status' => self::STATUS_USED,
            'used_at' => Carbon::now(),
            'used_by' => $userId ?: auth()->id(),
        ]);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_USED => 'success',
            self::STATUS_EXPIRED => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return $this->expires_at->diffInDays(Carbon::now(), false);
    }

    /**
     * Check if pre-approval can be used for specific email and phone
     */
    public static function canBeUsedFor($email, $phone)
    {
        return static::where(function($query) use ($email, $phone) {
            $query->where('email', $email)
                  ->orWhere('phone_number', $phone);
        })
        ->where('status', self::STATUS_PENDING)
        ->where('expires_at', '>', Carbon::now())
        ->exists();
    }

    /**
     * Find valid pre-approval for user
     */
    public static function findValidForUser($email, $phone = null)
    {
        $query = static::where('status', self::STATUS_PENDING)
                      ->where('expires_at', '>', Carbon::now());

        if ($phone) {
            $query->where(function($q) use ($email, $phone) {
                $q->where('email', $email)
                  ->orWhere('phone_number', $phone);
            });
        } else {
            $query->where('email', $email);
        }

        return $query->first();
    }

    /**
     * Auto-expire old pre-approvals
     */
    public static function expireOldPreApprovals()
    {
        return static::where('status', self::STATUS_PENDING)
                    ->where('expires_at', '<', Carbon::now())
                    ->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Get statistics for specific user/union
     */
    public static function getStatsForUser($userId)
    {
        $baseQuery = static::where('created_by', $userId);

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', self::STATUS_PENDING)->count(),
            'used' => (clone $baseQuery)->where('status', self::STATUS_USED)->count(),
            'expired' => (clone $baseQuery)->where('status', self::STATUS_EXPIRED)->count(),
            'expiring_soon' => (clone $baseQuery)
                ->where('status', self::STATUS_PENDING)
                ->where('expires_at', '<=', Carbon::now()->addDays(30))
                ->count(),
        ];
    }

    /**
     * Search pre-approvals
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%")
              ->orWhere('approval_code', 'like', "%{$search}%")
              ->orWhere('shop_name', 'like', "%{$search}%");
        });
    }

    /**
     * Get recent activity
     */
    public function scopeRecentActivity($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days))
                    ->orWhere('used_at', '>=', Carbon::now()->subDays($days));
    }
}