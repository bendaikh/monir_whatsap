<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get website settings for the first user (or current user if logged in)
        $userId = auth()->check() ? auth()->id() : \App\Models\User::first()->id;
        $settings = \App\Models\WebsiteSettings::getSettings($userId);
        
        // Filter products by user_id to show only products belonging to the website owner
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('user_id', $userId);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('user_id', $userId)
            ->limit(8)
            ->get();

        return view('welcome', compact('products', 'categories', 'featuredProducts', 'settings'));
    }

    public function show($slug)
    {
        // Get the user ID for filtering
        $userId = auth()->check() ? auth()->id() : \App\Models\User::first()->id;
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('user_id', $userId)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('user_id', $userId)
            ->limit(4)
            ->get();

        // Check if multi-language landing page exists
        if ($product->landing_page_fr || $product->landing_page_en || $product->landing_page_ar) {
            return view('product-landing', compact('product', 'relatedProducts'));
        }

        return view('product-detail', compact('product', 'relatedProducts'));
    }

    public function submitLead(Request $request, $slug)
    {
        $userId = auth()->check() ? auth()->id() : \App\Models\User::first()->id;
        
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('user_id', $userId)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
            'language' => 'required|in:fr,en,ar',
        ]);

        \App\Models\ProductLead::create([
            'product_id' => $product->id,
            'user_id' => $product->user_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'note' => $validated['note'],
            'language' => $validated['language'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $successMessages = [
            'fr' => 'Merci ! Nous vous contactons bientôt.',
            'en' => 'Thank you! We will contact you soon.',
            'ar' => 'شكرا لك! سنتصل بك قريبا.',
        ];

        return back()->with('success', $successMessages[$validated['language']] ?? $successMessages['fr']);
    }
}
