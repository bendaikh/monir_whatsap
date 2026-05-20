<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'subdomain',
        'domain',
        'description',
        'logo',
        'is_active',
        'facebook_pixel_id',
        'facebook_pixel_enabled',
        'facebook_pixels',
        'tiktok_pixel_id',
        'tiktok_pixel_enabled',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'facebook_pixel_enabled' => 'boolean',
        'facebook_pixels' => 'array',
        'tiktok_pixel_enabled' => 'boolean',
    ];

    /**
     * Active Facebook Pixel IDs (supports multiple pixels per store).
     */
    public function activeFacebookPixels(): array
    {
        $pixels = collect($this->facebook_pixels ?? [])
            ->filter(fn ($p) => !empty($p['id']) && ($p['enabled'] ?? true))
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        if (!empty($pixels)) {
            return $pixels;
        }

        if ($this->facebook_pixel_enabled && !empty($this->facebook_pixel_id)) {
            return [$this->facebook_pixel_id];
        }

        return [];
    }

    public function hasFacebookPixels(): bool
    {
        return count($this->activeFacebookPixels()) > 0;
    }

    public function syncLegacyFacebookPixel(): void
    {
        $pixels = $this->activeFacebookPixels();
        if (!empty($pixels)) {
            $this->update([
                'facebook_pixel_id' => $pixels[0],
                'facebook_pixel_enabled' => true,
            ]);
        } else {
            $this->update([
                'facebook_pixel_id' => null,
                'facebook_pixel_enabled' => false,
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($store) {
            if (empty($store->subdomain)) {
                $store->subdomain = Str::slug($store->name);
            }
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function websiteSettings()
    {
        return $this->hasOne(WebsiteSettings::class);
    }
}
