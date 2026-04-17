<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('objective');
            $table->decimal('daily_budget', 10, 2);
            $table->json('platforms'); // ['facebook', 'tiktok']
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('campaign_data')->nullable(); // Store all the campaign creation data
            $table->string('facebook_campaign_id')->nullable();
            $table->string('facebook_ad_id')->nullable();
            $table->string('tiktok_campaign_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
