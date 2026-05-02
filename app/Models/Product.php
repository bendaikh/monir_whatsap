<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'theme',
        'theme_data',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'stock',
        'sku',
        'has_variations',
        'has_promotions',
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
        'landing_page_currency',
        'landing_page_languages',
        'landing_page_translations',
        'landing_page_status',
        'landing_page_sections'
    ];

    protected $casts = [
        'images' => 'array',
        'image_descriptions' => 'array',
        'ai_generated_images' => 'array',
        'theme_data' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'has_variations' => 'boolean',
        'has_promotions' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'landing_page_features' => 'array',
        'landing_page_fr' => 'array',
        'landing_page_en' => 'array',
        'landing_page_ar' => 'array',
        'landing_page_languages' => 'array',
        'landing_page_translations' => 'array',
        'landing_page_sections' => 'array',
    ];

    /**
     * Get landing page content for a specific language.
     * Supports both new (landing_page_translations) and legacy (landing_page_fr/en/ar) formats.
     *
     * @param string|null $language Language code. If null, uses first available language.
     * @return array|null
     */
    public function getLandingPageContent(?string $language = null): ?array
    {
        $translations = $this->landing_page_translations ?? [];

        if (empty($translations)) {
            $translations = [];
            if (!empty($this->landing_page_fr)) {
                $translations['fr'] = $this->landing_page_fr;
            }
            if (!empty($this->landing_page_en)) {
                $translations['en'] = $this->landing_page_en;
            }
            if (!empty($this->landing_page_ar)) {
                $translations['ar'] = $this->landing_page_ar;
            }
        }

        if (empty($translations)) {
            return null;
        }

        if ($language && isset($translations[$language])) {
            return $translations[$language];
        }

        $enabledLanguages = $this->landing_page_languages ?? ['fr'];
        foreach ($enabledLanguages as $lang) {
            if (isset($translations[$lang])) {
                return $translations[$lang];
            }
        }

        return reset($translations) ?: null;
    }

    /**
     * Get all available languages for this product's landing page.
     * Returns an array of language codes that have content.
     *
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        $translations = $this->landing_page_translations ?? [];
        
        if (!empty($translations)) {
            return array_keys($translations);
        }

        $legacy = [];
        if (!empty($this->landing_page_fr)) $legacy[] = 'fr';
        if (!empty($this->landing_page_en)) $legacy[] = 'en';
        if (!empty($this->landing_page_ar)) $legacy[] = 'ar';

        return $legacy;
    }

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

    public function store()
    {
        return $this->belongsTo(Store::class);
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
        
        // If it's already a full URL, return as-is
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // If it's already a site-relative path starting with /
        if (str_starts_with($path, '/')) {
            return $path;
        }
        
        // If it's already starting with /storage/, return as-is
        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        // Build a relative URL to avoid APP_URL issues
        // Remove any leading slashes and build the path
        $cleanPath = ltrim($path, '/');
        return '/storage/' . $cleanPath;
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

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function activeVariations()
    {
        return $this->hasMany(ProductVariation::class)->where('is_active', true)->orderBy('order');
    }

    public function defaultVariation()
    {
        return $this->hasOne(ProductVariation::class)->where('is_default', true);
    }

    public function getPriceRangeAttribute()
    {
        if (!$this->has_variations) {
            return null;
        }

        $variations = $this->activeVariations;
        if ($variations->isEmpty()) {
            return null;
        }

        $minPrice = $variations->min('price');
        $maxPrice = $variations->max('price');

        if ($minPrice == $maxPrice) {
            return number_format($minPrice, 2) . ' MAD';
        }

        return number_format($minPrice, 2) . ' - ' . number_format($maxPrice, 2) . ' MAD';
    }

    public function getTotalStockAttribute()
    {
        if (!$this->has_variations) {
            return $this->stock;
        }

        return $this->variations()->sum('stock');
    }

    public function promotions()
    {
        return $this->hasMany(ProductPromotion::class)->orderBy('min_quantity');
    }

    public function activePromotions()
    {
        return $this->hasMany(ProductPromotion::class)->where('is_active', true)->orderBy('min_quantity');
    }

    public function getPriceForQuantity($quantity, $variationId = null)
    {
        if (!$this->has_promotions) {
            return $variationId ? $this->variations()->find($variationId)?->price : $this->price;
        }

        $query = $this->promotions()->where('is_active', true);
        
        if ($variationId) {
            $query->where('product_variation_id', $variationId);
        } else {
            $query->whereNull('product_variation_id');
        }

        $promotion = $query->where('min_quantity', '<=', $quantity)
            ->where(function($q) use ($quantity) {
                $q->whereNull('max_quantity')
                  ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($promotion) {
            return $promotion->price;
        }

        return $variationId ? $this->variations()->find($variationId)?->price : $this->price;
    }
}

