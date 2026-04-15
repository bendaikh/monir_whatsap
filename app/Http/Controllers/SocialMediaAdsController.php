<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAdsSetting;
use App\Models\FacebookAdAccount;
use App\Models\TikTokAdAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocialMediaAdsController extends Controller
{
    public function facebookAds()
    {
        $user = auth()->user();
        $settings = SocialMediaAdsSetting::firstOrCreate(['user_id' => $user->id]);
        $adAccounts = FacebookAdAccount::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.facebook-ads', compact('settings', 'adAccounts'));
    }

    public function tiktokAds()
    {
        $user = auth()->user();
        $settings = SocialMediaAdsSetting::firstOrCreate(['user_id' => $user->id]);
        $adAccounts = TikTokAdAccount::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.tiktok-ads', compact('settings', 'adAccounts'));
    }

    public function saveFacebookSettings(Request $request)
    {
        $validated = $request->validate([
            'facebook_access_token' => 'required|string|max:2048',
            'facebook_ad_account_id' => 'required|string|max:255',
            'facebook_page_id' => 'nullable|string|max:255',
            'facebook_business_id' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        
        // Check if this ad account is already connected
        $existingAccount = FacebookAdAccount::where('user_id', $user->id)
            ->where('ad_account_id', $validated['facebook_ad_account_id'])
            ->first();
            
        if ($existingAccount) {
            return redirect()
                ->route('app.facebook-ads')
                ->with('error', 'This ad account is already connected.');
        }
        
        // Encrypt and store the access token
        $encryptedToken = Crypt::encryptString(trim($validated['facebook_access_token']));
        
        // Normalize the ad account ID format (ensure it starts with act_)
        $adAccountId = $validated['facebook_ad_account_id'];
        $adAccountId = str_replace('act=', 'act_', $adAccountId);
        if (!str_starts_with($adAccountId, 'act_')) {
            $adAccountId = 'act_' . $adAccountId;
        }
        
        // Try to fetch account name from Facebook API
        $adAccountName = null;
        try {
            $response = Http::get("https://graph.facebook.com/v18.0/{$adAccountId}", [
                'access_token' => trim($validated['facebook_access_token']),
                'fields' => 'name,account_id'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $adAccountName = $data['name'] ?? null;
            }
        } catch (\Exception $e) {
            // Continue without the name if API call fails
        }
        
        // Create new ad account
        FacebookAdAccount::create([
            'user_id' => $user->id,
            'access_token_encrypted' => $encryptedToken,
            'ad_account_id' => $adAccountId,
            'ad_account_name' => $adAccountName,
            'page_id' => $validated['facebook_page_id'],
            'business_id' => $validated['facebook_business_id'],
            'token_expires_at' => Carbon::now()->addDays(60),
            'is_active' => true,
        ]);

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Facebook Ad Account connected successfully!');
    }

    public function testFacebookConnection(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:facebook_ad_accounts,id'
        ]);
        
        $account = FacebookAdAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$account) {
            return redirect()
                ->route('app.facebook-ads')
                ->with('error', 'Ad account not found.');
        }

        try {
            $accessToken = Crypt::decryptString($account->access_token_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.facebook-ads')
                ->with('error', 'Unable to decrypt the saved token.');
        }

        $response = Http::get('https://graph.facebook.com/v18.0/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email'
        ]);

        if (!$response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('app.facebook-ads')
                ->with('error', is_string($message) ? $message : 'Facebook API request failed.');
        }

        $userData = $response->json();

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Connection successful! Connected as: ' . ($userData['name'] ?? 'Unknown'));
    }

    public function disconnectFacebook(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:facebook_ad_accounts,id'
        ]);
        
        $account = FacebookAdAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if ($account) {
            $account->delete();
        }

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Facebook Ad Account disconnected successfully.');
    }

    public function saveTikTokSettings(Request $request)
    {
        $validated = $request->validate([
            'tiktok_access_token' => 'required|string|max:2048',
            'tiktok_advertiser_id' => 'required|string|max:255',
            'tiktok_app_id' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        
        // Check if this advertiser is already connected
        $existingAccount = TikTokAdAccount::where('user_id', $user->id)
            ->where('advertiser_id', $validated['tiktok_advertiser_id'])
            ->first();
            
        if ($existingAccount) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'This advertiser account is already connected.');
        }
        
        // Encrypt and store the access token
        $encryptedToken = Crypt::encryptString(trim($validated['tiktok_access_token']));
        
        // Try to fetch advertiser name from TikTok API
        $advertiserName = null;
        try {
            $response = Http::withHeaders([
                'Access-Token' => trim($validated['tiktok_access_token']),
            ])->get('https://business-api.tiktok.com/open_api/v1.3/advertiser/info/', [
                'advertiser_ids' => json_encode([$validated['tiktok_advertiser_id']])
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                if (data_get($data, 'code') === 0) {
                    $advertiserName = data_get($data, 'data.list.0.name');
                }
            }
        } catch (\Exception $e) {
            // Continue without the name if API call fails
        }
        
        // Create new ad account
        TikTokAdAccount::create([
            'user_id' => $user->id,
            'access_token_encrypted' => $encryptedToken,
            'advertiser_id' => $validated['tiktok_advertiser_id'],
            'advertiser_name' => $advertiserName,
            'app_id' => $validated['tiktok_app_id'],
            'token_expires_at' => Carbon::now()->addDays(60),
            'is_active' => true,
        ]);

        return redirect()
            ->route('app.tiktok-ads')
            ->with('success', 'TikTok Ad Account connected successfully!');
    }

    public function testTikTokConnection(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:tiktok_ad_accounts,id'
        ]);
        
        $account = TikTokAdAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$account) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'Ad account not found.');
        }

        try {
            $accessToken = Crypt::decryptString($account->access_token_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'Unable to decrypt the saved token.');
        }

        $response = Http::withHeaders([
            'Access-Token' => $accessToken,
        ])->get('https://business-api.tiktok.com/open_api/v1.3/advertiser/info/', [
            'advertiser_ids' => json_encode([$account->advertiser_id])
        ]);

        if (!$response->successful()) {
            $message = data_get($response->json(), 'message') ?: $response->body();

            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', is_string($message) ? $message : 'TikTok API request failed.');
        }

        $responseData = $response->json();
        
        if (data_get($responseData, 'code') === 0) {
            $advertiserName = data_get($responseData, 'data.list.0.name', 'Unknown');
            
            return redirect()
                ->route('app.tiktok-ads')
                ->with('success', 'Connection successful! Connected advertiser: ' . $advertiserName);
        }

        return redirect()
            ->route('app.tiktok-ads')
            ->with('error', data_get($responseData, 'message', 'TikTok connection test failed.'));
    }

    public function disconnectTikTok(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:tiktok_ad_accounts,id'
        ]);
        
        $account = TikTokAdAccount::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if ($account) {
            $account->delete();
        }

        return redirect()
            ->route('app.tiktok-ads')
            ->with('success', 'TikTok Ad Account disconnected successfully.');
    }
}
