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
        Schema::table('users', function (Blueprint $table) {
            $table->string('external_api_url')->nullable()->after('is_active');
            $table->text('external_api_key_encrypted')->nullable()->after('external_api_url');
            $table->boolean('external_api_enabled')->default(false)->after('external_api_key_encrypted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['external_api_url', 'external_api_key_encrypted', 'external_api_enabled']);
        });
    }
};
