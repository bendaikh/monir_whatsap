<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleSheetConnection extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'name',
        'spreadsheet_id',
        'sheet_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->name . ' (' . $this->sheet_name . ')';
    }
}
