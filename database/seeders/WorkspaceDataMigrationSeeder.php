<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Store;
use App\Models\WhatsappProfile;
use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use Illuminate\Support\Facades\DB;

class WorkspaceDataMigrationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting workspace data migration...');

        DB::beginTransaction();

        try {
            $users = User::all();
            $this->command->info("Found {$users->count()} users to process");

            foreach ($users as $user) {
                $storesCount = $user->stores()->count();
                $whatsappCount = $user->whatsappProfiles()->whereNull('workspace_id')->count();
                $facebookCount = $user->facebookAdAccounts()->whereNull('workspace_id')->count();
                $tiktokCount = $user->tiktokAdAccounts()->whereNull('workspace_id')->count();

                if ($storesCount > 0 || $whatsappCount > 0 || $facebookCount > 0 || $tiktokCount > 0) {
                    $workspaceName = $user->company_name ?: ($user->name . "'s Workspace");
                    
                    $workspace = Workspace::create([
                        'user_id' => $user->id,
                        'name' => $workspaceName,
                        'description' => 'Default workspace created during migration',
                        'is_active' => true,
                    ]);

                    $user->stores()->whereNull('workspace_id')->update(['workspace_id' => $workspace->id]);
                    $user->whatsappProfiles()->whereNull('workspace_id')->update(['workspace_id' => $workspace->id]);
                    $user->facebookAdAccounts()->whereNull('workspace_id')->update(['workspace_id' => $workspace->id]);
                    $user->tiktokAdAccounts()->whereNull('workspace_id')->update(['workspace_id' => $workspace->id]);

                    $this->command->info("Created workspace '{$workspaceName}' for user {$user->email}");
                    $this->command->info("  - Migrated {$storesCount} stores");
                    $this->command->info("  - Migrated {$whatsappCount} WhatsApp profiles");
                    $this->command->info("  - Migrated {$facebookCount} Facebook ad accounts");
                    $this->command->info("  - Migrated {$tiktokCount} TikTok ad accounts");
                } else {
                    $this->command->info("User {$user->email} has no data to migrate, skipping...");
                }
            }

            DB::commit();
            $this->command->info('Workspace data migration completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
