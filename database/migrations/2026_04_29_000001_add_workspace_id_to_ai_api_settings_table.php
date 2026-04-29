<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_api_settings', function (Blueprint $table) {
            // Add workspace_id column if it doesn't exist
            if (!Schema::hasColumn('ai_api_settings', 'workspace_id')) {
                $table->foreignId('workspace_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
            
            // Make user_id nullable since we're moving to workspace-level
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_api_settings', function (Blueprint $table) {
            // Remove workspace_id if it exists
            if (Schema::hasColumn('ai_api_settings', 'workspace_id')) {
                $table->dropForeign(['workspace_id']);
                $table->dropColumn('workspace_id');
            }
        });
    }
};
