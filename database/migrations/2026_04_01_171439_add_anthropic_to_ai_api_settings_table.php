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
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->text('anthropic_api_key_encrypted')->nullable()->after('openai_model');
            $table->string('anthropic_model', 128)->nullable()->after('anthropic_api_key_encrypted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropColumn(['anthropic_api_key_encrypted', 'anthropic_model']);
        });
    }
};
