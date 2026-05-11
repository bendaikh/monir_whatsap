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
        Schema::table('product_leads', function (Blueprint $table) {
            $table->string('source')->default('landing_page')->after('selected_promotion_id');
            $table->foreignId('conversation_id')->nullable()->after('source')->constrained('conversations')->onDelete('set null');
            $table->foreignId('whatsapp_profile_id')->nullable()->after('conversation_id')->constrained('whatsapp_profiles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['whatsapp_profile_id']);
            $table->dropColumn(['source', 'conversation_id', 'whatsapp_profile_id']);
        });
    }
};
