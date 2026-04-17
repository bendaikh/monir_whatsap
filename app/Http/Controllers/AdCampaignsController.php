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
        
        $facebookAccounts = FacebookAdAccount::where('user_id', $user->id)->get();
        $tiktokAccounts = TikTokAdAccount::where('user_id', $user->id)->get();
        
        $campaigns = collect();
        $errors = [];
        
        // First, get locally created campaigns from database
        $localCampaigns = Campaign::where('user_id', $user->id)
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
                ];
            });
        
        $campaigns = $campaigns->merge($localCampaigns);
        
        // Fetch Facebook campaigns
        if (in_array($platform, ['all', 'facebook'])) {
            foreach ($facebookAccounts as $account) {
                if ($accountId === 'all' || $accountId == 'fb_' . $account->id) {
                    try {
                        $fbCampaigns = $this->getFacebookCampaigns($account);
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
                        $ttCampaigns = $this->getTikTokCampaigns($account);
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
        
        return view('customer.ad-campaigns', compact(
            'campaigns',
            'facebookAccounts',
            'tiktokAccounts',
            'platform',
            'status',
            'accountId',
            'totalSpend',
            'totalImpressions',
            'totalClicks',
            'totalConversions',
            'errors'
        ));
    }
    
    private function getFacebookCampaigns(FacebookAdAccount $account)
    {
        // Check if token is expired before making API calls
        if (!$account->isTokenValid()) {
            throw new \Exception("Facebook Access Token has expired. Please reconnect your Facebook Ad Account.");
        }
        
        $cacheKey = "fb_campaigns_{$account->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($account) {
            try {
                $accessToken = Crypt::decryptString($account->access_token_encrypted);
                
                \Log::info("Fetching Facebook campaigns for account: {$account->ad_account_id}");
                
                $response = Http::get("https://graph.facebook.com/v18.0/{$account->ad_account_id}/campaigns", [
                    'access_token' => $accessToken,
                    'fields' => 'id,name,status,objective,daily_budget,lifetime_budget',
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
                
                return collect($campaignsData)->map(function ($campaign) use ($account, $accessToken) {
                    // Fetch insights for each campaign
                    $insights = $this->getFacebookCampaignInsights($campaign['id'], $accessToken);
                    
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
                    ];
                });
                
            } catch (\Exception $e) {
                \Log::error("Exception fetching Facebook campaigns: " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                return collect();
            }
        });
    }
    
    private function getFacebookCampaignInsights($campaignId, $accessToken)
    {
        try {
            $response = Http::get("https://graph.facebook.com/v18.0/{$campaignId}/insights", [
                'access_token' => $accessToken,
                'fields' => 'spend,impressions,clicks,actions,ctr,cpc,cpm',
                'date_preset' => 'last_30d'
            ]);
            
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
    
    private function getTikTokCampaigns(TikTokAdAccount $account)
    {
        // Check if token is expired before making API calls
        if (!$account->isTokenValid()) {
            throw new \Exception("TikTok Access Token has expired. Please reconnect your TikTok Ad Account.");
        }
        
        $cacheKey = "tt_campaigns_{$account->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($account) {
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
                
                return collect($campaignsData)->map(function ($campaign) use ($account, $accessToken) {
                    // Fetch insights
                    $insights = $this->getTikTokCampaignInsights($campaign['campaign_id'], $account->advertiser_id, $accessToken);
                    
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
                    ];
                });
                
            } catch (\Exception $e) {
                return collect();
            }
        });
    }
    
    private function getTikTokCampaignInsights($campaignId, $advertiserId, $accessToken)
    {
        try {
            $response = Http::withHeaders([
                'Access-Token' => $accessToken,
            ])->get('https://business-api.tiktok.com/open_api/v1.3/report/integrated/get/', [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(['campaign_id']),
                'metrics' => json_encode(['spend', 'impressions', 'clicks', 'conversion', 'ctr', 'cpc', 'cpm']),
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
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
}
