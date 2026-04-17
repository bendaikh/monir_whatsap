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
        if (!$this->apiSetting) {
            Log::warning('No AI API settings found for user', ['user_id' => $this->user->id]);
            return null;
        }

        Log::info('AiChatService: Generating response', [
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
            return $this->generateOpenAiResponse($systemPrompt, $messageContent, $conversationHistory);
        } elseif ($this->apiSetting->anthropic_api_key) {
            Log::info('Using Anthropic for response');
            return $this->generateAnthropicResponse($systemPrompt, $messageContent, $conversationHistory);
        }

        Log::warning('No API key available for AI response');
        return null;
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
        
        $products = $query->with('category')->get(['id', 'name', 'description', 'price', 'category_id', 'stock', 'has_variations']);

        if ($products->isEmpty()) {
            return "No products available at the moment.";
        }

        $context = "Available Products:\n\n";
        foreach ($products as $product) {
            $context .= "- **{$product->name}**\n";
            
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

{$productsContext}

Remember: You're chatting on WhatsApp, so keep it casual, friendly, and helpful!
PROMPT;
    }

    /**
     * Generate response using OpenAI
     */
    protected function generateOpenAiResponse(string $systemPrompt, string $userMessage, array $history): ?string
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
                return $response->json()['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error generating OpenAI response', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate response using Anthropic Claude
     */
    protected function generateAnthropicResponse(string $systemPrompt, string $userMessage, array $history): ?string
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
                return $response->json()['content'][0]['text'] ?? null;
            }

            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error generating Anthropic response', [
                'error' => $e->getMessage()
            ]);
            return null;
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
}
