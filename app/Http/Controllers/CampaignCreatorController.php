<?php

namespace App\Http\Controllers;

use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use App\Models\AiApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignCreatorController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $facebookAccounts = FacebookAdAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
            
        $tiktokAccounts = TikTokAdAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        // Check if user has AI settings configured
        $aiSetting = AiApiSetting::where('user_id', $user->id)->first();
        $hasOpenAI = $aiSetting && !empty($aiSetting->openai_api_key_encrypted);
        
        return view('customer.campaign-creator', compact('facebookAccounts', 'tiktokAccounts', 'hasOpenAI'));
    }
    
    public function getFacebookPages(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:facebook_ad_accounts,id'
        ]);
        
        $account = FacebookAdAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        
        try {
            $accessToken = Crypt::decryptString($account->access_token_encrypted);
            
            // Fetch pages from Facebook API
            $response = Http::get('https://graph.facebook.com/v18.0/me/accounts', [
                'access_token' => $accessToken,
                'fields' => 'id,name,access_token'
            ]);
            
            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch pages from Facebook'
                ], 500);
            }
            
            $pages = $response->json()['data'] ?? [];
            
            return response()->json([
                'pages' => $pages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching Facebook pages: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching pages'
            ], 500);
        }
    }
    
    public function generateCopy(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string|max:1000',
            'target_audience' => 'nullable|string|max:500',
            'campaign_objective' => 'required|string|in:AWARENESS,CONSIDERATION,CONVERSION',
            'tone' => 'required|string|in:professional,casual,exciting,urgent,friendly',
            'content_type' => 'required|string|in:headline,primary_text,description,cta',
        ]);
        
        try {
            // Get user's AI settings
            $aiSetting = \App\Models\AiApiSetting::where('user_id', $request->user()->id)->first();
            
            if (!$aiSetting || empty($aiSetting->openai_api_key_encrypted)) {
                return response()->json([
                    'error' => 'OpenAI API key not configured. Please configure your OpenAI settings first.',
                    'redirect_url' => route('app.ai-settings')
                ], 400);
            }
            
            try {
                $openaiApiKey = Crypt::decryptString($aiSetting->openai_api_key_encrypted);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Unable to decrypt your API key. Please reconfigure your OpenAI settings.',
                    'redirect_url' => route('app.ai-settings')
                ], 400);
            }
            
            $prompt = $this->buildPrompt($validated);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $aiSetting->openai_model ?? 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert advertising copywriter who creates compelling ad copy that converts. You understand platform-specific best practices for Facebook and TikTok ads.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.8,
                'max_tokens' => 500,
            ]);
            
            if (!$response->successful()) {
                Log::error('OpenAI API Error: ' . $response->body());
                return response()->json([
                    'error' => 'Failed to generate content. Please try again.'
                ], 500);
            }
            
            $generatedText = $response->json()['choices'][0]['message']['content'] ?? '';
            
            return response()->json([
                'generated_text' => trim($generatedText)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating copy: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while generating content. Please try again.'
            ], 500);
        }
    }
    
    public function createCampaign(Request $request)
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'objective' => 'required|string',
            'daily_budget' => 'required|numeric|min:1',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'required|string|in:facebook,tiktok',
            'facebook_account_id' => 'required_if:platforms.*,facebook|nullable|exists:facebook_ad_accounts,id',
            'facebook_page_id' => 'nullable|string|max:255',
            'tiktok_account_id' => 'required_if:platforms.*,tiktok|nullable|exists:tiktok_ad_accounts,id',
            'primary_text' => 'required|string|max:2000',
            'headline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'call_to_action' => 'nullable|string|max:50',
            'website_url' => 'nullable|url|max:500',
            'media_files' => 'required|array|min:1|max:10',
            'media_files.*' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:102400', // 100MB max
            'target_countries' => 'nullable|array',
            'target_countries.*' => 'string|size:2',
        ]);
        
        $user = auth()->user();
        $results = [];
        $errors = [];
        
        // Store uploaded media files
        $mediaFiles = [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $index => $file) {
                $path = $file->store('campaign-media', 'public');
                $mediaFiles[] = [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'mime_type' => $file->getMimeType(),
                    'type' => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                ];
            }
        }
        
        foreach ($validated['platforms'] as $platform) {
            try {
                if ($platform === 'facebook' && $validated['facebook_account_id']) {
                    $account = FacebookAdAccount::where('id', $validated['facebook_account_id'])
                        ->where('user_id', $user->id)
                        ->firstOrFail();
                    
                    $result = $this->createFacebookCampaign($account, $validated, $mediaFiles);
                    $results['facebook'] = $result;
                    
                } elseif ($platform === 'tiktok' && $validated['tiktok_account_id']) {
                    $account = TikTokAdAccount::where('id', $validated['tiktok_account_id'])
                        ->where('user_id', $user->id)
                        ->firstOrFail();
                    
                    $result = $this->createTikTokCampaign($account, $validated, $mediaFiles);
                    $results['tiktok'] = $result;
                }
            } catch (\Exception $e) {
                $errors[$platform] = $e->getMessage();
                Log::error("Error creating {$platform} campaign: " . $e->getMessage());
            }
        }
        
        // Clean up temporary files
        foreach ($mediaFiles as $media) {
            if (file_exists($media['full_path'])) {
                @unlink($media['full_path']);
            }
        }
        
        if (!empty($results)) {
            return redirect()
                ->route('app.ad-campaigns')
                ->with('success', 'Campaign(s) created successfully with media! It may take a few minutes to appear in your dashboard.');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create campaigns: ' . implode(', ', $errors));
        }
    }
    
    private function createFacebookCampaign(FacebookAdAccount $account, array $data, array $mediaFiles)
    {
        $accessToken = Crypt::decryptString($account->access_token_encrypted);
        
        // Map objectives (Facebook now uses OUTCOME_ prefix)
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
            'is_adset_budget_sharing_enabled' => false, // Required field for non-CBO campaigns
        ]);
        
        if (!$response->successful()) {
            $error = $response->json()['error']['message'] ?? 'Unknown error';
            $errorDetails = json_encode($response->json());
            Log::error("Facebook Campaign Creation Failed: {$errorDetails}");
            throw new \Exception("Facebook Campaign Error: {$error}");
        }
        
        $campaignId = $response->json()['id'];
        Log::info("Created Facebook campaign: {$campaignId}");
        
        // Step 2: Upload media files
        $uploadedMedia = [];
        foreach ($mediaFiles as $media) {
            try {
                Log::info("Uploading media: {$media['path']} (type: {$media['type']})");
                $hash = $this->uploadFacebookMedia($account, $media, $accessToken);
                if ($hash) {
                    $uploadedMedia[] = [
                        'hash' => $hash,
                        'type' => $media['type']
                    ];
                    Log::info("Media uploaded successfully: {$hash}");
                }
            } catch (\Exception $e) {
                Log::warning("Failed to upload media to Facebook: " . $e->getMessage());
            }
        }
        
        if (empty($uploadedMedia)) {
            throw new \Exception("Failed to upload any media files to Facebook");
        }
        
        // Prepare targeting - use countries from form or default to US
        $targetCountries = !empty($data['target_countries']) ? $data['target_countries'] : ['US'];
        
        // Step 3: Create ad set
        Log::info("Creating ad set for campaign: {$campaignId}");
        $adSetData = [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'] . ' - Ad Set',
            'campaign_id' => $campaignId,
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'REACH',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP', // Required: automatic bidding
            'daily_budget' => (int)($data['daily_budget'] * 100), // Convert to cents
            'targeting' => json_encode(['geo_locations' => ['countries' => $targetCountries]]),
            'status' => 'PAUSED',
        ];
        
        $adSetResponse = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/adsets", $adSetData);
        
        if (!$adSetResponse->successful()) {
            $errorDetails = json_encode($adSetResponse->json());
            Log::error("Facebook Ad Set Creation Failed: {$errorDetails}");
            throw new \Exception("Failed to create ad set: " . ($adSetResponse->json()['error']['message'] ?? 'Unknown error'));
        }
        
        $adSetId = $adSetResponse->json()['id'];
        Log::info("Created ad set: {$adSetId}");
        
        // Determine page_id - use from form or fall back to account page_id
        $pageId = $data['facebook_page_id'] ?? $account->page_id;
        
        if (!$pageId) {
            throw new \Exception("No Facebook page selected. Please select a page to create ads.");
        }
        
        // Step 4: Create ad creative
        Log::info("Creating ad creative with page_id: {$pageId}");
        
        // Determine whether to use image or video
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
        
        // Add media based on type
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
            $errorDetails = json_encode($creativeResponse->json());
            Log::error("Facebook Creative Creation Failed: {$errorDetails}");
            throw new \Exception("Failed to create ad creative: " . ($creativeResponse->json()['error']['message'] ?? 'Unknown error'));
        }
        
        $creativeId = $creativeResponse->json()['id'];
        Log::info("Created creative: {$creativeId}");
        
        // Step 5: Create ad
        Log::info("Creating ad");
        $adResponse = Http::post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/ads", [
            'access_token' => $accessToken,
            'name' => $data['campaign_name'] . ' - Ad',
            'adset_id' => $adSetId,
            'creative' => json_encode(['creative_id' => $creativeId]),
            'status' => 'PAUSED',
        ]);
        
        if (!$adResponse->successful()) {
            $errorDetails = json_encode($adResponse->json());
            Log::error("Facebook Ad Creation Failed: {$errorDetails}");
            throw new \Exception("Failed to create ad: " . ($adResponse->json()['error']['message'] ?? 'Unknown error'));
        }
        
        $adId = $adResponse->json()['id'];
        Log::info("Created ad: {$adId}");
        
        return [
            'campaign_id' => $campaignId,
            'platform' => 'Facebook',
            'name' => $data['campaign_name'],
            'status' => 'PAUSED',
            'media_count' => count($uploadedMedia),
            'ad_id' => $adId,
        ];
    }
    
    private function uploadFacebookMedia(FacebookAdAccount $account, array $media, string $accessToken): ?string
    {
        if ($media['type'] === 'image') {
            // Upload image
            $response = Http::attach(
                'file',
                file_get_contents($media['full_path']),
                basename($media['path'])
            )->post("https://graph.facebook.com/v18.0/{$account->ad_account_id}/adimages", [
                'access_token' => $accessToken,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                // Get the hash from the response
                $images = $data['images'] ?? [];
                foreach ($images as $image) {
                    if (isset($image['hash'])) {
                        return $image['hash'];
                    }
                }
            }
        } else {
            // Upload video
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
        
        // Map objectives
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
        Log::info("Created TikTok campaign: {$campaignId}");
        
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
        
        // Step 3: Create ad group (similar to Facebook ad set)
        $adGroupResponse = Http::withHeaders([
            'Access-Token' => $accessToken,
        ])->post('https://business-api.tiktok.com/open_api/v1.3/adgroup/create/', [
            'advertiser_id' => $account->advertiser_id,
            'campaign_id' => $campaignId,
            'adgroup_name' => $data['campaign_name'] . ' - Ad Group',
            'placement_type' => 'PLACEMENT_TYPE_AUTOMATIC',
            'location_ids' => ['6252001'], // US
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
            'name' => $data['campaign_name'],
            'status' => 'DISABLED',
            'media_count' => count($uploadedMediaIds),
        ];
    }
    
    private function uploadTikTokMedia(TikTokAdAccount $account, array $media, string $accessToken): ?string
    {
        if ($media['type'] === 'video') {
            // TikTok requires video upload
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
            // Upload image
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
    
    private function buildPrompt(array $data): string
    {
        $contentTypeLabels = [
            'headline' => 'attention-grabbing headline (max 40 characters)',
            'primary_text' => 'compelling primary ad text (max 125 words)',
            'description' => 'concise description (max 30 words)',
            'cta' => 'call-to-action phrase (max 5 words)',
        ];
        
        $objectiveContext = [
            'AWARENESS' => 'focused on building brand awareness and reach',
            'CONSIDERATION' => 'focused on driving traffic and engagement',
            'CONVERSION' => 'focused on driving sales and conversions',
        ];
        
        $toneContext = [
            'professional' => 'Use a professional, authoritative tone',
            'casual' => 'Use a casual, friendly conversational tone',
            'exciting' => 'Use an exciting, energetic tone with enthusiasm',
            'urgent' => 'Use an urgent tone that creates FOMO (fear of missing out)',
            'friendly' => 'Use a warm, friendly, and approachable tone',
        ];
        
        $contentType = $contentTypeLabels[$data['content_type']];
        $objective = $objectiveContext[$data['campaign_objective']];
        $tone = $toneContext[$data['tone']];
        
        $prompt = "Create a {$contentType} for a social media ad campaign.\n\n";
        $prompt .= "Product/Service: {$data['product_name']}\n";
        $prompt .= "Description: {$data['product_description']}\n";
        
        if (!empty($data['target_audience'])) {
            $prompt .= "Target Audience: {$data['target_audience']}\n";
        }
        
        $prompt .= "Campaign Objective: {$objective}\n";
        $prompt .= "Tone: {$tone}\n\n";
        $prompt .= "Generate ONLY the ad copy text without any labels, explanations, or quotation marks. Make it compelling and ready to use.";
        
        return $prompt;
    }
}
