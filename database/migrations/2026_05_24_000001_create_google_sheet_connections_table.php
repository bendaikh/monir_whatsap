<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_sheet_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('spreadsheet_id');
            $table->string('sheet_name')->default('Sheet1');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_sheet_connections');
    }
};
