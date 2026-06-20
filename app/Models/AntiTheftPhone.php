<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AntiTheftPhone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'serial_number',
        'phone_model',
        'phone_brand',
        'phone_color',
        'status',
        'current_receipt_id',
        'current_owner_name',
        'current_owner_phone',
        'external_api_id',
        'last_api_sync',
        'api_response_data',
        'status_history',
        'notes',
        'registered_at',
        'last_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'api_response_data' => 'array',
        'status_history' => 'array',
        'last_api_sync' => 'datetime',
        'registered_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Available status options.
     */
    const STATUS_AWAITING_VERIFICATION = 'awaiting_ownership_verification';
    const STATUS_IN_USE = 'in_use';
    const STATUS_REPORTED_STOLEN = 'reported_stolen';
    const STATUS_RECOVERED = 'recovered';
    const STATUS_BLACKLISTED = 'blacklisted';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_AWAITING_VERIFICATION => 'Awaiting Ownership Verification',
            self::STATUS_IN_USE => 'In Use',
            self::STATUS_REPORTED_STOLEN => 'Reported Stolen',
            self::STATUS_RECOVERED => 'Recovered',
            self::STATUS_BLACKLISTED => 'Blacklisted',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Get the current receipt (owner).
     */
    public function currentReceipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'current_receipt_id');
    }

    /**
     * Get formatted phone info.
     */
    public function getPhoneInfoAttribute(): string
    {
        return $this->phone_brand . ' ' . $this->phone_model . ' (' . $this->phone_color . ')';
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AWAITING_VERIFICATION => 'warning',
            self::STATUS_IN_USE => 'success',
            self::STATUS_REPORTED_STOLEN => 'danger',
            self::STATUS_RECOVERED => 'info',
            self::STATUS_BLACKLISTED => 'dark',
            self::STATUS_INACTIVE => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Check if phone can be used for receipt generation.
     */
    public function canGenerateReceipt(): bool
    {
        return $this->status === self::STATUS_AWAITING_VERIFICATION;
    }

    /**
     * Check if phone is available for new ownership.
     */
    public function isAvailableForOwnership(): bool
    {
        return in_array($this->status, [
            self::STATUS_AWAITING_VERIFICATION,
            self::STATUS_RECOVERED
        ]);
    }

    /**
     * Check if phone is stolen or blacklisted.
     */
    public function isStolenOrBlacklisted(): bool
    {
        return in_array($this->status, [
            self::STATUS_REPORTED_STOLEN,
            self::STATUS_BLACKLISTED
        ]);
    }

    /**
     * Update status with history tracking.
     */
    public function updateStatus(string $newStatus, string $reason = null, $updatedBy = null): void
    {
        $oldStatus = $this->status;
        
        // Add to status history
        $history = $this->status_history ?? [];
        $history[] = [
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'reason' => $reason,
            'updated_by' => $updatedBy,
            'updated_at' => now()->toISOString(),
        ];

        $this->update([
            'status' => $newStatus,
            'status_history' => $history,
            'last_verified_at' => now(),
        ]);
    }

    /**
     * Update ownership information.
     */
    public function updateOwnership(Receipt $receipt): void
    {
        $this->update([
            'current_receipt_id' => $receipt->id,
            'current_owner_name' => $receipt->customer_name,
            'current_owner_phone' => $receipt->customer_phone,
            'status' => self::STATUS_IN_USE,
            'last_verified_at' => now(),
        ]);
    }

    /**
     * Clear ownership (when phone is resold).
     */
    public function clearOwnership(): void
    {
        $this->update([
            'current_receipt_id' => null,
            'current_owner_name' => null,
            'current_owner_phone' => null,
            'status' => self::STATUS_AWAITING_VERIFICATION,
        ]);
    }

    /**
     * Update API sync information.
     */
    public function updateApiSync(array $responseData, string $externalId = null): void
    {
        $updateData = [
            'api_response_data' => $responseData,
            'last_api_sync' => now(),
        ];

        if ($externalId) {
            $updateData['external_api_id'] = $externalId;
        }

        $this->update($updateData);
    }

    /**
     * Check if API sync is recent (within last hour).
     */
    public function hasRecentApiSync(): bool
    {
        return $this->last_api_sync && 
               $this->last_api_sync->greaterThan(now()->subHour());
    }

    /**
     * Scope for phones awaiting verification.
     */
    public function scopeAwaitingVerification($query)
    {
        return $query->where('status', self::STATUS_AWAITING_VERIFICATION);
    }

    /**
     * Scope for phones in use.
     */
    public function scopeInUse($query)
    {
        return $query->where('status', self::STATUS_IN_USE);
    }

    /**
     * Scope for stolen phones.
     */
    public function scopeStolen($query)
    {
        return $query->where('status', self::STATUS_REPORTED_STOLEN);
    }

    /**
     * Scope for blacklisted phones.
     */
    public function scopeBlacklisted($query)
    {
        return $query->where('status', self::STATUS_BLACKLISTED);
    }

    /**
     * Scope for phones by serial number.
     */
    public function scopeBySerialNumber($query, $serialNumber)
    {
        return $query->where('serial_number', $serialNumber);
    }

    /**
     * Scope for phones needing API sync.
     */
    public function scopeNeedsApiSync($query)
    {
        return $query->where(function($q) {
            $q->whereNull('last_api_sync')
              ->orWhere('last_api_sync', '<', now()->subHours(6));
        });
    }

    /**
     * Find phone by serial number or create new record.
     */
    public static function findOrCreateBySerial(string $serialNumber, array $phoneData = []): self
    {
        return self::firstOrCreate(
            ['serial_number' => $serialNumber],
            array_merge([
                'phone_model' => $phoneData['phone_model'] ?? 'Unknown',
                'phone_brand' => $phoneData['phone_brand'] ?? 'Unknown',
                'phone_color' => $phoneData['phone_color'] ?? 'Unknown',
                'status' => self::STATUS_AWAITING_VERIFICATION,
                'registered_at' => now(),
            ], $phoneData)
        );
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($phone) {
            if (empty($phone->registered_at)) {
                $phone->registered_at = now();
            }
        });
    }
}