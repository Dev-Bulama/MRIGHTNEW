<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SystemLogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'logo_path',
        'original_name',
        'uploaded_by',
    ];

    /**
     * Logo type constants
     */
    const TYPE_AMPAT = 'ampat';
    const TYPE_MRIGHT = 'mright';

    /**
     * Get the user who uploaded this logo.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the logo URL attribute.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::url($this->logo_path);
        }
        
        return asset('assets/images/default-logo.png');
    }

    /**
     * Get logo by type.
     */
    public static function getByType(string $type): ?self
    {
        return static::where('type', $type)->first();
    }

    /**
     * Get AMPAT logo.
     */
    public static function getAmpatLogo(): ?self
    {
        return static::getByType(self::TYPE_AMPAT);
    }

    /**
     * Get MRight logo.
     */
    public static function getMrightLogo(): ?self
    {
        return static::getByType(self::TYPE_MRIGHT);
    }

    /**
     * Upload and store logo.
     */
    public static function uploadLogo(string $type, $file, int $uploadedBy): self
    {
        $path = $file->store('system-logos', 'public');
        
        // Delete existing logo of same type
        $existing = static::where('type', $type)->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->logo_path);
            $existing->delete();
        }

        return static::create([
            'type' => $type,
            'logo_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * Get all logo types.
     */
    public static function getLogoTypes(): array
    {
        return [
            self::TYPE_AMPAT => 'AMPAT Logo',
            self::TYPE_MRIGHT => 'MRight Logo',
        ];
    }
}