<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SearchIntelligence extends Model {
    protected $fillable = [
        'serial_number','seller_whatsapp','search_result','ip_address',
        'user_agent','browser','os','device_type','screen_resolution',
        'timezone','latitude','longitude','isp','network_type','referrer',
        'browser_fingerprint','session_id','image_path','admin_notified'
    ];
    protected $casts = ['browser_fingerprint'=>'array','admin_notified'=>'boolean'];
}
