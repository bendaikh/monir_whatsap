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
            'name' => 'nullable|string|max:100',
        ]);

        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $pixelId = trim($request->facebook_pixel_id);
        $name = trim($request->name ?? '') ?: ('Pixel ' . (count($store->facebook_pixels ?? []) + 1));

        $pixels = collect($store->facebook_pixels ?? []);

        if ($pixels->contains('id', $pixelId)) {
            return response()->json(['error' => 'This Pixel ID is already connected'], 422);
        }

        $pixels->push([
            'id' => $pixelId,
            'name' => $name,
            'enabled' => true,
        ]);

        $store->update(['facebook_pixels' => $pixels->values()->all()]);
        $store->syncLegacyFacebookPixel();

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel added successfully!',
            'pixels' => $store->fresh()->facebook_pixels,
        ]);
    }

    public function disconnectFacebookPixel(Request $request)
    {
        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $pixelId = $request->input('facebook_pixel_id');

        if ($pixelId) {
            $pixels = collect($store->facebook_pixels ?? [])
                ->reject(fn ($p) => ($p['id'] ?? '') === $pixelId)
                ->values()
                ->all();
            $store->update(['facebook_pixels' => $pixels]);
        } else {
            $store->update(['facebook_pixels' => []]);
        }

        $store->syncLegacyFacebookPixel();

        return response()->json([
            'success' => true,
            'message' => 'Facebook Pixel disconnected successfully!',
        ]);
    }

    public function toggleFacebookPixel(Request $request)
    {
        $request->validate([
            'facebook_pixel_id' => 'required|string|max:255',
            'enabled' => 'required|boolean',
        ]);

        $activeStoreId = session('active_store_id');
        
        if (!$activeStoreId) {
            return response()->json(['error' => 'No active store selected'], 400);
        }

        $store = Store::findOrFail($activeStoreId);
        $pixels = collect($store->facebook_pixels ?? [])
            ->map(function ($p) use ($request) {
                if (($p['id'] ?? '') === $request->facebook_pixel_id) {
                    $p['enabled'] = $request->boolean('enabled');
                }
                return $p;
            })
            ->values()
            ->all();

        $store->update(['facebook_pixels' => $pixels]);
        $store->syncLegacyFacebookPixel();

        return response()->json([
            'success' => true,
            'message' => 'Pixel updated successfully!',
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
