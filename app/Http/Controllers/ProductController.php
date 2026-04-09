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
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->limit(4)
            ->get();

        if ($product->landing_page_fr || $product->landing_page_en || $product->landing_page_ar) {
            return view('product-landing', compact('product', 'relatedProducts', 'store'));
        }

        return view('product-detail', compact('product', 'relatedProducts', 'store'));
    }

    public function submitLead(Request $request, $subdomain, $slug)
    {
        $store = Store::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->firstOrFail();
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|in:fr,en,ar',
        ]);

        $lead = \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        \App\Jobs\PushOrderToExternalApi::dispatch($lead);

        $successMessages = [
            'fr' => 'Merci ! Nous vous contactons bientôt.',
            'en' => 'Thank you! We will contact you soon.',
            'ar' => 'شكرا لك! سنتصل بك قريبا.',
        ];

        return back()->with('success', $successMessages[$validated['language']] ?? $successMessages['fr']);
    }
}
