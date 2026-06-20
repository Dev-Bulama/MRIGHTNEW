<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'phone_number',
        'secondary_phone',
        'password',
        'user_type',
        'status',
        'last_login_at',
        'last_login_ip',
        'import_metadata',
        'account_activated_at',
        'activation_token',
        'assigned_states',
        'assigned_lgas', 
        'assigned_by',
        'assigned_at',
        'deleted_at',        
        'deletion_reason', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'account_activated_at' => 'datetime',
        'import_metadata' => 'array',
        'assigned_states' => 'array',
        'assigned_lgas' => 'array', 
        'assigned_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * User types constants.
     */
    const TYPE_ADMIN = 'admin';
    const TYPE_SHOP_OWNER = 'shop_owner';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_UNION = 'union'; // NEW UNION TYPE

    /**
     * User status constants.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PENDING = 'pending';

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is shop owner.
     */
    public function isShopOwner(): bool
    {
        return $this->user_type === self::TYPE_SHOP_OWNER;
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->user_type === self::TYPE_CUSTOMER;
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Get user type display name.
     */
    // public function getUserTypeDisplayAttribute(): string
    // {
    //     return match($this->user_type) {
    //         self::TYPE_ADMIN => 'Administrator',
    //         self::TYPE_SHOP_OWNER => 'Shop Owner',
    //         self::TYPE_CUSTOMER => 'Customer',
    //         default => 'Unknown'
    //     };
    // }
 /**
 * Get user type display name - UPDATE THIS METHOD
 */
// public function getUserTypeDisplayAttribute(): string
// {
//     return match($this->user_type) {
//         self::TYPE_ADMIN => 'Administrator',
//         self::TYPE_SHOP_OWNER => 'Shop Owner', 
//         self::TYPE_CUSTOMER => 'Customer',
//         self::TYPE_UNION => 'Union Executive',
//         default => ucfirst(str_replace('_', ' ', $this->user_type)),
//     };
//}

public static function userTypes(): array
{
    return [
        self::TYPE_ADMIN => 'Administrator',
        self::TYPE_SHOP_OWNER => 'Shop Owner',
        self::TYPE_CUSTOMER => 'Customer', 
        self::TYPE_UNION => 'Union Executive',
    ];
}
    /**
     * Get user status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_PENDING => 'Pending',
            default => 'Unknown'
        };
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        
        return $this->name ?? 'Unknown User';
    }

    /**
     * Get avatar URL attribute.
     */
    // public function getAvatarUrlAttribute(): string
    // {
    //     $name = urlencode($this->full_name);
    //     return "https://ui-avatars.com/api/?name={$name}&background=667eea&color=ffffff&size=150";
    // }

    // /**
    //  * Get all phone numbers as array.
    //  */
    public function getPhoneNumbersAttribute(): array
    {
        $phones = [];
        
        if ($this->phone_number) {
            $phones[] = $this->phone_number;
        }
        
        if ($this->secondary_phone) {
            $phones[] = $this->secondary_phone;
        }
        
        return $phones;
    }

    /**
     * Check if user has any of the given phone numbers.
     */
    public function hasPhoneNumber(string $phone): bool
    {
        return in_array($phone, $this->phone_numbers);
    }

    /**
     * Shop relationship.
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Receipts relationship.
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Current month receipts.
     */
    public function currentMonthReceipts()
    {
        return $this->receipts()->whereMonth('created_at', now()->month);
    }

    /**
     * Successful payments relationship.
     */
    public function successfulPayments()
    {
        return $this->hasMany(Payment::class)->where('status', 'successful');
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(?string $ipAddress = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Activate user account.
     */
    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'account_activated_at' => now(),
            'activation_token' => null,
        ]);
    }

    /**
     * Suspend user account.
     */
    public function suspend(): void
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
        ]);
    }

    /**
     * Reactivate suspended user account.
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Generate activation token.
     */
    public function generateActivationToken(): string
    {
        $token = \Str::random(60);
        
        $this->update([
            'activation_token' => $token,
            'status' => self::STATUS_PENDING,
        ]);
        
        return $token;
    }

    /**
     * Check for duplicate users by email or phone numbers.
     */
    public static function findDuplicates(string $email, ?string $primaryPhone = null, ?string $secondaryPhone = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::query();
        
        $query->where(function($q) use ($email, $primaryPhone, $secondaryPhone) {
            $q->where('email', $email);
            
            if ($primaryPhone) {
                $q->orWhere('phone_number', $primaryPhone)
                  ->orWhere('secondary_phone', $primaryPhone);
            }
            
            if ($secondaryPhone) {
                $q->orWhere('phone_number', $secondaryPhone)
                  ->orWhere('secondary_phone', $secondaryPhone);
            }
        });
        
        return $query->get();
    }

    /**
     * Create user from import data.
     */
    public static function createFromImport(array $data, array $importMetadata = []): self
    {
        $userData = [
            'name' => $data['name'] ?? ($data['first_name'] . ' ' . $data['last_name']),
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? $data['primary_phone'] ?? null,
            'secondary_phone' => $data['secondary_phone'] ?? null,
            'user_type' => $data['user_type'] ?? self::TYPE_CUSTOMER,
            'status' => self::STATUS_PENDING,
            'password' => \Hash::make($data['password'] ?? $data['phone_number'] ?? 'password123'),
            'import_metadata' => array_merge($importMetadata, [
                'imported_at' => now()->toISOString(),
                'imported_by' => auth()->id(),
                'source' => 'admin_import',
            ]),
        ];

        return static::create($userData);
    }

    /**
     * Scopes
     */
    
    /**
     * Scope: Active users only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: By user type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('user_type', $type);
    }

    /**
     * Scope: Search users.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%")
              ->orWhere('secondary_phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Recently logged in.
     */
    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }
    /**
     * Check if user's email is verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }
  /**
     * Get current year receipts for this user.
     */
    public function currentYearReceipts()
    {
        return $this->receipts()->whereYear('created_at', now()->year);
    }
    /**
     * Check if user account is fully verified and active.
     */
    public function isFullyVerified(): bool
    {
        return $this->isVerified() && $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatically populate first_name and last_name from name when creating
        static::creating(function ($user) {
            if ($user->name && !$user->first_name && !$user->last_name) {
                $nameParts = explode(' ', trim($user->name));
                $user->first_name = $nameParts[0] ?? '';
                $user->last_name = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
            }
        });
        
        // Update name when first_name or last_name changes
        static::saving(function ($user) {
            if ($user->first_name || $user->last_name) {
                $user->name = trim($user->first_name . ' ' . $user->last_name);
            }
        });
    }
    

