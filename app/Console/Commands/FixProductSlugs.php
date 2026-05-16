<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove timestamp suffixes from product slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing product slugs...');
        
        $products = Product::all();
        $fixed = 0;
        $slugCount = [];
        
        foreach ($products as $product) {
            // Remove timestamp suffix (10 digits at the end)
            $baseSlug = preg_replace('/-\d{10}$/', '', $product->slug);
            
            if ($baseSlug !== $product->slug) {
                // Check if this slug already exists
                $newSlug = $baseSlug;
                $counter = 1;
                
                while (Product::where('slug', $newSlug)->where('id', '!=', $product->id)->exists()) {
                    $newSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $product->slug = $newSlug;
                $product->save();
                $fixed++;
                $this->line("Fixed: {$product->name} -> {$newSlug}");
            }
        }
        
        $this->info("Fixed {$fixed} product slugs!");
        
        return 0;
    }
}
