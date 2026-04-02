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
        'landing_page_ar'
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'landing_page_features' => 'array',
        'landing_page_fr' => 'array',
        'landing_page_en' => 'array',
        'landing_page_ar' => 'array',
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

    public function getFirstImageAttribute()
    {
        if (!empty($this->images) && isset($this->images[0])) {
            return \Storage::url($this->images[0]);
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
}

