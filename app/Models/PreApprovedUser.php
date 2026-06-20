<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreApprovedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'secondary_phone',
        'user_type',
        'shop_name',
        'business_address',
        'business_phone',
        'state',
        'local_government',
        'status',
        'used_at',
        'used_by_user_id',
        'import_metadata',
    ];

    protected $casts = [
        'import_metadata' => 'array',
        'used_at' => 'datetime',
    ];

    /**
     * Check if this pre-approved user matches the given data.
     */
    public function matchesUserData($email, $phone)
    {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        $cleanApprovedPhone = preg_replace('/[^0-9+]/', '', $this->phone_number);
        
        return $this->email === $email || 
               $cleanApprovedPhone === $cleanPhone ||
               $this->phone_number === $phone;
    }

    /**
     * Mark as used by a user.
     */
    public function markAsUsed($userId)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by_user_id' => $userId,
        ]);
    }

    /**
     * Get the user who used this pre-approval.
     */
    public function usedByUser()
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }
}