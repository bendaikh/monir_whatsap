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
            // French translations
            $table->json('landing_page_fr')->nullable()->after('landing_page_cta');
            
            // English translations
            $table->json('landing_page_en')->nullable()->after('landing_page_fr');
            
            // Arabic translations
            $table->json('landing_page_ar')->nullable()->after('landing_page_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['landing_page_fr', 'landing_page_en', 'landing_page_ar']);
        });
    }
};
