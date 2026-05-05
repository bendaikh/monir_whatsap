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
            $table->string('email')->nullable()->after('phone');
            $table->string('city')->nullable()->after('email');
            $table->text('address')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_leads', function (Blueprint $table) {
            $table->dropColumn(['email', 'city', 'address']);
        });
    }
};
