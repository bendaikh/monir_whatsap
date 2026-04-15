<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiktok_ad_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->text('access_token_encrypted');
            $table->string('advertiser_id');
            $table->string('advertiser_name')->nullable();
            $table->string('app_id')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'advertiser_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiktok_ad_accounts');
    }
};
