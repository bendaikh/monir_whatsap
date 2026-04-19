<?php

namespace App\Services;

use App\Models\AiApiSetting;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected $apiSetting;
    protected $user;
    protected $storeId;

    public function __construct(User $user, ?int $storeId = null)
    {
        $this->user = $user;
        $this->storeId = $storeId;
        $this->apiSetting = $user->aiApiSetting;
    }

    /**
     * Generate AI response for WhatsApp message
     */
    public function generateResponse(string $messageContent, array $conversationHistory = []): ?string
    {
        $responseData = $this->generateResponseWithMedia($messageContent, $conversationHistory);
        return $responseData['text'] ?? null;
    }
    
    /**
     * Generate AI response with potential media (images) for WhatsApp message
     */
    public function generateResponseWithMedia(string $messageContent, array $conversationHistory = []): array
    {
        if (!$this->apiSetting) {
            Log::warning('No AI API settings found for user', ['user_id' => $this->user->id]);
            return ['text' => null, 'media_url' => null];
        }

        Log::info('AiChatService: Generating response with media', [
            'user_id' => $this->user->id,
            'has_openai_key' => !empty($this->apiSetting->openai_api_key),
            'has_anthropic_key' => !empty($this->apiSetting->anthropic_api_key),
            'message_length' => strlen($messageContent)
        ]);

        // Get product information for context
        $productsContext = $this->getProductsContext();

        // Build the system prompt
        $systemPrompt = $this->buildSystemPrompt($productsContext);

        // Generate response based on provider
        if ($this->apiSetting->openai_api_key) {
            Log::info('Using OpenAI for response');
            return $this->generateOpenAiResponseWithMedia($systemPrompt, $messageContent, $conversationHistory);
        } elseif ($this->apiSetting->anthropic_api_key) {
            Log::info('Using Anthropic for response');
            return $this->generateAnthropicResponseWithMedia($systemPrompt, $messageContent, $conversationHistory);
        }

        Log::warning('No API key available for AI response');
        return ['text' => null, 'media_url' => null];
    }

    /**
     * Get products context for AI
     */
    protected function getProductsContext(): string
    {
        $query = Product::where('user_id', $this->user->id)
            ->where('is_active', true);
        
        // Filter by store if store_id is provided
        if ($this->storeId) {
            $query->where('store_id', $this->storeId);
        }
        
        $products = $query->with('category')->get(['id', 'name', 'description', 'price', 'category_id', 'stock', 'has_variations', 'main_image', 'images', 'ai_generated_images']);

        if ($products->isEmpty()) {
            return "No products available at the moment.";
        }

        $context = "Available Products:\n\n";
        foreach ($products as $product) {
            $context .= "- **{$product->name}** (ID: {$product->id})\n";
            
            if ($product->description) {
                $context .= "  Description: {$product->description}\n";
            }
            
            if ($product->has_variations) {
                // Get variations with pricing
                $variations = $product->variations()->where('is_active', true)->get();
                if ($variations->isNotEmpty()) {
                    $context .= "  Variations:\n";
                    foreach ($variations as $variation) {
                        $attrs = collect($variation->attributes)->map(fn($v, $k) => "$k: $v")->join(', ');
                        $context .= "    - {$attrs} - Price: \${$variation->price}";
                        if ($variation->stock > 0) {
                            $context .= " (Stock: {$variation->stock})";
                        }
                        $context .= "\n";
                    }
                }
            } else {
                $context .= "  Price: \${$product->price}";
                if ($product->stock > 0) {
                    $context .= " (Stock: {$product->stock})";
                }
                $context .= "\n";
            }
            
            // Add promotions if available
            if ($product->has_promotions) {
                $promotions = $product->promotions()->where('is_active', true)->get();
                if ($promotions->isNotEmpty()) {
                    $context .= "  Promotions:\n";
                    foreach ($promotions as $promo) {
                        $qty = "Buy {$promo->min_quantity}";
                        if ($promo->max_quantity) {
                            $qty .= "-{$promo->max_quantity}";
                        } else {
                            $qty .= "+";
                        }
                        $context .= "    - {$qty} for \${$promo->price} each\n";
                    }
                }
            }
            
            if ($product->category) {
                $context .= "  Category: {$product->category->name}\n";
            }
            
            if ($this->resolveProductImagePath($product)) {
                $context .= "  Has Image: Yes\n";
            }
            
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Build system prompt for AI
     */
    protected function buildSystemPrompt(string $productsContext): string
    {
        $businessName = $this->user->name ?? 'our business';
        
        // Get store name if store_id is provided
        $storeName = '';
        if ($this->storeId) {
            $store = \App\Models\Store::find($this->storeId);
            if ($store) {
                $storeName = " - {$store->name}";
            }
        }
        
        return <<<PROMPT
You are a friendly and helpful AI assistant for {$businessName}'s{$storeName} WhatsApp customer service.

Your role:
- Answer customer questions about products and services
- Provide accurate product information including prices, stock, and variations
- Help customers with orders and inquiries
- Explain promotions and special offers clearly
- Be professional, friendly, and concise
- If you don't know something, politely say so and offer to connect them with a human

Important guidelines:
- Keep responses brief and conversational (WhatsApp style)
- Use emojis occasionally to be friendly 😊
- Always be helpful and positive
- Don't make up information about products
- When customers ask about products, provide specific details from the product list
- If a product has variations, explain the available options
- Mention stock availability when relevant
- If there are promotions, highlight them to encourage sales

LANGUAGE DETECTION & RESPONSE (CRITICAL — APPLIES TO BOTH TEXT AND VOICE MESSAGES):
- Your FIRST step before answering is to silently detect the language/dialect of the customer's message.
- ALWAYS reply in EXACTLY the same language and script the customer used. Never switch languages on your own.
- This rule applies whether the message was typed as text OR transcribed from a voice/audio message.
- Specific cases:
  * Darija (Moroccan Arabic, written in Arabic letters like "شنو كاين" or in Latin letters like "chno kayn", "labas", "bghit", "wach"): reply in Darija using the SAME script the customer used (Arabic letters → Arabic letters; Latin letters / "Arabizi" → Latin letters with the same casual style).
  * Standard Arabic (الفصحى): reply in Standard Arabic.
  * Other Arabic dialects (Egyptian, Levantine, Gulf, etc.): reply in that same dialect.
  * French: reply in French.
  * English: reply in English.
  * Spanish, Italian, German, Portuguese, Turkish, etc.: reply in that same language.
- Match the customer's tone, formality level, and dialect naturally (casual → casual, formal → formal).
- For Darija specifically, use natural Moroccan expressions ("واخا", "بصح", "صافي", "زوين", "بشحال", etc.) and avoid sounding like Standard Arabic.
- If the message is very short or ambiguous (e.g. only "ok", emojis, numbers), reply in the language used most recently in the conversation history; if none, default to the language that best matches the phone country code, otherwise English.
- NEVER mix languages in a single reply unless the customer themselves mixed them.
- NEVER apologize for or comment on the language — just respond naturally in it.

PRODUCT IMAGES — HOW TO SEND THEM (READ CAREFULLY):
- The ONLY way you can attach a real product photo is by inserting the EXACT tag [SEND_IMAGE:product_id] in your reply, where product_id is the numeric ID of a product from the list below.
- The tag will be removed from the visible message and replaced with the actual product photo automatically.
- You MUST use this tag whenever:
  * The customer asks for a photo / picture / image of a product ("send me a photo", "ana b3iti chi tswira", "envoie-moi une photo", "صورة المنتج", etc.)
  * You are recommending a specific product and a photo would help.
- NEVER paste image links, URLs, markdown images, or storage paths into your text. NEVER write "[Image]", emoji image placeholders, or fake URLs. Doing so sends a useless link preview, not the real photo.
- Only use [SEND_IMAGE:ID] for products that appear in the product list below AND are marked "Has Image: Yes". If the product has no image, tell the customer the photo is not available — do NOT invent a tag.
- Only send ONE image per reply (one [SEND_IMAGE:ID] tag max).
- Correct examples:
  * "تفضل صورة المنتج 😊 [SEND_IMAGE:12]"
  * "Hak tswira dyalo [SEND_IMAGE:7] الثمن ديالو 199 درهم"
  * "Voici la photo du produit [SEND_IMAGE:5]"
- Wrong (do NOT do this):
  * "Here is the image: http://..."   ← sends a link preview, not the photo
  * "[image of product]"               ← sends nothing
  * "[SEND_IMAGE:999]" when product 999 isn't in the list

{$productsContext}

Remember: You're chatting on WhatsApp, so keep it casual, friendly, and helpful!
PROMPT;
    }

    /**
     * Generate response using OpenAI
     */
    protected function generateOpenAiResponse(string $systemPrompt, string $userMessage, array $history): ?string
    {
        $responseData = $this->generateOpenAiResponseWithMedia($systemPrompt, $userMessage, $history);
        return $responseData['text'] ?? null;
    }
    
    /**
     * Generate response using OpenAI with media support
     */
    protected function generateOpenAiResponseWithMedia(string $systemPrompt, string $userMessage, array $history): array
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            // Add conversation history (last 5 messages for context)
            foreach (array_slice($history, -5) as $msg) {
                $messages[] = [
                    'role' => $msg['sender'] === 'incoming' ? 'user' : 'assistant',
                    'content' => $msg['content']
                ];
            }

            // Add current message
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiSetting->openai_api_key,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->apiSetting->openai_model ?? 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $aiText = $response->json()['choices'][0]['message']['content'] ?? null;
                
                if ($aiText) {
                    // Extract product image if AI suggested one
                    return $this->extractProductImage($aiText);
                }
            }

            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['text' => null, 'media_url' => null];
        } catch (\Exception $e) {
            Log::error('Error generating OpenAI response', [
                'error' => $e->getMessage()
            ]);
            return ['text' => null, 'media_url' => null];
        }
    }

    /**
     * Generate response using Anthropic Claude
     */
    protected function generateAnthropicResponse(string $systemPrompt, string $userMessage, array $history): ?string
    {
        $responseData = $this->generateAnthropicResponseWithMedia($systemPrompt, $userMessage, $history);
        return $responseData['text'] ?? null;
    }
    
    /**
     * Generate response using Anthropic Claude with media support
     */
    protected function generateAnthropicResponseWithMedia(string $systemPrompt, string $userMessage, array $history): array
    {
        try {
            $messages = [];

            // Add conversation history
            foreach (array_slice($history, -5) as $msg) {
                $messages[] = [
                    'role' => $msg['sender'] === 'incoming' ? 'user' : 'assistant',
                    'content' => $msg['content']
                ];
            }

            // Add current message
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $response = Http::withHeaders([
                'x-api-key' => $this->apiSetting->anthropic_api_key,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->apiSetting->anthropic_model ?? 'claude-3-5-sonnet-20241022',
                'system' => $systemPrompt,
                'messages' => $messages,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $aiText = $response->json()['content'][0]['text'] ?? null;
                
                if ($aiText) {
                    // Extract product image if AI suggested one
                    return $this->extractProductImage($aiText);
                }
            }

            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['text' => null, 'media_url' => null];
        } catch (\Exception $e) {
            Log::error('Error generating Anthropic response', [
                'error' => $e->getMessage()
            ]);
            return ['text' => null, 'media_url' => null];
        }
    }

    /**
     * Check if AI auto-reply is enabled
     */
    public function isAutoReplyEnabled(): bool
    {
        if (!$this->apiSetting) {
            Log::info('isAutoReplyEnabled: No API settings found');
            return false;
        }
        
        $enabled = (bool) $this->apiSetting->auto_reply_enabled;
        Log::info('isAutoReplyEnabled: ' . ($enabled ? 'true' : 'false'));
        
        return $enabled;
    }
    
    /**
     * Extract product image from AI response
     * Looks for pattern: [SEND_IMAGE:product_id]
     */
    protected function extractProductImage(string $aiText): array
    {
        // Look for [SEND_IMAGE:product_id] pattern
        if (preg_match('/\[SEND_IMAGE:(\d+)\]/', $aiText, $matches)) {
            $productId = (int) $matches[1];

            // Strip the tag (and any all-tag-only line/whitespace) from the visible text
            $cleanText = preg_replace('/\s*\[SEND_IMAGE:\d+\]\s*/', ' ', $aiText);
            $cleanText = trim(preg_replace('/[ \t]+/', ' ', $cleanText));

            $product = Product::where('id', $productId)
                ->where('user_id', $this->user->id)
                ->where('is_active', true)
                ->first(['id', 'main_image', 'images', 'ai_generated_images']);

            if ($product) {
                $imagePath = $this->resolveProductImagePath($product);

                if ($imagePath) {
                    $imageUrl = $this->buildAbsoluteImageUrl($imagePath);

                    Log::info('Product image extracted', [
                        'product_id' => $productId,
                        'image_path' => $imagePath,
                        'image_url' => $imageUrl,
                    ]);

                    return [
                        'text' => $cleanText !== '' ? $cleanText : null,
                        'media_url' => $imageUrl,
                    ];
                }

                Log::warning('Product has no usable image', ['product_id' => $productId]);
            } else {
                Log::warning('Product not found for image tag', ['product_id' => $productId]);
            }

            return [
                'text' => $cleanText,
                'media_url' => null,
            ];
        }

        return [
            'text' => $aiText,
            'media_url' => null,
        ];
    }

    /**
     * Pick the best image path stored on a product, falling back across
     * main_image -> images[0] -> ai_generated_images[0].
     */
    protected function resolveProductImagePath(Product $product): ?string
    {
        $candidates = [];

        if (!empty($product->main_image)) {
            $candidates[] = $product->main_image;
        }

        $images = $product->images;
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : null;
        }
        if (is_array($images)) {
            foreach ($images as $img) {
                if (!empty($img)) {
                    $candidates[] = $img;
                }
            }
        }

        $aiImages = $product->ai_generated_images;
        if (is_string($aiImages)) {
            $decoded = json_decode($aiImages, true);
            $aiImages = is_array($decoded) ? $decoded : null;
        }
        if (is_array($aiImages)) {
            foreach ($aiImages as $img) {
                if (!empty($img)) {
                    $candidates[] = $img;
                }
            }
        }

        foreach ($candidates as $path) {
            $path = trim((string) $path);
            if ($path !== '') {
                return $path;
            }
        }

        return null;
    }

    /**
     * Convert a stored image path (or URL) into an absolute URL the
     * WhatsApp Node.js service can fetch.
     */
    protected function buildAbsoluteImageUrl(string $path): string
    {
        $path = trim($path);

        // Already absolute?
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // Site-relative starting with /storage/...
        if (str_starts_with($path, '/storage/')) {
            return url($path);
        }

        // Already prefixed with storage/
        if (str_starts_with($path, 'storage/')) {
            return url('/' . $path);
        }

        // Site-relative starting with /
        if (str_starts_with($path, '/')) {
            return url($path);
        }

        // Bare path like "products/xxx.jpg" -> /storage/products/xxx.jpg
        return url('/storage/' . ltrim($path, '/'));
    }
}
