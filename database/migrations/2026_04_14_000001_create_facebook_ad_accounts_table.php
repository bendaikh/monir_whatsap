<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_ad_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->text('access_token_encrypted');
            $table->string('ad_account_id');
            $table->string('ad_account_name')->nullable();
            $table->string('page_id')->nullable();
            $table->string('business_id')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'ad_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_ad_accounts');
    }
};
