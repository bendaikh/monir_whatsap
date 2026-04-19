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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('facebook_pixel_id')->nullable()->after('is_active');
            $table->boolean('facebook_pixel_enabled')->default(false)->after('facebook_pixel_id');
            $table->string('tiktok_pixel_id')->nullable()->after('facebook_pixel_enabled');
            $table->boolean('tiktok_pixel_enabled')->default(false)->after('tiktok_pixel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_pixel_id',
                'facebook_pixel_enabled',
                'tiktok_pixel_id',
                'tiktok_pixel_enabled',
            ]);
        });
    }
};
