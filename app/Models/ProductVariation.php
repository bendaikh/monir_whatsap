<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'stock',
        'attributes',
        'images',
        'is_active',
        'is_default',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'attributes' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    public function getAttributesDisplayAttribute()
    {
        if (empty($this->attributes)) {
            return '';
        }

        $display = [];
        foreach ($this->attributes as $key => $value) {
            $display[] = ucfirst($key) . ': ' . $value;
        }

        return implode(' / ', $display);
    }

    public function getFirstImageAttribute()
    {
        if (!empty($this->images) && isset($this->images[0])) {
            return Product::resolvePublicImageUrl($this->images[0]);
        }

        return $this->product->first_image;
    }
}
