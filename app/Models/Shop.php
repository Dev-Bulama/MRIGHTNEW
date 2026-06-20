<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shop_name',
        'owner_full_name',
        'business_address',
        'business_phone_1',
        'business_phone_2',
        'business_email',
        'country',
        'state',
        'local_government',
        'logo',
        'terms_and_conditions',
        'additional_info',
        'status',
        'approved',
        'approved_at',
        'approved_by',
        'total_receipts_generated',
        'total_commission_earned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_info' => 'array',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
        'total_commission_earned' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the shop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the shop.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all receipts for this shop.
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Get active receipts for this shop.
     */
    public function activeReceipts(): HasMany
    {
        return $this->receipts()->where('status', 'active');
    }

    /**
     * Get receipts for current month.
     */
    public function currentMonthReceipts(): HasMany
    {
        return $this->receipts()->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year);
    }

    /**
     * Get receipts for current year.
     */
    public function currentYearReceipts(): HasMany
    {
        return $this->receipts()->whereYear('created_at', now()->year);
    }

    /**
     * Get the shop's logo URL.
     */
    // public function getLogoUrlAttribute(): string
    // {
    //     if ($this->logo && Storage::disk('public')->exists($this->logo)) {
    //         return Storage::url($this->logo);
    //     }
        
    //     return asset('assets/images/default-shop-logo.png');
    // }

    /**
     * Get formatted business address.
     */
    public function getFormattedAddressAttribute(): string
    {
        return $this->business_address . ', ' . $this->local_government . ', ' . 
               $this->state . ', ' . $this->country;
    }

    /**
     * Get receipt header for this shop.
     */
    public function getReceiptHeaderAttribute(): string
    {
        $phones = $this->business_phone_2 ? 
                 $this->business_phone_1 . ', ' . $this->business_phone_2 : 
                 $this->business_phone_1;
                 
        return $this->shop_name . "\n" .
               $this->formatted_address . "\n" .
               "Phone No: " . $phones;
    }

    /**
     * Scope for approved shops.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope for pending approval shops.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approved', false)->where('status', 'pending_approval');
    }

    /**
     * Scope for shops by state.
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope for shops by local government.
     */
    public function scopeByLocalGovernment($query, $lga)
    {
        return $query->where('local_government', $lga);
    }

    /**
     * Update total receipts and commission.
     */
    public function updateReceiptStats(): void
    {
        $totalReceipts = $this->receipts()->count();
        $totalCommission = $totalReceipts * 50; // ₦50 commission per receipt
        
        $this->update([
            'total_receipts_generated' => $totalReceipts,
            'total_commission_earned' => $totalCommission,
        ]);
    }

    /**
     * Check if shop is approved and active.
     */
    public function isActive(): bool
    {
        return $this->approved && $this->status === 'active';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update stats when receipts change
        static::created(function ($shop) {
            $shop->updateReceiptStats();
        });
    }
    // Add to Shop.php model - insert these methods after existing methods

/**
 * Get the shop's logo URL.
 */
public function getLogoUrlAttribute(): string
{
    if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
        return \Storage::url($this->logo);
    }
    
    return asset('assets/images/default-shop-logo.png');
}

/**
 * Get logo path for PDF generation.
 */
public function getLogoPdfPathAttribute(): ?string
{
    if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
        return storage_path('app/public/' . $this->logo);
    }
    
    return null;
}

/**
 * Upload and store user logo.
 */
public function uploadLogo($file): bool
{
    try {
        // Delete existing logo if exists
        if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
            \Storage::disk('public')->delete($this->logo);
        }

        // Store new logo
        $path = $file->store('user-logos', 'public');
        $this->update(['logo' => $path]);

        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to upload user logo: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete user logo.
 */
public function deleteLogo(): bool
{
    try {
        if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
            \Storage::disk('public')->delete($this->logo);
            $this->update(['logo' => null]);
        }

        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to delete user logo: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if user can manage system logos.
 */
public function canManageSystemLogos(): bool
{
    return $this->isAdmin();
}
/**
 * Get base64 encoded shop logo for PDF generation.
 */
public function getLogoBase64Attribute(): ?string
{
    if (!$this->logo || !Storage::disk('public')->exists($this->logo)) {
        return null;
    }

    try {
        $path = storage_path('app/public/' . $this->logo);
        $imageData = file_get_contents($path);
        $mimeType = mime_content_type($path);
        
        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    } catch (\Exception $e) {
        \Log::error('Failed to generate shop logo base64: ' . $e->getMessage());
        return null;
    }
}
}