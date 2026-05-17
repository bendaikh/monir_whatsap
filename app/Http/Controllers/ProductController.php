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
        // Check if accessing via custom domain
        $store = $request->attributes->get('custom_domain_store');
        
        // If not custom domain, use subdomain
        if (!$store) {
            $store = Store::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->firstOrFail();
        }
        
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

    public function show($subdomain, $slug, Request $request)
    {
        // Check if accessing via custom domain
        $store = $request->attributes->get('custom_domain_store');
        
        // If not custom domain, use subdomain
        if (!$store) {
            $store = Store::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->firstOrFail();
        }

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
        // Check if accessing via custom domain
        $store = $request->attributes->get('custom_domain_store');
        
        // If not custom domain, use subdomain
        if (!$store) {
            $store = Store::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->firstOrFail();
        }
        
        $product = Product::with(['activePromotions'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|string|max:10',
            'selected_promotion_id' => 'nullable|exists:product_promotions,id',
        ]);

        // Get selected promotion if any
        $selectedPromotionId = $request->input('selected_promotion_id');
        $selectedPromotion = null;
        if ($selectedPromotionId) {
            $selectedPromotion = \App\Models\ProductPromotion::find($selectedPromotionId);
        }

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'note' => $validated['note'] ?? null,
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'selected_promotion_id' => $selectedPromotionId,
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        $request->session()->put('thank_you_lead_id', $lead->id);

        return redirect(thank_you_url());
    }

    public function thankYouLegacy($subdomain, $slug, $leadId, Request $request)
    {
        $request->session()->put('thank_you_lead_id', (int) $leadId);

        return redirect(thank_you_url());
    }

    public function thankYouPage(Request $request)
    {
        $leadId = $request->session()->get('thank_you_lead_id');

        if (!$leadId) {
            $customStore = $request->attributes->get('custom_domain_store');
            if ($customStore) {
                return redirect(url('/'));
            }

            abort(404);
        }

        $lead = \App\Models\ProductLead::with(['product.store'])
            ->findOrFail($leadId);

        $store = $lead->product->store;

        if (!$store || !$store->is_active) {
            abort(404);
        }

        $customStore = $request->attributes->get('custom_domain_store');
        if ($customStore && $customStore->id !== $store->id) {
            abort(403);
        }

        $product = Product::with(['activePromotions', 'upsellProducts'])
            ->where('id', $lead->product_id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $selectedPromotion = null;
        if ($lead->selected_promotion_id) {
            $selectedPromotion = \App\Models\ProductPromotion::find($lead->selected_promotion_id);
        }

        return view('product-thank-you-theme2', compact('product', 'store', 'lead', 'selectedPromotion'));
    }
    
    public function buyUpsell($subdomain, $slug, Request $request)
    {
        $request->validate([
            'upsell_product_id' => 'required|exists:upsell_products,id',
            'lead_id' => 'required|exists:product_leads,id',
        ]);

        // Check if accessing via custom domain
        $store = $request->attributes->get('custom_domain_store');
        
        // If not custom domain, use subdomain
        if (!$store) {
            $store = Store::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->firstOrFail();
        }

        // Verify the lead belongs to a product in this store
        $lead = \App\Models\ProductLead::with('product')
            ->where('id', $request->lead_id)
            ->firstOrFail();

        if ($lead->product->store_id !== $store->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request'
            ], 403);
        }

        // Verify the upsell product belongs to this store
        $upsellProduct = \App\Models\UpsellProduct::where('id', $request->upsell_product_id)
            ->where('store_id', $store->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if this upsell order already exists
        $existingOrder = \App\Models\UpsellOrder::where('lead_id', $request->lead_id)
            ->where('upsell_product_id', $request->upsell_product_id)
            ->first();

        if ($existingOrder) {
            return response()->json([
                'success' => true,
                'message' => 'Order already placed'
            ]);
        }

        // Create the upsell order
        \App\Models\UpsellOrder::create([
            'lead_id' => $request->lead_id,
            'upsell_product_id' => $request->upsell_product_id,
            'store_id' => $store->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully'
        ]);
    }
}
