<?php

namespace App\Services;

use App\Models\AiApiSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiLandingPageService
{
    protected $user;
    protected $aiSetting;

    public function __construct($user)
    {
        $this->user = $user;
        $this->aiSetting = AiApiSetting::where('user_id', $user->id)->first();
    }

    public function generateLandingPage(Product $product, array $languages = ['fr', 'en', 'ar'])
    {
        if (!$this->aiSetting) {
            throw new \Exception('AI API settings not configured. Please configure your AI settings first.');
        }

        $categoryName = $product->category ? $product->category->name : 'General';

        $results = [];
        foreach ($languages as $language) {
            $prompt = $this->buildPrompt($product, $categoryName, $language);

            if (!empty($this->aiSetting->openai_api_key_encrypted)) {
                $results[$language] = $this->generateWithOpenAI($prompt);
            } elseif (!empty($this->aiSetting->anthropic_api_key_encrypted)) {
                $results[$language] = $this->generateWithAnthropic($prompt);
            } else {
                throw new \Exception('No AI API key configured. Please add an OpenAI or Anthropic API key.');
            }
        }

        return $results;
    }

    protected function buildPrompt(Product $product, string $categoryName, string $language = 'fr'): string
    {
        $languageInstructions = [
            'fr' => 'Generate all content in French (Français)',
            'en' => 'Generate all content in English',
            'ar' => 'Generate all content in Arabic (العربية)'
        ];

        $instruction = $languageInstructions[$language] ?? $languageInstructions['fr'];

        return "You are a professional marketing copywriter and landing page designer. {$instruction}.

Create compelling landing page content for the following product:

Product Name: {$product->name}
Category: {$categoryName}
Price: {$product->price} MAD
" . ($product->compare_at_price ? "Original Price: {$product->compare_at_price} MAD\n" : "") . "
Description: {$product->description}

Generate a professional, conversion-optimized landing page with the following structure in JSON format:

{
    \"hero_title\": \"An attention-grabbing headline (max 60 characters)\",
    \"hero_description\": \"A compelling 2-3 sentence description that highlights the main benefit\",
    \"features\": [
        {\"title\": \"Feature 1 Title\", \"description\": \"Brief feature description\", \"icon\": \"✓\"},
        {\"title\": \"Feature 2 Title\", \"description\": \"Brief feature description\", \"icon\": \"⚡\"},
        {\"title\": \"Feature 3 Title\", \"description\": \"Brief feature description\", \"icon\": \"🎯\"},
        {\"title\": \"Feature 4 Title\", \"description\": \"Brief feature description\", \"icon\": \"💎\"}
    ],
    \"steps\": [
        {\"number\": \"1\", \"title\": \"Step 1 Title\", \"description\": \"What customer does in step 1\"},
        {\"number\": \"2\", \"title\": \"Step 2 Title\", \"description\": \"What customer does in step 2\"},
        {\"number\": \"3\", \"title\": \"Step 3 Title\", \"description\": \"What customer does in step 3\"}
    ],
    \"steps_title\": \"Title for steps section (e.g., 'How It Works', '3 Easy Steps')\",
    \"testimonials\": [
        {\"name\": \"Customer Name\", \"text\": \"Positive testimonial quote\", \"rating\": 5},
        {\"name\": \"Customer Name\", \"text\": \"Positive testimonial quote\", \"rating\": 5},
        {\"name\": \"Customer Name\", \"text\": \"Positive testimonial quote\", \"rating\": 5}
    ],
    \"testimonials_title\": \"Title for testimonials section\",
    \"faqs\": [
        {\"question\": \"Common question 1?\", \"answer\": \"Detailed answer to question 1\"},
        {\"question\": \"Common question 2?\", \"answer\": \"Detailed answer to question 2\"},
        {\"question\": \"Common question 3?\", \"answer\": \"Detailed answer to question 3\"},
        {\"question\": \"Common question 4?\", \"answer\": \"Detailed answer to question 4\"}
    ],
    \"faqs_title\": \"Title for FAQ section\",
    \"cta\": \"Call-to-action text (e.g., 'Get Yours Today', 'Order Now')\",
    \"full_description\": \"A detailed 3-4 paragraph description of the product, its benefits, and why customers should buy it. Make it persuasive and SEO-friendly.\",
    \"form_title\": \"Contact form title\",
    \"form_subtitle\": \"Contact form subtitle/description\",
    \"form_name_placeholder\": \"Name field placeholder\",
    \"form_phone_placeholder\": \"Phone field placeholder\",
    \"form_note_placeholder\": \"Note field placeholder\",
    \"form_submit_button\": \"Submit button text\"
}

Important:
- Make it specific to the {$categoryName} category
- Focus on benefits, not just features
- Use persuasive, action-oriented language
- Keep it professional but engaging
- Create realistic testimonials with Moroccan names
- FAQs should address common concerns about buying/ordering
- Return ONLY valid JSON, no additional text or markdown code blocks
- All content must be in {$instruction}";
    }

    protected function generateWithOpenAI(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->openai_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt OpenAI API key.');
        }

        $model = $this->aiSetting->openai_model ?: 'gpt-4o-mini';

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional marketing copywriter. Always respond with valid JSON only, no markdown or additional text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('OpenAI API request failed: ' . $error);
        }

        $content = $response->json('choices.0.message.content');
        
        return $this->parseAiResponse($content);
    }

    protected function generateWithAnthropic(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->anthropic_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt Anthropic API key.');
        }

        $model = $this->aiSetting->anthropic_model ?: 'claude-3-5-sonnet-20241022';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 2000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('Anthropic API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('Anthropic API request failed: ' . $error);
        }

        $content = $response->json('content.0.text');
        
        return $this->parseAiResponse($content);
    }

    protected function parseAiResponse(string $content): array
    {
        $content = trim($content);
        
        $content = preg_replace('/^```json\s*/s', '', $content);
        $content = preg_replace('/\s*```$/s', '', $content);
        $content = trim($content);

        try {
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
            }

            if (!isset($data['hero_title']) || !isset($data['features']) || !isset($data['full_description'])) {
                throw new \Exception('AI response missing required fields.');
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('Failed to parse AI response', ['content' => $content, 'error' => $e->getMessage()]);
            throw new \Exception('Failed to parse AI response: ' . $e->getMessage());
        }
    }

    public function saveLandingPageToProduct(Product $product, array $landingPageData): void
    {
        $updateData = [];

        // Save multi-language data
        if (isset($landingPageData['fr'])) {
            $updateData['landing_page_fr'] = $landingPageData['fr'];
        }
        if (isset($landingPageData['en'])) {
            $updateData['landing_page_en'] = $landingPageData['en'];
        }
        if (isset($landingPageData['ar'])) {
            $updateData['landing_page_ar'] = $landingPageData['ar'];
        }

        // Keep backward compatibility - save French as default
        if (isset($landingPageData['fr'])) {
            $updateData['landing_page_hero_title'] = $landingPageData['fr']['hero_title'] ?? null;
            $updateData['landing_page_hero_description'] = $landingPageData['fr']['hero_description'] ?? null;
            $updateData['landing_page_features'] = $landingPageData['fr']['features'] ?? [];
            $updateData['landing_page_cta'] = $landingPageData['fr']['cta'] ?? null;
            $updateData['landing_page_content'] = $landingPageData['fr']['full_description'] ?? null;
        }

        $product->update($updateData);
    }
}
