<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'openai_api_key_encrypted',
        'openai_model',
        'anthropic_api_key_encrypted',
        'anthropic_model',
        'auto_reply_enabled',
    ];

    protected $casts = [
        'auto_reply_enabled' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get decrypted OpenAI API key
     */
    public function getOpenaiApiKeyAttribute(): ?string
    {
        if (empty($this->openai_api_key_encrypted)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->openai_api_key_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get decrypted Anthropic API key
     */
    public function getAnthropicApiKeyAttribute(): ?string
    {
        if (empty($this->anthropic_api_key_encrypted)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->anthropic_api_key_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }
}

