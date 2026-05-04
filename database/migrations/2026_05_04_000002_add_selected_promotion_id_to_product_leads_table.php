<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_promotion_id')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->dropColumn('selected_promotion_id');
        });
    }
};
