<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpsellOrder extends Model
{
    protected $fillable = [
        'lead_id',
        'upsell_product_id',
        'store_id',
        'status',
    ];

    public function lead()
    {
        return $this->belongsTo(ProductLead::class, 'lead_id');
    }

    public function upsellProduct()
    {
        return $this->belongsTo(UpsellProduct::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
