<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\AiLandingPageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateProductLandingPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product, $userId)
    {
        $this->product = $product;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to processing
            $this->product->update(['landing_page_status' => 'processing']);

            // Get the user
            $user = \App\Models\User::find($this->userId);
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Generate landing page
            $aiService = new AiLandingPageService($user);
            
            // Use product's selected languages, default to ['fr'] if none selected
            $languages = $this->product->landing_page_languages ?? ['fr'];
            
            $landingPageData = $aiService->generateLandingPage($this->product, $languages);
            $aiService->saveLandingPageToProduct($this->product, $landingPageData);

            // Update status to completed
            $this->product->update(['landing_page_status' => 'completed']);

            Log::info('Landing page generated successfully for product: ' . $this->product->id);
        } catch (\Exception $e) {
            // Update status to failed
            $this->product->update(['landing_page_status' => 'failed']);
            
            Log::error('Failed to generate landing page for product ' . $this->product->id . ': ' . $e->getMessage());
            
            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Update status to failed if not already set
        $this->product->update(['landing_page_status' => 'failed']);
        
        Log::error('Job failed for product ' . $this->product->id . ': ' . $exception->getMessage());
    }
}
