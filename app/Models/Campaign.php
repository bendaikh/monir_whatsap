<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'objective',
        'daily_budget',
        'platforms',
        'status',
        'campaign_data',
        'facebook_campaign_id',
        'facebook_ad_id',
        'tiktok_campaign_id',
        'error_message',
    ];

    protected $casts = [
        'platforms' => 'array',
        'campaign_data' => 'array',
        'daily_budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
