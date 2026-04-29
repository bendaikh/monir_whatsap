<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing AI API settings from user-based to workspace-based
        // For each user with AI settings, assign them to their active workspace
        $aiSettings = DB::table('ai_api_settings')
            ->whereNull('workspace_id')
            ->whereNotNull('user_id')
            ->get();

        foreach ($aiSettings as $setting) {
            // Get the user's workspaces
            $workspace = DB::table('workspaces')
                ->where('user_id', $setting->user_id)
                ->where('is_active', true)
                ->first();

            if ($workspace) {
                // Update the setting with the workspace_id
                DB::table('ai_api_settings')
                    ->where('id', $setting->id)
                    ->update(['workspace_id' => $workspace->id]);
            }
        }
    }

    public function down(): void
    {
        // Revert workspace_id to null for all settings
        DB::table('ai_api_settings')->update(['workspace_id' => null]);
    }
};
