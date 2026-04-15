<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookAdAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'access_token_encrypted',
        'ad_account_id',
        'ad_account_name',
        'page_id',
        'business_id',
        'token_expires_at',
        'is_active',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTokenValid(): bool
    {
        return $this->is_active 
            && $this->token_expires_at 
            && $this->token_expires_at->isFuture();
    }
}
