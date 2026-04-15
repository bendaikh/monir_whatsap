<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TikTokAdAccount extends Model
{
    use HasFactory;

    protected $table = 'tiktok_ad_accounts';

    protected $fillable = [
        'user_id',
        'access_token_encrypted',
        'advertiser_id',
        'advertiser_name',
        'app_id',
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