/**
 * Get the user's logo URL.
 */
public function getLogoUrlAttribute(): string
{
    if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
        return \Storage::url($this->logo);
    }
    
    return asset('assets/images/default-user-logo.png');
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
// Add these constants and methods to User.php model

/**
 * User types constants - ADD UNION TYPE
 */
// const TYPE_ADMIN = 'admin';
// const TYPE_SHOP_OWNER = 'shop_owner';
// const TYPE_CUSTOMER = 'customer';


/**
 * Check if user is union.
 */
// public function isUnion(): bool
// {
//     return $this->user_type === self::TYPE_UNION;
//}

/**
 * Get user type display name - UPDATE THIS METHOD
 */


/**
 * Roles relationship
 */
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class, 'user_roles')
                ->withPivot('assigned_by', 'assigned_at')
                ->withTimestamps();
}

/**
 * Payout requests relationship
 */
public function payoutRequests(): HasMany
{
    return $this->hasMany(PayoutRequest::class);
}

/**
 * Pending payout requests
 */
public function pendingPayoutRequests(): HasMany
{
    return $this->payoutRequests()->where('status', PayoutRequest::STATUS_PENDING);
}

/**
 * Assigned by relationship (for unions)
 */
public function assignedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'assigned_by');
}

/**
 * Users assigned by this user (for admins)
 */
public function assignedUsers(): HasMany
{
    return $this->hasMany(User::class, 'assigned_by');
}

/**
 * Check if user has specific permission
 */
