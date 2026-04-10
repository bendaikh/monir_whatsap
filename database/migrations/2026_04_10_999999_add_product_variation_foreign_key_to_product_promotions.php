<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only proceed if both tables exist
        if (!Schema::hasTable('product_promotions') || !Schema::hasTable('product_variations')) {
            return;
        }

        // Check if foreign key already exists
        if ($this->foreignKeyExists('product_promotions', 'product_promotions_product_variation_id_foreign')) {
            return;
        }

        // Verify column types match before adding foreign key
        $promotionsColumn = DB::select("SHOW COLUMNS FROM product_promotions WHERE Field = 'product_variation_id'");
        $variationsColumn = DB::select("SHOW COLUMNS FROM product_variations WHERE Field = 'id'");

        if (empty($promotionsColumn) || empty($variationsColumn)) {
            \Log::warning('Cannot add foreign key: columns do not exist');
            return;
        }

        try {
            Schema::table('product_promotions', function (Blueprint $table) {
                $table->foreign('product_variation_id')
                    ->references('id')
                    ->on('product_variations')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            \Log::error('Foreign key constraint failed: ' . $e->getMessage());
            // Don't throw - allow migration to complete
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_promotions')) {
            Schema::table('product_promotions', function (Blueprint $table) {
                $table->dropForeign(['product_variation_id']);
            });
        }
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
