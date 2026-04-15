<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone',
        'subscription_plan',
        'subscription_ends_at',
        'is_active',
        'external_api_url',
        'external_api_key_encrypted',
        'external_api_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
            'is_active' => 'boolean',
            'external_api_enabled' => 'boolean',
        ];
    }
    
    public function whatsappProfiles()
    {
        return $this->hasMany(WhatsappProfile::class);
    }

    public function aiApiSetting()
    {
        return $this->hasOne(AiApiSetting::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function facebookAdAccounts()
    {
        return $this->hasMany(FacebookAdAccount::class);
    }

    public function tiktokAdAccounts()
    {
        return $this->hasMany(TikTokAdAccount::class);
    }
}
