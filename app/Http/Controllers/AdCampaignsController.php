<?php

namespace App\Http\Controllers;

use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AdCampaignsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get filter parameters
        $platform = $request->get('platform', 'all'); // all, facebook, tiktok
        $status = $request->get('status', 'all'); // all, active, paused
        $accountId = $request->get('account_id', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 15);
        
        $facebookAccounts = FacebookAdAccount::where('user_id', $user->id)->get();
        $tiktokAccounts = TikTokAdAccount::where('user_id', $user->id)->get();
        
        $campaigns = collect();
        $errors = [];
        
        // First, get locally created campaigns from database
        $localCampaignsQuery = Campaign::where('user_id', $user->id);
        
        // Apply date range filter for local campaigns
        if ($dateFrom) {
            $localCampaignsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $localCampaignsQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        $localCampaigns = $localCampaignsQuery
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($campaign) {
                $platformsDisplay = collect($campaign->platforms)->map(function ($p) {
                    return ucfirst($p);
                })->join(', ');
                
                return [
                    'id' => 'local_' . $campaign->id,
                    'name' => $campaign->name,
                    'platform' => $platformsDisplay,
                    'account_name' => 'Your Account',
                    'account_id' => 'local',
                    'status' => ucfirst($campaign->status),
                    'objective' => $campaign->objective,
                    'daily_budget' => $campaign->daily_budget,
                    'lifetime_budget' => 0,
                    'spend' => 0,
                    'impressions' => 0,
                    'clicks' => 0,
                    'conversions' => 0,
                    'ctr' => 0,
                    'cpc' => 0,
                    'cpm' => 0,
                    'is_local' => true,
                    'facebook_campaign_id' => $campaign->facebook_campaign_id,
                    'tiktok_campaign_id' => $campaign->tiktok_campaign_id,
                    'error_message' => $campaign->error_message,
                    'created_at' => $campaign->created_at,
                ];
            });
        
        $campaigns = $campaigns->merge($localCampaigns);
        
        // Fetch Facebook campaigns
        if (in_array($platform, ['all', 'facebook'])) {
            foreach ($facebookAccounts as $account) {
                if ($accountId === 'all' || $accountId == 'fb_' . $account->id) {
                    try {
                        $fbCampaigns = $this->getFacebookCampaigns($account, $dateFrom, $dateTo);
                        $campaigns = $campaigns->merge($fbCampaigns);
                        
                        if ($fbCampaigns->isEmpty()) {
                            $errors[] = "No campaigns found for Facebook account: {$account->ad_account_name}. Check logs for details.";
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error fetching Facebook campaigns: " . $e->getMessage();
                        \Log::error("Error in index fetching Facebook campaigns: " . $e->getMessage());
                    }
                }
            }
        }
        
        // Fetch TikTok campaigns
        if (in_array($platform, ['all', 'tiktok'])) {
            foreach ($tiktokAccounts as $account) {
                if ($accountId === 'all' || $accountId == 'tt_' . $account->id) {
                    try {
                        $ttCampaigns = $this->getTikTokCampaigns($account, $dateFrom, $dateTo);
                        $campaigns = $campaigns->merge($ttCampaigns);
                        
                        if ($ttCampaigns->isEmpty()) {
                            $errors[] = "No campaigns found for TikTok account: {$account->advertiser_name}";
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error fetching TikTok campaigns: " . $e->getMessage();
                        \Log::error("Error in index fetching TikTok campaigns: " . $e->getMessage());
                    }
                }
            }
        }
        
        // Apply status filter
        if ($status !== 'all') {
            $campaigns = $campaigns->filter(function ($campaign) use ($status) {
                $campaignStatus = strtolower($campaign['status']);
                
                // Map our statuses to the filter values
                if ($status === 'active') {
                    return $campaignStatus === 'active' || $campaignStatus === 'processing' || $campaignStatus === 'completed';
                } elseif ($status === 'paused') {
                    return $campaignStatus === 'paused' || $campaignStatus === 'pending';
                }
                
                return $campaignStatus === $status;
            });
        }
        
        // Sort by created order (local campaigns first, then by spend for API campaigns)
        $campaigns = $campaigns->sortByDesc(function ($campaign) {
            return $campaign['is_local'] ?? false ? 1 : 0;
        })->sortByDesc('spend');
        
        // Calculate totals (excluding local pending campaigns)
        $totalSpend = $campaigns->filter(fn($c) => !($c['is_local'] ?? false))->sum('spend');
        $totalImpressions = $campaigns->filter(fn($c) => !($c['is_local'] ?? false))->sum('impressions');
        $totalClicks = $campaigns->filter(fn($c) => !($c['is_local'] ?? false))->sum('clicks');
        $totalConversions = $campaigns->filter(fn($c) => !($c['is_local'] ?? false))->sum('conversions');
        
        // Manual pagination
        $page = $request->get('page', 1);
        $campaignsCollection = $campaigns->values();
        $total = $campaignsCollection->count();
        $campaigns = $campaignsCollection->forPage($page, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $campaigns,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('customer.ad-campaigns', compact(
            'paginator',
            'facebookAccounts',
            'tiktokAccounts',
            'platform',
            'status',
            'accountId',
            'dateFrom',
            'dateTo',
            'perPage',
            'totalSpend',
            'totalImpressions',
            'totalClicks',
            'totalConversions',
            'errors'
        ));
    }
    
    private function getFacebookCampaigns(FacebookAdAccount $account, $dateFrom = null, $dateTo = null)
    {
        // Check if token is expired before making API calls
        if (!$account->isTokenValid()) {
            throw new \Exception("Facebook Access Token has expired. Please reconnect your Facebook Ad Account.");
        }
        
        $cacheKey = "fb_campaigns_{$account->id}_" . md5($dateFrom . $dateTo);
        
        return Cache::remember($cacheKey, 300, function () use ($account, $dateFrom, $dateTo) {
            try {
                $accessToken = Crypt::decryptString($account->access_token_encrypted);
                
                \Log::info("Fetching Facebook campaigns for account: {$account->ad_account_id}");
                
                $response = Http::get("https://graph.facebook.com/v18.0/{$account->ad_account_id}/campaigns", [
                    'access_token' => $accessToken,
                    'fields' => 'id,name,status,objective,daily_budget,lifetime_budget,created_time',
                    'limit' => 100
                ]);
                
                \Log::info("Facebook API Response Status: " . $response->status());
                \Log::info("Facebook API Response Body: " . $response->body());
                
                if (!$response->successful()) {
                    \Log::error("Facebook API Error: " . $response->body());
                    
                    // Check for specific errors
                    $errorData = $response->json();
                    $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                    $errorCode = $errorData['error']['code'] ?? 0;
                    $errorSubcode = $errorData['error']['error_subcode'] ?? null;
                    
                    // Handle expired/invalid token (OAuth error code 190)
                    if ($errorCode == 190) {
                        // Mark account as inactive
                        $account->update(['is_active' => false]);
                        
                        throw new \Exception("Facebook Access Token has expired or is invalid. Please reconnect your Facebook Ad Account.");
                    }
                    
                    // Handle permission errors
                    if ($errorCode == 200 || str_contains($errorMessage, 'ads_management') || str_contains($errorMessage, 'ads_read')) {
                        throw new \Exception("Facebook Access Token is missing required permissions (ads_read or ads_management). Please regenerate your token with these permissions at https://developers.facebook.com/tools/accesstoken/");
                    }
                    
                    throw new \Exception("Facebook API Error: " . $errorMessage);
                }
                
                $campaignsData = $response->json()['data'] ?? [];
                
                \Log::info("Found " . count($campaignsData) . " campaigns");
                
                return collect($campaignsData)->map(function ($campaign) use ($account, $accessToken, $dateFrom, $dateTo) {
                    // Fetch insights for each campaign
                    $insights = $this->getFacebookCampaignInsights($campaign['id'], $accessToken, $dateFrom, $dateTo);
                    
                    return [
                        'id' => $campaign['id'],
                        'name' => $campaign['name'],
                        'platform' => 'Facebook',
                        'account_name' => $account->ad_account_name ?? $account->ad_account_id,
                        'account_id' => $account->id,
                        'status' => ucfirst(strtolower($campaign['status'] ?? 'unknown')),
                        'objective' => $campaign['objective'] ?? 'N/A',
                        'daily_budget' => isset($campaign['daily_budget']) ? $campaign['daily_budget'] / 100 : 0,
                        'lifetime_budget' => isset($campaign['lifetime_budget']) ? $campaign['lifetime_budget'] / 100 : 0,
                        'spend' => $insights['spend'] ?? 0,
                        'impressions' => $insights['impressions'] ?? 0,
                        'clicks' => $insights['clicks'] ?? 0,
                        'conversions' => $insights['conversions'] ?? 0,
                        'ctr' => $insights['ctr'] ?? 0,
                        'cpc' => $insights['cpc'] ?? 0,
                        'cpm' => $insights['cpm'] ?? 0,
                        'created_at' => $campaign['created_time'] ?? null,
                    ];
                });
                
            } catch (\Exception $e) {
                \Log::error("Exception fetching Facebook campaigns: " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                return collect();
            }
        });
    }
    
    private function getFacebookCampaignInsights($campaignId, $accessToken, $dateFrom = null, $dateTo = null)
    {
        try {
            $params = [
                'access_token' => $accessToken,
                'fields' => 'spend,impressions,clicks,actions,ctr,cpc,cpm',
            ];
            
            // If date range is provided, use it; otherwise use last 30 days
            if ($dateFrom && $dateTo) {
                $params['time_range'] = json_encode([
                    'since' => $dateFrom,
                    'until' => $dateTo
                ]);
            } else {
                $params['date_preset'] = 'last_30d';
            }
            
            $response = Http::get("https://graph.facebook.com/v18.0/{$campaignId}/insights", $params);
            
            if (!$response->successful()) {
                $errorData = $response->json();
                $errorCode = $errorData['error']['code'] ?? 0;
                
                // If token is expired, throw exception to be caught by parent method
                if ($errorCode == 190) {
                    throw new \Exception("Token expired while fetching insights");
                }
                
                return [];
            }
            
            if (empty($response->json()['data'])) {
                return [];
            }
            
            $data = $response->json()['data'][0];
            
            // Extract conversions from actions
            $conversions = 0;
            if (isset($data['actions'])) {
                foreach ($data['actions'] as $action) {
                    if (in_array($action['action_type'], ['purchase', 'lead', 'complete_registration'])) {
                        $conversions += (int)$action['value'];
                    }
                }
            }
            
            return [
                'spend' => (float)($data['spend'] ?? 0),
                'impressions' => (int)($data['impressions'] ?? 0),
                'clicks' => (int)($data['clicks'] ?? 0),
                'conversions' => $conversions,
                'ctr' => (float)($data['ctr'] ?? 0),
                'cpc' => (float)($data['cpc'] ?? 0),
                'cpm' => (float)($data['cpm'] ?? 0),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getTikTokCampaigns(TikTokAdAccount $account, $dateFrom = null, $dateTo = null)
    {
        // Check if token is expired before making API calls
        if (!$account->isTokenValid()) {
            throw new \Exception("TikTok Access Token has expired. Please reconnect your TikTok Ad Account.");
        }
        
        $cacheKey = "tt_campaigns_{$account->id}_" . md5($dateFrom . $dateTo);
        
        return Cache::remember($cacheKey, 300, function () use ($account, $dateFrom, $dateTo) {
            try {
                $accessToken = Crypt::decryptString($account->access_token_encrypted);
                
                $response = Http::withHeaders([
                    'Access-Token' => $accessToken,
                ])->get('https://business-api.tiktok.com/open_api/v1.3/campaign/get/', [
                    'advertiser_id' => $account->advertiser_id,
                    'page_size' => 100
                ]);
                
                if (!$response->successful()) {
                    return collect();
                }
                
                $responseData = $response->json();
                
                $responseCode = data_get($responseData, 'code');
                
                // Check for token expiration errors
                // TikTok error code 40102 = Access token expired
                // Error code 40104 = Invalid access token
                if (in_array($responseCode, [40102, 40104])) {
                    $account->update(['is_active' => false]);
                    throw new \Exception("TikTok Access Token has expired or is invalid. Please reconnect your TikTok Ad Account.");
                }
                
                if ($responseCode !== 0) {
                    $errorMessage = data_get($responseData, 'message', 'Unknown TikTok API error');
                    throw new \Exception("TikTok API Error: " . $errorMessage);
                }
                
                $campaignsData = data_get($responseData, 'data.list', []);
                
                return collect($campaignsData)->map(function ($campaign) use ($account, $accessToken, $dateFrom, $dateTo) {
                    // Fetch insights
                    $insights = $this->getTikTokCampaignInsights($campaign['campaign_id'], $account->advertiser_id, $accessToken, $dateFrom, $dateTo);
                    
                    return [
                        'id' => $campaign['campaign_id'],
                        'name' => $campaign['campaign_name'],
                        'platform' => 'TikTok',
                        'account_name' => $account->advertiser_name ?? $account->advertiser_id,
                        'account_id' => $account->id,
                        'status' => $this->mapTikTokStatus($campaign['operation_status'] ?? 'UNKNOWN'),
                        'objective' => $campaign['objective_type'] ?? 'N/A',
                        'daily_budget' => isset($campaign['budget']) ? (float)$campaign['budget'] : 0,
                        'lifetime_budget' => 0,
                        'spend' => $insights['spend'] ?? 0,
                        'impressions' => $insights['impressions'] ?? 0,
                        'clicks' => $insights['clicks'] ?? 0,
                        'conversions' => $insights['conversions'] ?? 0,
                        'ctr' => $insights['ctr'] ?? 0,
                        'cpc' => $insights['cpc'] ?? 0,
                        'cpm' => $insights['cpm'] ?? 0,
                        'created_at' => $campaign['create_time'] ?? null,
                    ];
                });
                
            } catch (\Exception $e) {
                return collect();
            }
        });
    }
    
    private function getTikTokCampaignInsights($campaignId, $advertiserId, $accessToken, $dateFrom = null, $dateTo = null)
    {
        try {
            // Use provided date range or default to last 30 days
            $startDate = $dateFrom ?? now()->subDays(30)->format('Y-m-d');
            $endDate = $dateTo ?? now()->format('Y-m-d');
            
            $response = Http::withHeaders([
                'Access-Token' => $accessToken,
            ])->get('https://business-api.tiktok.com/open_api/v1.3/report/integrated/get/', [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(['campaign_id']),
                'metrics' => json_encode(['spend', 'impressions', 'clicks', 'conversion', 'ctr', 'cpc', 'cpm']),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'filters' => json_encode([['field' => 'campaign_id', 'operator' => 'IN', 'values' => [$campaignId]]])
            ]);
            
            if (!$response->successful()) {
                return [];
            }
            
            $responseData = $response->json();
            
            if (data_get($responseData, 'code') !== 0 || empty(data_get($responseData, 'data.list'))) {
                return [];
            }
            
            $data = data_get($responseData, 'data.list.0.metrics', []);
            
            return [
                'spend' => (float)($data['spend'] ?? 0),
                'impressions' => (int)($data['impressions'] ?? 0),
                'clicks' => (int)($data['clicks'] ?? 0),
                'conversions' => (int)($data['conversion'] ?? 0),
                'ctr' => (float)($data['ctr'] ?? 0),
                'cpc' => (float)($data['cpc'] ?? 0),
                'cpm' => (float)($data['cpm'] ?? 0),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function mapTikTokStatus($status)
    {
        return match ($status) {
            'ENABLE' => 'Active',
            'DISABLE' => 'Paused',
            'DELETE' => 'Deleted',
            default => ucfirst(strtolower($status))
        };
    }
    
    public function refresh(Request $request)
    {
        $user = auth()->user();
        
        // Clear all campaign caches
        $facebookAccounts = FacebookAdAccount::where('user_id', $user->id)->get();
        $tiktokAccounts = TikTokAdAccount::where('user_id', $user->id)->get();
        
        foreach ($facebookAccounts as $account) {
            Cache::forget("fb_campaigns_{$account->id}");
        }
        
        foreach ($tiktokAccounts as $account) {
            Cache::forget("tt_campaigns_{$account->id}");
        }
        
        return redirect()
            ->route('app.ad-campaigns')
            ->with('success', 'Campaign data refreshed successfully!');
    }
    
    public function analyzeCampaigns(Request $request)
    {
        \Log::info('Campaign analysis requested', ['request_data' => $request->all()]);
        
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $user = auth()->user();
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        
        \Log::info('Analysis parameters', ['user_id' => $user->id, 'date_from' => $dateFrom, 'date_to' => $dateTo]);
        
        // Get user's AI settings
        $aiSetting = \App\Models\AiApiSetting::where('user_id', $user->id)->first();
        
        if (!$aiSetting || empty($aiSetting->openai_api_key_encrypted)) {
            \Log::warning('No OpenAI API key configured for user', ['user_id' => $user->id]);
            return response()->json([
                'error' => 'OpenAI API key not configured. Please configure your OpenAI settings first.',
                'redirect_url' => route('workspaces.ai-settings')
            ], 400);
        }
        
        try {
            $openaiApiKey = Crypt::decryptString($aiSetting->openai_api_key_encrypted);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt OpenAI API key', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Unable to decrypt your API key. Please reconfigure your OpenAI settings.',
                'redirect_url' => route('workspaces.ai-settings')
            ], 400);
        }
        
        // Fetch all campaigns data
        $facebookAccounts = FacebookAdAccount::where('user_id', $user->id)->get();
        $tiktokAccounts = TikTokAdAccount::where('user_id', $user->id)->get();
        
        \Log::info('Fetching campaigns', [
            'facebook_accounts' => $facebookAccounts->count(),
            'tiktok_accounts' => $tiktokAccounts->count()
        ]);
        
        $campaigns = collect();
        
        // Get local campaigns
        $localCampaignsQuery = Campaign::where('user_id', $user->id)->where('status', '!=', 'pending');
        
        if ($dateFrom) {
            $localCampaignsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $localCampaignsQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        $localCampaigns = $localCampaignsQuery->get()->map(function ($campaign) {
            return [
                'name' => $campaign->name,
                'platform' => implode(', ', $campaign->platforms),
                'status' => $campaign->status,
                'objective' => $campaign->objective,
                'daily_budget' => $campaign->daily_budget,
                'spend' => 0,
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'ctr' => 0,
                'cpc' => 0,
            ];
        });
        
        $campaigns = $campaigns->merge($localCampaigns);
        
        // Fetch Facebook campaigns
        foreach ($facebookAccounts as $account) {
            try {
                $fbCampaigns = $this->getFacebookCampaigns($account, $dateFrom, $dateTo);
                $campaigns = $campaigns->merge($fbCampaigns->map(function($c) {
                    return [
                        'name' => $c['name'],
                        'platform' => $c['platform'],
                        'status' => $c['status'],
                        'objective' => $c['objective'],
                        'daily_budget' => $c['daily_budget'],
                        'spend' => $c['spend'],
                        'impressions' => $c['impressions'],
                        'clicks' => $c['clicks'],
                        'conversions' => $c['conversions'],
                        'ctr' => $c['ctr'],
                        'cpc' => $c['cpc'],
                    ];
                }));
            } catch (\Exception $e) {
                \Log::error("Error fetching FB campaigns for analysis: " . $e->getMessage());
            }
        }
        
        // Fetch TikTok campaigns
        foreach ($tiktokAccounts as $account) {
            try {
                $ttCampaigns = $this->getTikTokCampaigns($account, $dateFrom, $dateTo);
                $campaigns = $campaigns->merge($ttCampaigns->map(function($c) {
                    return [
                        'name' => $c['name'],
                        'platform' => $c['platform'],
                        'status' => $c['status'],
                        'objective' => $c['objective'],
                        'daily_budget' => $c['daily_budget'],
                        'spend' => $c['spend'],
                        'impressions' => $c['impressions'],
                        'clicks' => $c['clicks'],
                        'conversions' => $c['conversions'],
                        'ctr' => $c['ctr'],
                        'cpc' => $c['cpc'],
                    ];
                }));
            } catch (\Exception $e) {
                \Log::error("Error fetching TikTok campaigns for analysis: " . $e->getMessage());
            }
        }
        
        if ($campaigns->isEmpty()) {
            \Log::warning('No campaigns found for analysis', ['user_id' => $user->id]);
            return response()->json([
                'error' => 'No campaign data available for analysis.'
            ], 400);
        }
        
        \Log::info('Campaigns collected for analysis', ['count' => $campaigns->count()]);
        
        // Calculate aggregate metrics
        $totalSpend = $campaigns->sum('spend');
        $totalImpressions = $campaigns->sum('impressions');
        $totalClicks = $campaigns->sum('clicks');
        $totalConversions = $campaigns->sum('conversions');
        $avgCTR = $campaigns->avg('ctr');
        $avgCPC = $campaigns->avg('cpc');
        
        \Log::info('Campaign metrics', [
            'total_spend' => $totalSpend,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'total_conversions' => $totalConversions
        ]);
        
        // Prepare prompt for AI analysis
        $dateRangeText = $dateFrom && $dateTo 
            ? "from {$dateFrom} to {$dateTo}" 
            : "in the last 30 days";
        
        $prompt = "You are an expert media buyer analyzing advertising campaign data. Here's the campaign performance data {$dateRangeText}:\n\n";
        $prompt .= "OVERALL METRICS:\n";
        $prompt .= "- Total Spend: $" . number_format($totalSpend, 2) . "\n";
        $prompt .= "- Total Impressions: " . number_format($totalImpressions) . "\n";
        $prompt .= "- Total Clicks: " . number_format($totalClicks) . "\n";
        $prompt .= "- Total Conversions: " . number_format($totalConversions) . "\n";
        $prompt .= "- Average CTR: " . number_format($avgCTR, 2) . "%\n";
        $prompt .= "- Average CPC: $" . number_format($avgCPC, 2) . "\n\n";
        
        $prompt .= "INDIVIDUAL CAMPAIGNS:\n";
        foreach ($campaigns as $index => $campaign) {
            $prompt .= ($index + 1) . ". {$campaign['name']} ({$campaign['platform']})\n";
            $prompt .= "   Status: {$campaign['status']}\n";
            $prompt .= "   Objective: {$campaign['objective']}\n";
            $prompt .= "   Daily Budget: $" . number_format($campaign['daily_budget'], 2) . "\n";
            $prompt .= "   Spend: $" . number_format($campaign['spend'], 2) . "\n";
            $prompt .= "   Impressions: " . number_format($campaign['impressions']) . "\n";
            $prompt .= "   Clicks: " . number_format($campaign['clicks']) . "\n";
            $prompt .= "   Conversions: " . number_format($campaign['conversions']) . "\n";
            $prompt .= "   CTR: " . number_format($campaign['ctr'], 2) . "%\n";
            $prompt .= "   CPC: $" . number_format($campaign['cpc'], 2) . "\n\n";
        }
        
        $prompt .= "Please provide a comprehensive media buyer analysis with the following sections:\n\n";
        $prompt .= "1. PERFORMANCE OVERVIEW: Brief summary of overall campaign performance\n";
        $prompt .= "2. TOP PERFORMERS: Which campaigns are performing best and why\n";
        $prompt .= "3. UNDERPERFORMERS: Which campaigns are underperforming and need attention\n";
        $prompt .= "4. SCALING RECOMMENDATIONS: Which campaigns should be scaled up (with specific budget increase recommendations)\n";
        $prompt .= "5. STOP/PAUSE RECOMMENDATIONS: Which campaigns should be paused or stopped (with clear reasons)\n";
        $prompt .= "6. OPTIMIZATION OPPORTUNITIES: Specific actionable recommendations to improve performance\n";
        $prompt .= "7. BUDGET ALLOCATION: Recommendations on how to redistribute budget across campaigns\n\n";
        $prompt .= "Format your response in clear sections with bullet points. Be specific with dollar amounts and percentages.";
        
        \Log::info('Sending request to OpenAI', [
            'model' => $aiSetting->openai_model ?? 'gpt-4',
            'prompt_length' => strlen($prompt)
        ]);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $aiSetting->openai_model ?? 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert media buyer and advertising analyst with 10+ years of experience managing multi-million dollar ad campaigns across Facebook, TikTok, and other platforms. You provide data-driven, actionable insights.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);
            
            \Log::info('OpenAI response received', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);
            
            if (!$response->successful()) {
                \Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Failed to generate analysis. Please try again.'
                ], 500);
            }
            
            $analysis = $response->json()['choices'][0]['message']['content'] ?? '';
            
            \Log::info('Analysis generated successfully', ['length' => strlen($analysis)]);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'campaigns_analyzed' => $campaigns->count(),
                'total_spend' => $totalSpend,
                'total_conversions' => $totalConversions,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error generating campaign analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while generating analysis. Please try again.'
            ], 500);
        }
    }
}
