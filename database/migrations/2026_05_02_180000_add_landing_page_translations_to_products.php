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
        Schema::table('products', function (Blueprint $table) {
            // Unified JSON column to store landing page content in ALL languages
            // Structure: { "fr": {...}, "en": {...}, "es": {...}, "de": {...}, etc. }
            $table->json('landing_page_translations')->nullable()->after('landing_page_languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('landing_page_translations');
        });
    }
};
