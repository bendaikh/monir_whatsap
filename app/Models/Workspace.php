<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function whatsappProfiles()
    {
        return $this->hasMany(WhatsappProfile::class);
    }

    public function facebookAdAccounts()
    {
        return $this->hasMany(FacebookAdAccount::class);
    }

    public function tiktokAdAccounts()
    {
        return $this->hasMany(TikTokAdAccount::class);
    }

    public function aiApiSetting()
    {
        return $this->hasOne(AiApiSetting::class);
    }
}
