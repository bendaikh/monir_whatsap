<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappProfile extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'store_id',
        'name',
        'phone_number',
        'profile_picture',
        'qr_code',
        'status',
        'session_id',
        'session_data',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'session_data' => 'array',
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
