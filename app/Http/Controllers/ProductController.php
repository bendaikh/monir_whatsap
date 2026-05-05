<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index($subdomain, Request $request)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        
        if (!$settings) {
            $settings = \App\Models\WebsiteSettings::getSettings($store->user_id, $store->id);
        }
        
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('store_id', $store->id);

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)
            ->where('store_id', $store->id)
            ->orderBy('order')
            ->get();
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('store_id', $store->id)
            ->limit(8)
            ->get();

        return view('welcome', compact('products', 'categories', 'featuredProducts', 'settings', 'store'));
    }

    public function show($subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();

        $product = Product::with(['activeVariations', 'activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->limit(4)
            ->get();

        // Get the WhatsApp profile for the store
        $whatsappProfile = \App\Models\WhatsappProfile::where('store_id', $store->id)
            ->where('is_active', true)
            ->first();
        
        // All products now use Theme 2 (Theme 1 has been removed)
        // If the product has a theme or landing page content, use Theme 2 landing page
        $hasLandingPageContent = $product->theme === 'theme2' 
            || !empty($product->landing_page_translations) 
            || $product->landing_page_fr 
            || $product->landing_page_en 
            || $product->landing_page_ar;
        
        if ($hasLandingPageContent) {
            return view('product-landing-theme2', compact('product', 'relatedProducts', 'store', 'whatsappProfile'));
        }

        return view('product-detail', compact('product', 'relatedProducts', 'store'));
    }

    public function submitLead(Request $request, $subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $product = Product::with(['activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|string|max:10',
            'selected_promotion_form' => 'nullable|exists:product_promotions,id',
        ]);

        // Get selected promotion if any
        $selectedPromotionId = $request->input('selected_promotion_form');
        $selectedPromotion = null;
        if ($selectedPromotionId) {
            $selectedPromotion = \App\Models\ProductPromotion::find($selectedPromotionId);
        }

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'selected_promotion_id' => $selectedPromotionId,
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        // Redirect to thank you page
        return redirect()->route('store.product.thank-you', [
            'subdomain' => $subdomain,
            'slug' => $slug,
            'lead' => $lead->id
        ]);
    }

    public function thankYou($subdomain, $slug, $leadId)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $product = Product::with(['activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $lead = \App\Models\ProductLead::where('id', $leadId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        // Get the selected promotion if any
        $selectedPromotion = null;
        if ($lead->selected_promotion_id) {
            $selectedPromotion = \App\Models\ProductPromotion::find($lead->selected_promotion_id);
        }

        return view('product-thank-you-theme2', compact('product', 'store', 'lead', 'selectedPromotion'));
    }
}
