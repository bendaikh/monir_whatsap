<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Store;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->json('facebook_pixels')->nullable()->after('facebook_pixel_enabled');
        });

        Store::whereNotNull('facebook_pixel_id')
            ->where('facebook_pixel_id', '!=', '')
            ->each(function (Store $store) {
                $store->update([
                    'facebook_pixels' => [
                        [
                            'id' => $store->facebook_pixel_id,
                            'name' => 'Primary Pixel',
                            'enabled' => (bool) $store->facebook_pixel_enabled,
                        ],
                    ],
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('facebook_pixels');
        });
    }
};
