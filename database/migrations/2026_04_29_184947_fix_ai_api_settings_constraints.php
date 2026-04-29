<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint on user_id first
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropForeign('ai_api_settings_user_id_foreign');
        });

        // Now drop the unique constraint on user_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropUnique('ai_api_settings_user_id_unique');
        });

        // Re-add the foreign key without the unique constraint
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add unique constraint on workspace_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->unique('workspace_id');
        });
    }

    public function down(): void
    {
        // Remove unique constraint on workspace_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropUnique(['workspace_id']);
        });

        // Drop the foreign key
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Restore user_id unique constraint
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->unique('user_id');
        });

        // Re-add the foreign key with unique constraint
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
