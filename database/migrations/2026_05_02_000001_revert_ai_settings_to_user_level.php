<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint on workspace_id if it exists
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
        });

        // Drop the unique constraint on workspace_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropUnique(['workspace_id']);
        });

        // Make workspace_id nullable
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->change();
        });

        // Add unique constraint back on user_id for global user settings
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        // Remove unique constraint on user_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        // Make workspace_id not nullable
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable(false)->change();
        });

        // Restore unique constraint on workspace_id
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->unique('workspace_id');
        });

        // Re-add the foreign key
        Schema::table('ai_api_settings', function (Blueprint $table) {
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }
};
