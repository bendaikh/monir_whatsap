<?php

namespace App\Http\Controllers;

use App\Models\AiApiSetting;
use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiLandingPageService;
use App\Jobs\GenerateProductLandingPageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class CustomerDashboardController extends Controller
{
    protected function getActiveStoreId()
    {
        return session('active_store_id');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $stats = [
            'conversations' => Conversation::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'messages' => Message::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'orders' => \App\Models\ProductLead::where('user_id', $user->id)->count(),
            'active_profiles' => WhatsappProfile::where('user_id', $user->id)
                ->where('status', 'connected')
                ->count(),
            'ai_tokens' => 0,
            'sales_percentage' => 0,
        ];
        
        $recent_conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->take(10)
        ->get();
        
        $whatsapp_profiles = WhatsappProfile::where('user_id', $user->id)->get();
        
        return view('customer.dashboard', compact('stats', 'recent_conversations', 'whatsapp_profiles'));
    }
    
    public function whatsapp()
    {
        $user = auth()->user();
        $profiles = WhatsappProfile::where('user_id', $user->id)->get();
        
        return view('customer.whatsapp', compact('profiles'));
    }
    
    public function conversations()
    {
        $user = auth()->user();
        
        $conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->paginate(20);
        
        return view('customer.conversations', compact('conversations'));
    }
    
    public function conversationDetail($id)
    {
        $user = auth()->user();
        
        $conversation = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['messages', 'whatsappProfile'])
        ->findOrFail($id);
        
        return view('customer.conversation-detail', compact('conversation'));
    }
    
    public function aiSettings()
    {
        $user = auth()->user();
        $profiles = WhatsappProfile::where('user_id', $user->id)->get();
        $aiSetting = AiApiSetting::where('user_id', $user->id)->first();

        return view('customer.ai-settings', compact('profiles', 'aiSetting'));
    }

    public function saveOpenAiSettings(Request $request)
    {
        $validated = $request->validate([
            'openai_api_key' => 'nullable|string|max:2048',
            'openai_model' => 'required|string|max:128',
            'clear_openai_key' => 'nullable|boolean',
            'auto_reply_enabled' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $setting = AiApiSetting::firstOrNew(['user_id' => $user->id]);
        $setting->openai_model = $validated['openai_model'];
        $setting->auto_reply_enabled = $request->boolean('auto_reply_enabled');

        if ($request->boolean('clear_openai_key')) {
            $setting->openai_api_key_encrypted = null;
        } elseif (! empty($validated['openai_api_key'])) {
            $setting->openai_api_key_encrypted = Crypt::encryptString(trim($validated['openai_api_key']));
        }

        $setting->save();

        return redirect()
            ->route('app.ai-settings')
            ->withFragment('openai-connect')
            ->with('success', 'Paramètres OpenAI enregistrés.');
    }

    public function testOpenAiConnection(Request $request)
    {
        $setting = AiApiSetting::where('user_id', $request->user()->id)->first();

        if (! $setting || empty($setting->openai_api_key_encrypted)) {
            return redirect()
                ->route('app.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', 'Enregistrez d\'abord une clé API OpenAI.');
        }

        try {
            $apiKey = Crypt::decryptString($setting->openai_api_key_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', 'Impossible de lire la clé enregistrée. Saisissez-la à nouveau et enregistrez.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(20)
            ->acceptJson()
            ->get('https://api.openai.com/v1/models');

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('app.ai-settings')
                ->withFragment('openai-connect')
                ->with('openai_error', is_string($message) ? $message : 'La requête vers OpenAI a échoué.');
        }

        return redirect()
            ->route('app.ai-settings')
            ->withFragment('openai-connect')
            ->with('openai_success', 'Connexion OpenAI réussie.');
    }

    public function saveAnthropicSettings(Request $request)
    {
        $validated = $request->validate([
            'anthropic_api_key' => 'nullable|string|max:2048',
            'anthropic_model' => 'required|string|max:128',
            'clear_anthropic_key' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $setting = AiApiSetting::firstOrNew(['user_id' => $user->id]);
        $setting->anthropic_model = $validated['anthropic_model'];

        if ($request->boolean('clear_anthropic_key')) {
            $setting->anthropic_api_key_encrypted = null;
        } elseif (! empty($validated['anthropic_api_key'])) {
            $setting->anthropic_api_key_encrypted = Crypt::encryptString(trim($validated['anthropic_api_key']));
        }

        $setting->save();

        return redirect()
            ->route('app.ai-settings')
            ->withFragment('anthropic-connect')
            ->with('success', 'Paramètres Anthropic enregistrés.');
    }

    public function testAnthropicConnection(Request $request)
    {
        $setting = AiApiSetting::where('user_id', $request->user()->id)->first();

        if (! $setting || empty($setting->anthropic_api_key_encrypted)) {
            return redirect()
                ->route('app.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', 'Enregistrez d\'abord une clé API Anthropic.');
        }

        try {
            $apiKey = Crypt::decryptString($setting->anthropic_api_key_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', 'Impossible de lire la clé enregistrée. Saisissez-la à nouveau et enregistrez.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(20)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $setting->anthropic_model ?: 'claude-3-5-sonnet-20241022',
                'max_tokens' => 10,
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello']
                ]
            ]);

        if (! $response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('app.ai-settings')
                ->withFragment('anthropic-connect')
                ->with('anthropic_error', is_string($message) ? $message : 'La requête vers Anthropic a échoué.');
        }

        return redirect()
            ->route('app.ai-settings')
            ->withFragment('anthropic-connect')
            ->with('anthropic_success', 'Connexion Anthropic réussie.');
    }
    
    public function orders()
    {
        return view('customer.orders');
    }
    
    public function products()
    {
        $user = auth()->user();
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Product::where('user_id', $user->id);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $products = $query->latest()->paginate(10);
        
        return view('customer.products', compact('products'));
    }
    
    public function productsCreate()
    {
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Category::where('is_active', true);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $categories = $query->orderBy('order')->orderBy('name')->get();
        
        return view('customer.products-create', compact('categories'));
    }
    
    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'landing_sections' => 'nullable|array',
            'landing_sections.*.image' => 'nullable|image|max:2048',
            'landing_sections.*.title_fr' => 'nullable|string|max:255',
            'landing_sections.*.description_fr' => 'nullable|string',
            'landing_sections.*.title_en' => 'nullable|string|max:255',
            'landing_sections.*.description_en' => 'nullable|string',
            'landing_sections.*.title_ar' => 'nullable|string|max:255',
            'landing_sections.*.description_ar' => 'nullable|string',
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['store_id'] = $this->getActiveStoreId();
        $validated['slug'] = \Str::slug($validated['name']) . '-' . time();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['stock'] = $validated['stock'] ?? 0;
        
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }
        
        if ($request->has('landing_sections')) {
            $landingSections = [];
            foreach ($request->input('landing_sections', []) as $index => $section) {
                // Check if this is an auto-generated section (no descriptions yet, AI will fill them)
                if (!empty($section['auto_generated'])) {
                    $imageIndex = $section['image_index'] ?? 0;
                    // Store placeholder - AI will fill this when landing page is generated
                    $sectionData = [
                        'image_index' => $imageIndex,
                        'pending_ai' => true, // Mark for AI generation
                    ];
                    $landingSections[] = $sectionData;
                } else {
                    // Manual section with custom data
                    $sectionData = [
                        'title_fr' => $section['title_fr'] ?? '',
                        'description_fr' => $section['description_fr'] ?? '',
                        'title_en' => $section['title_en'] ?? '',
                        'description_en' => $section['description_en'] ?? '',
                        'title_ar' => $section['title_ar'] ?? '',
                        'description_ar' => $section['description_ar'] ?? '',
                    ];
                    
                    if ($request->hasFile("landing_sections.{$index}.image")) {
                        $sectionData['image'] = $request->file("landing_sections.{$index}.image")->store('products/landing-sections', 'public');
                    }
                    
                    if (!empty($sectionData['title_fr']) || !empty($sectionData['description_fr'])) {
                        $landingSections[] = $sectionData;
                    }
                }
            }
            
            if (!empty($landingSections)) {
                $validated['landing_page_sections'] = $landingSections;
            }
        }
        
        if ($request->boolean('generate_landing_page')) {
            $validated['landing_page_status'] = 'pending';
        } else {
            $validated['landing_page_status'] = 'none';
        }

        if ($request->boolean('generate_product_images')) {
            $validated['ai_images_status'] = 'pending';
        } else {
            $validated['ai_images_status'] = 'none';
        }
        
        $product = \App\Models\Product::create($validated);
        
        $jobsDispatched = [];
        
        if ($request->boolean('generate_landing_page')) {
            GenerateProductLandingPageJob::dispatch($product, auth()->id());
            $jobsDispatched[] = 'AI landing page';
        }

        if ($request->boolean('generate_product_images')) {
            \App\Jobs\GenerateProductImagesJob::dispatch($product, auth()->id(), 5);
            $jobsDispatched[] = 'AI product images';
        }
        
        if (!empty($jobsDispatched)) {
            $message = 'Product created successfully! ' . implode(' and ', $jobsDispatched) . ' generation started in the background.';
            return redirect()->route('app.products')->with('success', $message);
        }
        
        return redirect()->route('app.products')->with('success', 'Product created successfully!');
    }
    
    public function productsEdit($id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        
        return view('customer.products-edit', compact('product', 'categories'));
    }
    
    public function productsUpdate(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'landing_sections' => 'nullable|array',
            'landing_sections.*.image' => 'nullable|image|max:2048',
            'landing_sections.*.existing_image' => 'nullable|string',
            'landing_sections.*.title_fr' => 'nullable|string|max:255',
            'landing_sections.*.description_fr' => 'nullable|string',
            'landing_sections.*.title_en' => 'nullable|string|max:255',
            'landing_sections.*.description_en' => 'nullable|string',
            'landing_sections.*.title_ar' => 'nullable|string|max:255',
            'landing_sections.*.description_ar' => 'nullable|string',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['stock'] = $validated['stock'] ?? 0;
        
        if ($request->hasFile('images')) {
            $imagePaths = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }
        
        if ($request->has('landing_sections')) {
            $landingSections = [];
            foreach ($request->input('landing_sections', []) as $index => $section) {
                $sectionData = [
                    'title_fr' => $section['title_fr'] ?? '',
                    'description_fr' => $section['description_fr'] ?? '',
                    'title_en' => $section['title_en'] ?? '',
                    'description_en' => $section['description_en'] ?? '',
                    'title_ar' => $section['title_ar'] ?? '',
                    'description_ar' => $section['description_ar'] ?? '',
                ];
                
                if ($request->hasFile("landing_sections.{$index}.image")) {
                    $sectionData['image'] = $request->file("landing_sections.{$index}.image")->store('products/landing-sections', 'public');
                } elseif (!empty($section['existing_image'])) {
                    $sectionData['image'] = $section['existing_image'];
                }
                
                if (!empty($sectionData['title_fr']) || !empty($sectionData['description_fr'])) {
                    $landingSections[] = $sectionData;
                }
            }
            
            $validated['landing_page_sections'] = $landingSections;
        }
        
        // Handle image deletion
        if ($request->has('delete_images')) {
            $currentImages = $product->images ?? [];
            $deleteImages = $request->input('delete_images', []);
            $validated['images'] = array_values(array_diff($currentImages, $deleteImages));
            
            // Delete files from storage
            foreach ($deleteImages as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        $product->update($validated);
        
        return redirect()->route('app.products')->with('success', 'Product updated successfully!');
    }
    
    public function productsDestroy($id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        // Delete product images from storage
        if ($product->images) {
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }
        
        // Delete AI generated images
        if ($product->ai_generated_images) {
            foreach ($product->ai_generated_images as $image) {
                $path = str_replace('/storage/', '', $image);
                \Storage::disk('public')->delete($path);
            }
        }
        
        $product->delete();
        
        return redirect()->route('app.products')->with('success', 'Product deleted successfully!');
    }

    public function generateLandingPage($productId)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($productId);
        
        $product->update(['landing_page_status' => 'pending']);
        
        GenerateProductLandingPageJob::dispatch($product, auth()->id());
        
        return response()->json([
            'success' => true,
            'message' => 'Landing page generation started! It will be ready in a few moments.'
        ]);
    }

    public function generateProductImages($productId)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($productId);
        
        $product->update([
            'ai_images_status' => 'pending',
            'ai_images_progress' => 0,
            'ai_images_generated' => 0,
        ]);
        
        \App\Jobs\GenerateProductImagesJob::dispatch($product, auth()->id(), 5);
        
        return response()->json([
            'success' => true,
            'message' => 'AI image generation started! This may take a few minutes.'
        ]);
    }

    public function checkImageGenerationProgress($productId)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($productId);
        
        return response()->json([
            'status' => $product->ai_images_status,
            'progress' => $product->ai_images_progress,
            'generated' => $product->ai_images_generated,
            'total' => $product->ai_images_total,
            'images' => $product->ai_generated_images ?? [],
        ]);
    }

    public function landingPageBuilder($id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        return view('customer.products-landing-builder', compact('product'));
    }

    public function saveLandingPageBuilder(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'sections' => 'nullable|array',
            'sections.*.title_fr' => 'nullable|string|max:255',
            'sections.*.description_fr' => 'nullable|string',
            'sections.*.title_en' => 'nullable|string|max:255',
            'sections.*.description_en' => 'nullable|string',
            'sections.*.title_ar' => 'nullable|string|max:255',
            'sections.*.description_ar' => 'nullable|string',
            'sections.*.image' => 'nullable|string',
            'page_data' => 'nullable|array',
            'page_data.fr' => 'nullable|array',
            'page_data.en' => 'nullable|array',
            'page_data.ar' => 'nullable|array',
        ]);
        
        $product->update([
            'landing_page_sections' => $validated['sections'] ?? [],
            'landing_page_fr' => $validated['page_data']['fr'] ?? [],
            'landing_page_en' => $validated['page_data']['en'] ?? [],
            'landing_page_ar' => $validated['page_data']['ar'] ?? [],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Landing page updated successfully!'
        ]);
    }

    public function uploadProductImage(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);
        
        $path = $request->file('image')->store('products/landing-sections', 'public');
        $url = \Storage::url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }

    public function setMainImage(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'image_path' => 'required|string'
        ]);
        
        $images = $product->images ?? [];
        $imagePath = $validated['image_path'];
        
        // Remove from current position and add to front
        $images = array_values(array_diff($images, [$imagePath]));
        array_unshift($images, $imagePath);
        
        $product->update(['images' => $images]);
        
        return response()->json([
            'success' => true,
            'message' => 'Main image updated successfully!'
        ]);
    }

    public function updateImageDescription(Request $request, $id)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'image_path' => 'required|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);
        
        $imageDescriptions = $product->image_descriptions ?? [];
        $imageDescriptions[$validated['image_path']] = [
            'fr' => $validated['description_fr'] ?? '',
            'en' => $validated['description_en'] ?? '',
            'ar' => $validated['description_ar'] ?? '',
        ];
        
        $product->update(['image_descriptions' => $imageDescriptions]);
        
        return response()->json([
            'success' => true,
            'message' => 'Image description updated successfully!'
        ]);
    }
    
    public function campaigns()
    {
        return view('customer.campaigns');
    }

    public function leads()
    {
        $user = auth()->user();
        $leads = \App\Models\ProductLead::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        
        return view('customer.leads', compact('leads'));
    }
    
    public function categories()
    {
        $storeId = $this->getActiveStoreId();
        
        $query = \App\Models\Category::orderBy('order')->orderBy('name');
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $categories = $query->get();
        return view('customer.categories', compact('categories'));
    }
    
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['store_id'] = $this->getActiveStoreId();
        
        \App\Models\Category::create($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category created successfully!');
    }
    
    public function categoriesUpdate(Request $request, $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $category->update($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category updated successfully!');
    }
    
    public function categoriesDestroy($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('app.categories')->with('error', 'Cannot delete category with associated products.');
        }
        
        $category->delete();
        
        return redirect()->route('app.categories')->with('success', 'Category deleted successfully!');
    }

    public function externalApiSettings()
    {
        $user = auth()->user();
        return view('customer.external-api-settings', compact('user'));
    }

    public function saveExternalApiSettings(Request $request)
    {
        $validated = $request->validate([
            'external_api_url' => 'nullable|url|max:500',
            'external_api_key' => 'nullable|string|max:2048',
            'external_api_enabled' => 'nullable|boolean',
            'clear_external_api_key' => 'nullable|boolean',
        ]);

        $user = $request->user();
        
        $user->external_api_url = $validated['external_api_url'] ?? null;
        $user->external_api_enabled = $request->boolean('external_api_enabled');

        if ($request->boolean('clear_external_api_key')) {
            $user->external_api_key_encrypted = null;
        } elseif (!empty($validated['external_api_key'])) {
            $user->external_api_key_encrypted = \Crypt::encryptString(trim($validated['external_api_key']));
        }

        $user->save();

        return redirect()
            ->route('app.external-api-settings')
            ->with('success', 'External API settings saved successfully!');
    }

    public function testExternalApiConnection(Request $request)
    {
        $user = $request->user();
        
        if (!$user->external_api_enabled || !$user->external_api_url || !$user->external_api_key_encrypted) {
            return redirect()
                ->route('app.external-api-settings')
                ->with('error', 'Please configure all API settings before testing.');
        }

        $apiService = new \App\Services\ExternalApiService($user);
        $result = $apiService->testConnection();

        if ($result['success']) {
            return redirect()
                ->route('app.external-api-settings')
                ->with('success', 'Connection successful! Your API is working correctly.');
        }

        return redirect()
            ->route('app.external-api-settings')
            ->with('error', 'Connection failed: ' . $result['message']);
    }
}