public function hasPermission(string $permission): bool
{
    // Admins have all permissions
    if ($this->isAdmin()) {
        return true;
    }

    // Check role-based permissions
    foreach ($this->roles as $role) {
        if ($role->hasPermission($permission)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if user has any of the given permissions
 */
public function hasAnyPermission(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if ($this->hasPermission($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if user has all given permissions
 */
public function hasAllPermissions(array $permissions): bool
{
    foreach ($permissions as $permission) {
        if (!$this->hasPermission($permission)) {
            return false;
        }
    }
    return true;
}

/**
 * Assign role to user
 */
public function assignRole(Role $role, User $assignedBy): bool
{
    if (!$this->roles()->where('role_id', $role->id)->exists()) {
        $this->roles()->attach($role->id, [
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
        ]);
        return true;
    }
    return false;
}

/**
 * Remove role from user
 */
public function removeRole(Role $role): bool
{
    return $this->roles()->detach($role->id) > 0;
}

// /**
//  * Get assigned states for union users
//  */
// public function getAssignedStatesAttribute()
// {
//     return $this->attributes['assigned_states'] ? json_decode($this->attributes['assigned_states'], true) : [];
// }
/**
 * Get assigned states for union users - COMPLETE THIS METHOD
 */
public function getAssignedStatesAttribute()
{
    return $this->attributes['assigned_states'] ? 
           json_decode($this->attributes['assigned_states'], true) : [];
}
/**
 * Get assigned LGAs for union users
 */
public function getAssignedLgasAttribute()
{
    return $this->attributes['assigned_lgas'] ? 
           json_decode($this->attributes['assigned_lgas'], true) : [];
}

/**
 * Check if union can manage specific state
 */
public function canManageState(string $state): bool
{
    if ($this->isAdmin()) {
        return true;
    }
    
    if (!$this->isUnion()) {
        return false;
    }
    
    return in_array($state, $this->assigned_states);
}

/**
 * Set assigned states for union users
 */
public function setAssignedStatesAttribute($value)
{
    $this->attributes['assigned_states'] = is_array($value) ? json_encode($value) : $value;
}

/**
 * Get assigned LGAs for union users
 */
// public function getAssignedLgasAttribute()
// {
//     return $this->attributes['assigned_lgas'] ? json_decode($this->attributes['assigned_lgas'], true) : [];
// }
/**
 * Check if union can manage specific LGA
 */
public function canManageLga(string $lga, string $state): bool
{
    if ($this->isAdmin()) {
        return true;
    }
    
    if (!$this->isUnion()) {
        return false;
    }
    
    return $this->canManageState($state) && in_array($lga, $this->assigned_lgas);
}

/**
 * Get shop owners that this union can manage
 */
public function manageableShopOwners()
{
    if ($this->isAdmin()) {
        return User::where('user_type', self::TYPE_SHOP_OWNER);
    }
    
    if (!$this->isUnion()) {
        return User::whereNull('id'); // Return empty query
    }
    
    return User::where('user_type', self::TYPE_SHOP_OWNER)
               ->whereHas('shop', function($query) {
                   $query->whereIn('state', $this->assigned_states);
                   
                   if (!empty($this->assigned_lgas)) {
                       $query->whereIn('local_government', $this->assigned_lgas);
                   }
               });
}
/**
 * Calculate available commission balance for payout
 */
public function getAvailableCommissionBalance(): float
{
    if (!$this->isShopOwner() || !$this->shop) {
        return 0.0;
    }

    // Get total commissions earned from successful receipts
    $totalEarned = $this->receipts()
        ->where('payment_gateway_status', 'successful')
        ->sum('commission_amount') ?? 0;

    // Subtract already paid out amounts
    $totalPaidOut = $this->payoutRequests()
        ->whereIn('status', [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PAID])
        ->sum('amount_approved') ?? 0;

    return max(0, $totalEarned - $totalPaidOut);
}

/**
 * Check if user can request payout
 */
public function canRequestPayout(): bool
{
    return $this->isShopOwner() && 
           $this->shop && 
           $this->getAvailableCommissionBalance() > 0 &&
           $this->pendingPayoutRequests()->count() === 0;
}
/**
 * Set assigned LGAs for union users
 */
public function setAssignedLgasAttribute($value)
{
    $this->attributes['assigned_lgas'] = is_array($value) ? json_encode($value) : $value;
}

/**
 * Check if union user can manage shop in specific location
 */
public function canManageShopInLocation(string $state, string $lga = null): bool
{
    if (!$this->isUnion()) {
        return false;
    }

    // Check if state is assigned
    if (!in_array($state, $this->assigned_states)) {
        return false;
    }

    // If LGA is specified, check if it's assigned
    if ($lga && !empty($this->assigned_lgas) && !in_array($lga, $this->assigned_lgas)) {
        return false;
    }

    return true;
}

/**
 * Get shops that this union user can manage
 */
public function getManageableShops()
{
    if (!$this->isUnion()) {
        return collect();
    }

    $query = Shop::query();

    // Filter by assigned states
    if (!empty($this->assigned_states)) {
        $query->whereIn('state', $this->assigned_states);
    }

    // Filter by assigned LGAs if specified
    if (!empty($this->assigned_lgas)) {
        $query->whereIn('local_government', $this->assigned_lgas);
    }

    return $query->get();
}

/**
 * Get available payout amount for shop owner
 */
public function getAvailablePayoutAmountAttribute(): float
{
    if (!$this->isShopOwner() || !$this->shop) {
        return 0;
    }

    return PayoutRequest::getAvailablePayoutAmount($this->shop);
}

/**
 * Check if user can request payout
 */
// public function canRequestPayout(): bool
// {
//     return $this->isShopOwner() 
//            && $this->shop 
//            && $this->shop->approved 
//            && $this->available_payout_amount >= PayoutRequest::getMinimumPayoutAmount();
// }

/**
 * Get total commission earned
 */
public function getTotalCommissionEarnedAttribute(): float
{
    return $this->shop ? $this->shop->total_commission_earned : 0;
}

/**
 * Get total payouts received
 */
public function getTotalPayoutsReceivedAttribute(): float
{
    return $this->payoutRequests()
                ->where('status', PayoutRequest::STATUS_PAID)
                ->sum('amount_approved');
}

/**
 * Scopes for union users
 */
public function scopeUnions($query)
{
    return $query->where('user_type', self::TYPE_UNION);
}

public function scopeByState($query, $state)
{
    return $query->whereJsonContains('assigned_states', $state);
}

public function scopeByLga($query, $lga)
{
    return $query->whereJsonContains('assigned_lgas', $lga);
}
/**
 * Check if user is a union executive
 */
public function isUnion()
{
    return in_array($this->user_type, ['union', 'union_executive']);
}

/**
 * Get shop owners manageable by this union user
 */
// public function manageableShopOwners()
// {
//     if (!$this->isUnion()) {
//         return collect();
//     }

//     $query = User::where('user_type', 'shop_owner')->with(['shop']);

//     if ($this->assigned_states) {
//         $query->whereHas('shop', function($q) {
//             $q->whereIn('state', $this->assigned_states);
//         });
//     }

//     return $query->get();
// }

/**
 * Get assigned states as array
 */
// public function getAssignedStatesAttribute($value)
// {
//     if (is_string($value)) {
//         return json_decode($value, true) ?: [];
//     }
//     return $value ?: [];
// }

/**
 * Get assigned LGAs as array  
 */
// public function getAssignedLgasAttribute($value)
// {
//     if (is_string($value)) {
//         return json_decode($value, true) ?: [];
//     }
//     return $value ?: [];
// }
/**
 * Get the user's avatar URL
 */
// public function getAvatarUrlAttribute()
// {
//     // If user has uploaded avatar
//     if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
//         return asset('storage/' . $this->avatar);
//     }
    
//     // If user has a profile photo
//     if ($this->profile_photo_path && file_exists(public_path('storage/' . $this->profile_photo_path))) {
//         return asset('storage/' . $this->profile_photo_path);
//     }
    
//     // Default to Gravatar or placeholder
//     return $this->getGravatarUrl();
// }

/**
 * Get Gravatar URL for user
 */
public function getGravatarUrl($size = 200)
{
    $hash = md5(strtolower(trim($this->email)));
    return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp&r=g";
}

/**
 * Get user type display name
 */
public function getUserTypeDisplayAttribute()
{
    $userTypes = [
        'admin' => 'Administrator',
        'shop_owner' => 'Shop Owner',
        'union' => 'Union Executive',
        'union_executive' => 'Union Executive',
        'user' => 'User',
    ];
    
    return $userTypes[$this->user_type] ?? ucfirst($this->user_type);
}
/**
 * Get the user's avatar URL (null-safe)
 */
public function getAvatarUrlAttribute()
{
    try {
        // If user has uploaded avatar
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        
        // If user has a profile photo
        if ($this->profile_photo_path && file_exists(public_path('storage/' . $this->profile_photo_path))) {
            return asset('storage/' . $this->profile_photo_path);
        }
        
        // Return null if no avatar (don't generate Gravatar to avoid external calls)
        return null;
        
    } catch (Exception $e) {
        // Return null on any error
        return null;
    }
}
}