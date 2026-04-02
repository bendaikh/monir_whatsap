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
            $table->longText('landing_page_content')->nullable()->after('description');
            $table->string('landing_page_hero_title')->nullable()->after('landing_page_content');
            $table->text('landing_page_hero_description')->nullable()->after('landing_page_hero_title');
            $table->json('landing_page_features')->nullable()->after('landing_page_hero_description');
            $table->text('landing_page_cta')->nullable()->after('landing_page_features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['landing_page_content', 'landing_page_hero_title', 'landing_page_hero_description', 'landing_page_features', 'landing_page_cta']);
        });
    }
};
