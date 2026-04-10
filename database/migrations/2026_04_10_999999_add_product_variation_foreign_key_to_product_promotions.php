<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key constraint if it doesn't already exist
        if (!$this->foreignKeyExists('product_promotions', 'product_promotions_product_variation_id_foreign')) {
            Schema::table('product_promotions', function (Blueprint $table) {
                $table->foreign('product_variation_id')
                    ->references('id')
                    ->on('product_variations')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_promotions', function (Blueprint $table) {
            $table->dropForeign(['product_variation_id']);
        });
    }

    private function foreignKeyExists($table, $foreignKey): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = ? 
             AND CONSTRAINT_NAME = ? 
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$database, $table, $foreignKey]
        );

        return count($result) > 0;
    }
};
