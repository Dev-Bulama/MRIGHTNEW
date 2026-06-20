<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AppDownload extends Model {
    protected $fillable = ['name','description','file_path','external_url','platform','version','is_active','download_count'];
    protected $casts = ['is_active'=>'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
}
