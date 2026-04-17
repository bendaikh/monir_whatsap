<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to processing
            $this->campaign->update(['status' => 'processing']);

            $data = $this->campaign->campaign_data;
            $platforms = $this->campaign->platforms;
            $results = [];
            $errors = [];

            // Process media files
            $mediaFiles = $this->processMediaFiles($data['media_files_paths'] ?? []);

            foreach ($platforms as $platform) {
                try {
                    if ($platform === 'facebook' && !empty($data['facebook_account_id'])) {
                        $account = FacebookAdAccount::where('id', $data['facebook_account_id'])
                            ->where('user_id', $this->campaign->user_id)
                            ->firstOrFail();
                        
                        $result = $this->createFacebookCampaign($account, $data, $mediaFiles);
                        $results['facebook'] = $result;
                        
                        // Save Facebook campaign IDs
                        $this->campaign->update([
                            'facebook_campaign_id' => $result['campaign_id'],
                            'facebook_ad_id' => $result['ad_id'],
                        ]);
                        
                    } elseif ($platform === 'tiktok' && !empty($data['tiktok_account_id'])) {
                        $account = TikTokAdAccount::where('id', $data['tiktok_account_id'])
                            ->where('user_id', $this->campaign->user_id)
                            ->firstOrFail();
                        
                        $result = $this->createTikTokCampaign($account, $data, $mediaFiles);
                        $results['tiktok'] = $result;
                        
                        // Save TikTok campaign ID
                        $this->campaign->update([
                            'tiktok_campaign_id' => $result['campaign_id'],
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[$platform] = $e->getMessage();
                    Log::error("Error creating {$platform} campaign for Campaign ID {$this->campaign->id}: " . $e->getMessage());
                }
            }

            // Clean up temporary files
            $this->cleanupMediaFiles($mediaFiles);

            if (!empty($results)) {
                // Update status to completed
                $this->campaign->update([
                    'status' => 'completed',
                    'error_message' => !empty($errors) ? json_encode($errors) : null,
                ]);
                
                Log::info('Campaign created successfully: ' . $this->campaign->id);
            } else {
                throw new \Exception('Failed to create campaigns on any platform: ' . implode(', ', $errors));
            }
            
        } catch (\Exception $e) {
            // Update status to failed
            $this->campaign->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            Log::error('Failed to create campaign ' . $this->campaign->id . ': ' . $e->getMessage());
            
            throw $e;
        }
    }

    /**
     * Process media files from storage paths
     */
    private function processMediaFiles(array $filePaths): array
    {
        $mediaFiles = [];
        
        foreach ($filePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                $fullPath = Storage::disk('public')->path($path);
                $mimeType = Storage::disk('public')->mimeType($path);
                
                $mediaFiles[] = [
                    'path' => $path,
                    'full_path' => $fullPath,
                    'mime_type' => $mimeType,
                    'type' => str_starts_with($mimeType, 'video') ? 'video' : 'image',
                ];
            }
        }
        
        return $mediaFiles;
    }

    /**
     * Clean up temporary media files
     */
    private function cleanupMediaFiles(array $mediaFiles): void
    {
        foreach ($mediaFiles as $media) {
            if (isset($media['path']) && Storage::disk('public')->exists($media['path'])) {
                Storage::disk('public')->delete($media['path']);
            }
        }
    }

    private function createFacebookCampaign(FacebookAdAccount $account, array $data, array $mediaFiles)
    {
        $accessToken = Crypt::decryptString($account->access_token_encrypted);
        
        // Map objectives
        $objectiveMap = [
            'AWARENESS' => 'OUTCOME_AWARENESS',
            'CONSIDERATION' => 'OUTCOME_TRAFFIC',
            'CONVERSION' => 'OUTCOME_SALES',
        ];
        
        $objective = $objectiveMap[$data['objective']] ?? 'OUTCOME_TRAFFIC';
        
        // Step 1: Create campaign
        Log::info("Creating Facebook campaign with objective: {$objective}");
        $response = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/campaigns", [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'],
            'objective' => $objective,
            'status' => 'PAUSED',
            'special_ad_categories' => [],
            'is_adset_budget_sharing_enabled' => false,
        ]);
        
        if (!$response->successful()) {
            $error = $response->json()['error']['message'] ?? 'Unknown error';
            throw new \Exception("Facebook Campaign Error: {$error}");
        }
        
        $campaignId = $response->json()['id'];
        Log::info("Created Facebook campaign: {$campaignId}");
        
        // Step 2: Upload media files
        $uploadedMedia = [];
        foreach ($mediaFiles as $media) {
            try {
                $hash = $this->uploadFacebookMedia($account, $media, $accessToken);
                if ($hash) {
                    $uploadedMedia[] = [
                        'hash' => $hash,
                        'type' => $media['type']
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Failed to upload media to Facebook: " . $e->getMessage());
            }
        }
        
        if (empty($uploadedMedia)) {
            throw new \Exception("Failed to upload any media files to Facebook");
        }
        
        // Prepare targeting
        $targetCountries = !empty($data['target_countries']) ? $data['target_countries'] : ['US'];
        
        // Step 3: Create ad set
        $adSetData = [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'] . ' - Ad Set',
            'campaign_id' => $campaignId,
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'REACH',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'daily_budget' => (int)($data['daily_budget'] * 100),
            'targeting' => json_encode(['geo_locations' => ['countries' => $targetCountries]]),
            'status' => 'PAUSED',
        ];
        
        $adSetResponse = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/adsets", $adSetData);
        
        if (!$adSetResponse->successful()) {
            throw new \Exception("Failed to create ad set: " . ($adSetResponse->json()['error']['message'] ?? 'Unknown error'));
        }
        
        $adSetId = $adSetResponse->json()['id'];
        
        // Step 4: Create ad creative
        $pageId = $data['facebook_page_id'] ?? $account->page_id;
        
        if (!$pageId) {
            throw new \Exception("No Facebook page selected");
        }
        
        $firstMedia = $uploadedMedia[0];
        $linkData = [
            'message' => $data['primary_text'],
            'link' => $data['website_url'] ?? 'https://example.com',
            'name' => $data['headline'] ?? $data['campaign_name'],
            'description' => $data['description'] ?? '',
            'call_to_action' => [
                'type' => $this->mapCallToAction($data['call_to_action'] ?? 'LEARN_MORE')
            ],
        ];
        
        if ($firstMedia['type'] === 'image') {
            $linkData['image_hash'] = $firstMedia['hash'];
        } else {
            $linkData['video_id'] = $firstMedia['hash'];
        }
        
        $creativeData = [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'] . ' - Creative',
            'object_story_spec' => json_encode([
                'page_id' => $pageId,
                'link_data' => $linkData
            ])
        ];
        
        $creativeResponse = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/adcreatives", $creativeData);
        
        if (!$creativeResponse->successful()) {
            throw new \Exception("Failed to create ad creative");
        }
        
        $creativeId = $creativeResponse->json()['id'];
        
        // Step 5: Create ad
        $adResponse = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/ads", [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'] . ' - Ad',
            'adset_id' => $adSetId,
            'creative' => json_encode(['creative_id' => $creativeId]),
            'status' => 'PAUSED',
        ]);
        
        if (!$adResponse->successful()) {
            throw new \Exception("Failed to create ad");
        }
        
        $adId = $adResponse->json()['id'];
        
        return [
            'campaign_id' => $campaignId,
            'ad_id' => $adId,
            'platform' => 'Facebook',
        ];
    }

    private function uploadFacebookMedia(FacebookAdAccount $account, array $media, string $accessToken): ?string
    {
        if ($media['type'] === 'image') {
            $response = Http::attach(
                'file',
                file_get_contents($media['full_path']),
                basename($media['path'])
            )->post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/adimages", [
                'access_token' => $accessToken,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $images = $data['images'] ?? [];
                foreach ($images as $image) {
                    if (isset($image['hash'])) {
                        return $image['hash'];
                    }
                }
            }
        } else {
            $response = Http::attach(
                'file',
                file_get_contents($media['full_path']),
                basename($media['path'])
            )->post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/advideos", [
                'access_token' => $accessToken,
            ]);
            
            if ($response->successful()) {
                return $response->json()['id'] ?? null;
            }
        }
        
        return null;
    }

    private function mapCallToAction(string $cta): string
    {
        $ctaMap = [
            'Shop Now' => 'SHOP_NOW',
            'Learn More' => 'LEARN_MORE',
            'Sign Up' => 'SIGN_UP',
            'Book Now' => 'BOOK_TRAVEL',
            'Contact Us' => 'CONTACT_US',
            'Download' => 'DOWNLOAD',
            'Get Quote' => 'GET_QUOTE',
            'Apply Now' => 'APPLY_NOW',
        ];
        
        return $ctaMap[$cta] ?? 'LEARN_MORE';
    }

    private function createTikTokCampaign(TikTokAdAccount $account, array $data, array $mediaFiles)
    {
        $accessToken = Crypt::decryptString($account->access_token_encrypted);
        
        $objectiveMap = [
            'AWARENESS' => 'REACH',
            'CONSIDERATION' => 'TRAFFIC',
            'CONVERSION' => 'CONVERSIONS',
        ];
        
        $objective = $objectiveMap[$data['objective']] ?? 'TRAFFIC';
        
        // Step 1: Create campaign
        $response = Http::withHeaders([
            'Access-Token' => $accessToken,
        ])->post('https://business-api.tiktok.com/open_api/v1.3/campaign/create/', [
            'advertiser_id' => $account->advertiser_id,
            'campaign_name' => $data['campaign_name'],
            'objective_type' => $objective,
            'budget_mode' => 'BUDGET_MODE_DAY',
            'budget' => $data['daily_budget'],
            'operation_status' => 'DISABLE',
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('TikTok API request failed');
        }
        
        $responseData = $response->json();
        
        if (data_get($responseData, 'code') !== 0) {
            $error = data_get($responseData, 'message', 'Unknown error');
            throw new \Exception("TikTok Campaign Error: {$error}");
        }
        
        $campaignId = data_get($responseData, 'data.campaign_id');
        
        // Step 2: Upload media files
        $uploadedMediaIds = [];
        foreach ($mediaFiles as $media) {
            try {
                $mediaId = $this->uploadTikTokMedia($account, $media, $accessToken);
                if ($mediaId) {
                    $uploadedMediaIds[] = $mediaId;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to upload media to TikTok: " . $e->getMessage());
            }
        }
        
        if (empty($uploadedMediaIds)) {
            throw new \Exception("Failed to upload any media files to TikTok");
        }
        
        // Step 3: Create ad group
        $adGroupResponse = Http::withHeaders([
            'Access-Token' => $accessToken,
        ])->post('https://business-api.tiktok.com/open_api/v1.3/adgroup/create/', [
            'advertiser_id' => $account->advertiser_id,
            'campaign_id' => $campaignId,
            'adgroup_name' => $data['campaign_name'] . ' - Ad Group',
            'placement_type' => 'PLACEMENT_TYPE_AUTOMATIC',
            'location_ids' => ['6252001'],
            'budget_mode' => 'BUDGET_MODE_DAY',
            'budget' => $data['daily_budget'],
            'schedule_type' => 'SCHEDULE_START_END',
            'schedule_start_time' => now()->format('Y-m-d H:i:s'),
            'schedule_end_time' => now()->addMonths(3)->format('Y-m-d H:i:s'),
            'operation_status' => 'DISABLE',
        ]);
        
        if ($adGroupResponse->successful() && data_get($adGroupResponse->json(), 'code') === 0) {
            $adGroupId = data_get($adGroupResponse->json(), 'data.adgroup_id');
            
            // Step 4: Create ad with media
            foreach ($uploadedMediaIds as $index => $mediaId) {
                Http::withHeaders([
                    'Access-Token' => $accessToken,
                ])->post('https://business-api.tiktok.com/open_api/v1.3/ad/create/', [
                    'advertiser_id' => $account->advertiser_id,
                    'adgroup_id' => $adGroupId,
                    'ad_name' => $data['campaign_name'] . ' - Ad ' . ($index + 1),
                    'ad_format' => 'SINGLE_VIDEO',
                    'ad_text' => $data['primary_text'],
                    'call_to_action' => $data['call_to_action'] ?? 'LEARN_MORE',
                    'landing_page_url' => $data['website_url'] ?? 'https://example.com',
                    'video_id' => $mediaId,
                    'operation_status' => 'DISABLE',
                ]);
            }
        }
        
        return [
            'campaign_id' => $campaignId,
            'platform' => 'TikTok',
        ];
    }

    private function uploadTikTokMedia(TikTokAdAccount $account, array $media, string $accessToken): ?string
    {
        if ($media['type'] === 'video') {
            $response = Http::withHeaders([
                'Access-Token' => $accessToken,
            ])->attach(
                'video_file',
                file_get_contents($media['full_path']),
                basename($media['path'])
            )->post('https://business-api.tiktok.com/open_api/v1.3/file/video/ad/upload/', [
                'advertiser_id' => $account->advertiser_id,
                'upload_type' => 'UPLOAD_BY_FILE',
                'video_signature' => md5_file($media['full_path']),
            ]);
            
            if ($response->successful() && data_get($response->json(), 'code') === 0) {
                return data_get($response->json(), 'data.video_id');
            }
        } else {
            $response = Http::withHeaders([
                'Access-Token' => $accessToken,
            ])->attach(
                'image_file',
                file_get_contents($media['full_path']),
                basename($media['path'])
            )->post('https://business-api.tiktok.com/open_api/v1.3/file/image/ad/upload/', [
                'advertiser_id' => $account->advertiser_id,
                'upload_type' => 'UPLOAD_BY_FILE',
                'image_signature' => md5_file($media['full_path']),
            ]);
            
            if ($response->successful() && data_get($response->json(), 'code') === 0) {
                return data_get($response->json(), 'data.image_id');
            }
        }
        
        return null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
        
        Log::error('Job failed for campaign ' . $this->campaign->id . ': ' . $exception->getMessage());
    }
}
