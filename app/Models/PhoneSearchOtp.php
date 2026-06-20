<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PhoneSearchOtp extends Model {
    protected $fillable = [
        'serial_number','seller_whatsapp','otp','session_token',
        'ip_address','verified','used','attempts','expires_at'
    ];
    protected $casts = ['verified'=>'boolean','used'=>'boolean','expires_at'=>'datetime'];

    public function isExpired(): bool { return now()->greaterThan($this->expires_at); }
    public function isValid(): bool { return !$this->isExpired() && !$this->used && $this->attempts < 5; }
}
