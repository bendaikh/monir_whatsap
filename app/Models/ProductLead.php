<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLead extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'name',
        'phone',
        'email',
        'city',
        'address',
        'note',
        'language',
        'ip_address',
        'user_agent',
        'selected_promotion_id',
        'source',
        'conversation_id',
        'whatsapp_profile_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function selectedPromotion()
    {
        return $this->belongsTo(ProductPromotion::class, 'selected_promotion_id');
    }
    
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    
    public function whatsappProfile()
    {
        return $this->belongsTo(WhatsappProfile::class);
    }
    
    public function upsellOrders()
    {
        return $this->hasMany(UpsellOrder::class, 'lead_id');
    }
}
