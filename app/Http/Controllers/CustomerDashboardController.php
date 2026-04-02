<?php

namespace App\Http\Controllers;

use App\Models\AiApiSetting;
use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiLandingPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class CustomerDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'conversations' => Conversation::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'messages' => Message::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'orders' => 0, // Placeholder
            'active_profiles' => WhatsappProfile::where('user_id', $user->id)
                ->where('status', 'connected')
                ->count(),
            'ai_tokens' => 0, // Placeholder
            'sales_percentage' => 0, // Placeholder
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
        $products = \App\Models\Product::where('user_id', $user->id)->latest()->paginate(10);
        
        return view('customer.products', compact('products'));
    }
    
    public function productsCreate()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        
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
        ]);
        
        $validated['user_id'] = auth()->id();
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
        
        $product = \App\Models\Product::create($validated);
        
        if ($request->boolean('generate_landing_page')) {
            try {
                $aiService = new AiLandingPageService(auth()->user());
                $landingPageData = $aiService->generateLandingPage($product);
                $aiService->saveLandingPageToProduct($product, $landingPageData);
                
                return redirect()
                    ->route('app.products')
                    ->with('success', 'Product created successfully with AI-generated landing page!');
            } catch (\Exception $e) {
                return redirect()
                    ->route('app.products')
                    ->with('warning', 'Product created, but AI landing page generation failed: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('app.products')->with('success', 'Product created successfully!');
    }
    
    public function generateLandingPage($productId)
    {
        $product = \App\Models\Product::where('user_id', auth()->id())->findOrFail($productId);
        
        try {
            $aiService = new AiLandingPageService(auth()->user());
            $landingPageData = $aiService->generateLandingPage($product);
            $aiService->saveLandingPageToProduct($product, $landingPageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Landing page generated successfully!',
                'data' => $landingPageData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
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
        $categories = \App\Models\Category::orderBy('order')->orderBy('name')->get();
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
}
