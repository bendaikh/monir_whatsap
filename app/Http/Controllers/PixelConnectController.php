<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class PixelConnectController extends Controller
{
    public function index()
    {
        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return redirect()->route('stores.dashboard')->with('error', 'Please select a store first.');
        }

        $activeStore = Store::findOrFail($activeStoreId);

        return view('customer.pixel-connect', compact('activeStore'));
    }

    public function saveFacebookPixel(Request $request)
    {
        $request->validate([
            'facebook_pixel_id' => 'required|string|max:255',
        ]);

        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'facebook_pixel_id' => $request->facebook_pixel_id,
            'facebook_pixel_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel connected successfully!'
        ]);
    }

    public function disconnectFacebookPixel()
    {
        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'facebook_pixel_id' => null,
            'facebook_pixel_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel disconnected successfully!'
        ]);
    }

    public function saveTikTokPixel(Request $request)
    {
        $request->validate([
            'tiktok_pixel_id' => 'required|string|max:255',
        ]);

        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'tiktok_pixel_id' => $request->tiktok_pixel_id,
            'tiktok_pixel_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok Pixel connected successfully!'
        ]);
    }

    public function disconnectTikTokPixel()
    {
        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $store->update([
            'tiktok_pixel_id' => null,
            'tiktok_pixel_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TikTok Pixel disconnected successfully!'
        ]);
    }
}
