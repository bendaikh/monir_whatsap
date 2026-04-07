<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'stock',
        'sku',
        'images',
        'main_image',
        'image_descriptions',
        'ai_generated_images',
        'ai_images_status',
        'ai_images_progress',
        'ai_images_total',
        'ai_images_generated',
        'is_active',
        'is_featured',
        'order',
        'landing_page_content',
        'landing_page_hero_title',
        'landing_page_hero_description',
        'landing_page_features',
        'landing_page_cta',
        'landing_page_fr',
        'landing_page_en',
        'landing_page_ar',
        'landing_page_status',
        'landing_page_sections'
    ];

    protected $casts = [
        'images' => 'array',
        'image_descriptions' => 'array',
        'ai_generated_images' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'landing_page_features' => 'array',
        'landing_page_fr' => 'array',
        'landing_page_en' => 'array',
        'landing_page_ar' => 'array',
        'landing_page_sections' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Resolve a stored path or URL to a usable browser URL (public disk, absolute URL, or site-relative).
     */
    public static function resolvePublicImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = trim($path);
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return \Storage::disk('public')->url($path);
    }

    public function getFirstImageAttribute()
    {
        $candidates = [];
        if ($this->main_image) {
            $candidates[] = $this->main_image;
        }
        if (! empty($this->images) && isset($this->images[0])) {
            $candidates[] = $this->images[0];
        }
        if (! empty($this->ai_generated_images) && isset($this->ai_generated_images[0])) {
            $candidates[] = $this->ai_generated_images[0];
        }

        foreach ($candidates as $raw) {
            $url = self::resolvePublicImageUrl($raw);
            if ($url !== null && $url !== '') {
                return $url;
            }
        }

        return 'https://via.placeholder.com/400x400/e5e7eb/6b7280?text=No+Image';
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    public function leads()
    {
        return $this->hasMany(ProductLead::class);
    }

    public function getAllImagesAttribute()
    {
        $images = [];
        
        if (! empty($this->images)) {
            foreach ($this->images as $image) {
                $resolved = self::resolvePublicImageUrl($image);
                if ($resolved) {
                    $images[] = $resolved;
                }
            }
        }

        if (! empty($this->ai_generated_images)) {
            foreach ($this->ai_generated_images as $image) {
                $resolved = self::resolvePublicImageUrl($image);
                if ($resolved) {
                    $images[] = $resolved;
                }
            }
        }
        
        if (empty($images)) {
            return ['https://via.placeholder.com/800x800/e5e7eb/6b7280?text=No+Image'];
        }
        
        return $images;
    }
}

