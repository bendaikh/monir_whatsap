<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpsellProduct extends Model
{
    protected $fillable = [
        'store_id',
        'title',
        'description',
        'price',
        'images',
        'is_active',
        'order',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getFirstImageAttribute()
    {
        if (!empty($this->images) && isset($this->images[0])) {
            return Product::resolvePublicImageUrl($this->images[0]);
        }
        return 'https://via.placeholder.com/400x400/e5e7eb/6b7280?text=No+Image';
    }
}
