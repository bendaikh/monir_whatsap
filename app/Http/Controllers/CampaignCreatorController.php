<?php

namespace App\Http\Controllers;

use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use App\Models\AiApiSetting;
use App\Models\Campaign;
use App\Jobs\CreateCampaignJob;
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
        
        // Store uploaded media files
        $mediaFilePaths = [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $index => $file) {
                $path = $file->store('campaign-media', 'public');
                $mediaFilePaths[] = $path;
            }
        }
        
        // Add media file paths to the data
        $validated['media_files_paths'] = $mediaFilePaths;
        
        // Create campaign record immediately with status 'pending'
        $campaign = Campaign::create([
            'user_id' => $user->id,
            'name' => $validated['campaign_name'],
            'objective' => $validated['objective'],
            'daily_budget' => $validated['daily_budget'],
            'platforms' => $validated['platforms'],
            'status' => 'pending',
            'campaign_data' => $validated,
        ]);
        
        // Dispatch background job to actually create the campaign
        CreateCampaignJob::dispatch($campaign);
        
        return redirect()
            ->route('app.ad-campaigns')
            ->with('success', 'Campaign "' . $validated['campaign_name'] . '" is being created! You can create more campaigns while this one is being generated.');
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
