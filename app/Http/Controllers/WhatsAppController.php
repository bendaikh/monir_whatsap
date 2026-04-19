<?php

namespace App\Http\Controllers;

use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhatsAppController extends Controller
{
    public function saveConnection(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'phone' => 'required|string',
            'name' => 'required|string'
        ]);
        
        try {
            $storeId = session('active_store_id');
            
            // First, try to find by session_id or phone_number
            $profile = WhatsappProfile::where('session_id', $request->session_id)
                ->orWhere(function($query) use ($request, $storeId) {
                    $query->where('phone_number', $request->phone)
                          ->where('user_id', auth()->id())
                          ->when($storeId, function($q) use ($storeId) {
                              $q->where('store_id', $storeId);
                          });
                })
                ->where('user_id', auth()->id())
                ->when($storeId, function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->first();
            
            if ($profile) {
                // Update existing profile
                $profile->update([
                    'session_id' => $request->session_id,
                    'name' => $request->name,
                    'phone_number' => $request->phone,
                    'status' => 'connected',
                    'is_active' => true,
                    'last_connected_at' => now()
                ]);
            } else {
                // Create new profile
                $profile = WhatsappProfile::create([
                    'user_id' => auth()->id(),
                    'store_id' => $storeId,
                    'session_id' => $request->session_id,
                    'name' => $request->name,
                    'phone_number' => $request->phone,
                    'status' => 'connected',
                    'is_active' => true,
                    'last_connected_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Connection saved successfully.',
                'profile' => $profile
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save connection: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function generateQrCode(Request $request)
    {
        $sessionId = Str::uuid()->toString();
        
        session(['whatsapp_session_id' => $sessionId]);
        
        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'qr_code' => $this->generateQrCodeData($sessionId),
            'message' => 'QR code generated. Please scan with WhatsApp.'
        ]);
    }
    
    public function checkConnection(Request $request)
    {
        $sessionId = session('whatsapp_session_id');
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'connected' => false,
                'message' => 'No active session found.'
            ]);
        }
        
        $profile = WhatsappProfile::where('session_id', $sessionId)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($profile && $profile->status === 'connected') {
            return response()->json([
                'success' => true,
                'connected' => true,
                'profile' => $profile,
                'message' => 'WhatsApp connected successfully!'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'connected' => false,
            'message' => 'Waiting for WhatsApp connection...'
        ]);
    }
    
    public function webhook(Request $request)
    {
        $data = $request->all();
        
        if (isset($data['type']) && $data['type'] === 'qr_scanned') {
            $this->handleQrScanned($data);
        } elseif (isset($data['type']) && $data['type'] === 'message') {
            $this->handleIncomingMessage($data);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function disconnect(Request $request, $profileId)
    {
        $profile = WhatsappProfile::where('id', $profileId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        // Delete the profile and all related data
        // Note: Conversations and messages will be deleted via cascade if set up in migration
        $profile->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'WhatsApp profile deleted successfully.'
        ]);
    }
    
    public function getConversations(Request $request, $profileId)
    {
        $conversations = Conversation::where('whatsapp_profile_id', $profileId)
            ->whereHas('whatsappProfile', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }
    
    public function getMessages(Request $request, $conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->whereHas('whatsappProfile', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
    
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:4096'
        ]);
        
        $conversation = Conversation::whereHas('whatsappProfile', function($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($conversationId);
        
        $message = Message::create([
            'conversation_id' => $conversationId,
            'whatsapp_profile_id' => $conversation->whatsapp_profile_id,
            'message_id' => 'msg_' . Str::uuid(),
            'sender' => 'outgoing',
            'content' => $request->message,
            'type' => 'text',
            'status' => 'pending',
            'timestamp' => now()
        ]);
        
        $conversation->update([
            'last_message_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    private function generateQrCodeData($sessionId)
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($sessionId);
    }
    
    private function handleQrScanned($data)
    {
        $sessionId = $data['session_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        $storeId = $data['store_id'] ?? null;
        $phone = $data['phone'] ?? null;
        
        if (!$sessionId || !$userId || !$phone) {
            \Log::warning('WhatsApp QR scanned but missing required data', $data);
            return;
        }
        
        \Log::info('Saving WhatsApp connection', [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'store_id' => $storeId,
            'name' => $data['name'] ?? 'WhatsApp User',
            'phone' => $phone
        ]);
        
        // First check if profile exists by phone_number (unique constraint)
        $profile = WhatsappProfile::where('phone_number', $phone)->first();
        
        if ($profile) {
            // Update existing profile
            $profile->update([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'store_id' => $storeId,
                'name' => $data['name'] ?? 'WhatsApp User',
                'profile_picture' => $data['profile_picture'] ?? null,
                'status' => 'connected',
                'is_active' => true,
                'last_connected_at' => now()
            ]);
            \Log::info('WhatsApp profile updated successfully', ['id' => $profile->id]);
        } else {
            // Create new profile
            $profile = WhatsappProfile::create([
                'phone_number' => $phone,
                'session_id' => $sessionId,
                'user_id' => $userId,
                'store_id' => $storeId,
                'name' => $data['name'] ?? 'WhatsApp User',
                'profile_picture' => $data['profile_picture'] ?? null,
                'status' => 'connected',
                'is_active' => true,
                'last_connected_at' => now()
            ]);
            \Log::info('WhatsApp profile created successfully', ['id' => $profile->id]);
        }
    }
    
    private function handleIncomingMessage($data)
    {
        $profileId = $data['profile_id'] ?? null;
        $fromNumber = $data['from'] ?? null;
        
        if (!$profileId || !$fromNumber) {
            return;
        }
        
        $conversation = Conversation::firstOrCreate(
            [
                'whatsapp_profile_id' => $profileId,
                'contact_phone' => $fromNumber
            ],
            [
                'contact_name' => $data['contact_name'] ?? $fromNumber,
                'last_message_at' => now()
            ]
        );
        
        Message::create([
            'conversation_id' => $conversation->id,
            'whatsapp_profile_id' => $profileId,
            'message_id' => $data['message_id'] ?? 'msg_' . Str::uuid(),
            'sender' => 'incoming',
            'content' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'text',
            'status' => 'received',
            'timestamp' => $data['timestamp'] ?? now()
        ]);
        
        $conversation->update([
            'last_message_at' => now()
        ]);
    }
    
    /**
     * Process incoming message and generate AI response
     */
    public function processMessageWithAi(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            $userId = $request->input('user_id');
            $from = $request->input('from');
            $messageContent = $request->input('message');
            $messageId = $request->input('message_id');
            $contactName = $request->input('contact_name');
            $messageType = $request->input('type', 'text');
            $mediaUrl = $request->input('media_url');
            
            \Log::info('Processing message with AI', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'from' => $from,
                'message' => $messageContent,
                'type' => $messageType,
                'media_url' => $mediaUrl
            ]);
            
            // Handle voice/audio messages - transcribe them first
            if (in_array($messageType, ['audio', 'voice', 'ptt']) && !empty($mediaUrl)) {
                \Log::info('Processing audio message', ['media_url' => $mediaUrl]);
                $messageContent = $this->transcribeAudio($userId, $mediaUrl);
                
                if (empty($messageContent)) {
                    \Log::warning('Failed to transcribe audio message');
                    return response()->json([
                        'success' => true, 
                        'ai_response' => "Sorry, I couldn't understand the voice message. Could you please type your message?",
                        'media_type' => null,
                        'media_url' => null
                    ]);
                }
                
                \Log::info('Audio transcribed successfully', ['transcription' => $messageContent]);
            }
            
            // Skip if message is empty or null (other media messages without transcription)
            if (empty($messageContent)) {
                \Log::info('Skipping empty/null message');
                return response()->json(['success' => true, 'ai_response' => null]);
            }
            
            // Find the WhatsApp profile
            $profile = WhatsappProfile::where('session_id', $sessionId)
                ->where('user_id', $userId)
                ->first();
                
            if (!$profile) {
                \Log::warning('WhatsApp profile not found', [
                    'session_id' => $sessionId,
                    'user_id' => $userId
                ]);
                return response()->json(['success' => false, 'error' => 'Profile not found']);
            }
            
            \Log::info('Found WhatsApp profile', ['profile_id' => $profile->id]);
            
            // Find or create conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'whatsapp_profile_id' => $profile->id,
                    'contact_phone' => $from
                ],
                [
                    'contact_name' => $contactName ?? $from,
                    'last_message_at' => now()
                ]
            );
            
            \Log::info('Conversation found/created', ['conversation_id' => $conversation->id]);
            
            // Don't save base64 data URLs (too large for database)
            // Only save URLs that are real HTTP/HTTPS URLs
            $dbMediaUrl = (!empty($mediaUrl) && !str_starts_with($mediaUrl, 'data:')) ? $mediaUrl : null;
            
            // Map message type to valid database enum value
            $validTypes = ['text', 'image', 'video', 'audio', 'document', 'location'];
            $dbType = in_array($messageType, $validTypes) ? $messageType : 'text';
            // Map ptt/voice to audio
            if (in_array($messageType, ['ptt', 'voice'])) {
                $dbType = 'audio';
            }
            
            // Save the incoming message
            Message::create([
                'conversation_id' => $conversation->id,
                'whatsapp_profile_id' => $profile->id,
                'whatsapp_message_id' => $messageId,
                'direction' => 'incoming',
                'content' => $messageContent,
                'type' => $dbType,
                'media_url' => $dbMediaUrl,
                'is_ai_response' => false,
                'is_read' => false
            ]);
            
            $conversation->update(['last_message_at' => now()]);
            
            // Check if AI auto-reply is enabled
            $aiService = new AiChatService($profile->user, $profile->store_id);
            $autoReplyEnabled = $aiService->isAutoReplyEnabled();
            
            \Log::info('AI auto-reply check', [
                'user_id' => $userId,
                'enabled' => $autoReplyEnabled
            ]);
            
            if (!$autoReplyEnabled) {
                \Log::info('AI auto-reply is disabled for user', ['user_id' => $userId]);
                return response()->json(['success' => true, 'ai_response' => null]);
            }
            
            // Get conversation history for context (last 10 messages)
            $history = Message::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return [
                        'sender' => $msg->sender,
                        'content' => $msg->content
                    ];
                })
                ->toArray();
            
            \Log::info('Generating AI response', ['history_count' => count($history)]);
            
            // Generate AI response with potential images
            $aiResponseData = $aiService->generateResponseWithMedia($messageContent, $history);
            
            \Log::info('AI response result', [
                'has_response' => !empty($aiResponseData['text']),
                'has_media' => !empty($aiResponseData['media_url'])
            ]);
            
            if ($aiResponseData && !empty($aiResponseData['text'])) {
                // Save AI response to database
                Message::create([
                    'conversation_id' => $conversation->id,
                    'whatsapp_profile_id' => $profile->id,
                    'whatsapp_message_id' => 'ai_' . Str::uuid(),
                    'direction' => 'outgoing',
                    'content' => $aiResponseData['text'],
                    'type' => $aiResponseData['media_url'] ? 'image' : 'text',
                    'media_url' => $aiResponseData['media_url'] ?? null,
                    'is_ai_response' => true,
                    'is_read' => true
                ]);
                
                $conversation->update(['last_message_at' => now()]);
                
                \Log::info('AI response generated', [
                    'conversation_id' => $conversation->id,
                    'response_length' => strlen($aiResponseData['text']),
                    'has_media' => !empty($aiResponseData['media_url'])
                ]);
                
                return response()->json([
                    'success' => true,
                    'ai_response' => $aiResponseData['text'],
                    'media_type' => $aiResponseData['media_url'] ? 'image' : null,
                    'media_url' => $aiResponseData['media_url'] ?? null
                ]);
            }
            
            return response()->json(['success' => true, 'ai_response' => null]);
            
        } catch (\Exception $e) {
            \Log::error('Error processing message with AI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Transcribe audio message using OpenAI Whisper
     */
    private function transcribeAudio(int $userId, string $audioUrl): ?string
    {
        $tempFilePath = null;
        
        try {
            // Get user's AI API settings
            $user = \App\Models\User::find($userId);
            if (!$user || !$user->aiApiSetting || !$user->aiApiSetting->openai_api_key) {
                \Log::warning('No OpenAI API key found for voice transcription', ['user_id' => $userId]);
                return null;
            }
            
            \Log::info('Starting audio transcription', [
                'user_id' => $userId,
                'url_type' => str_starts_with($audioUrl, 'data:') ? 'data_url' : 'http_url',
                'url_length' => strlen($audioUrl)
            ]);
            
            // Handle different URL types
            $audioContent = null;
            $extension = 'ogg';
            $mimeType = 'audio/ogg';
            
            if (str_starts_with($audioUrl, 'data:')) {
                // Parse data URL: data:audio/ogg;base64,XXXX or data:audio/ogg; codecs=opus;base64,XXXX
                // The mime type can contain parameters like "; codecs=opus"
                $base64Pos = strpos($audioUrl, ';base64,');
                
                if ($base64Pos === false) {
                    \Log::error('Invalid data URL format - no base64 marker found', [
                        'url_preview' => substr($audioUrl, 0, 100)
                    ]);
                    return null;
                }
                
                // Extract mime type (between "data:" and ";base64,")
                $mimeType = substr($audioUrl, 5, $base64Pos - 5);
                $base64Data = substr($audioUrl, $base64Pos + 8);
                $audioContent = base64_decode($base64Data);
                
                if ($audioContent === false || empty($audioContent)) {
                    \Log::error('Failed to decode base64 audio data');
                    return null;
                }
                
                // Determine extension from mime type
                $mimeTypeLower = strtolower(trim($mimeType));
                if (str_contains($mimeTypeLower, 'ogg') || str_contains($mimeTypeLower, 'opus')) {
                    $extension = 'ogg';
                } elseif (str_contains($mimeTypeLower, 'mpeg') || str_contains($mimeTypeLower, 'mp3')) {
                    $extension = 'mp3';
                } elseif (str_contains($mimeTypeLower, 'mp4') || str_contains($mimeTypeLower, 'm4a')) {
                    $extension = 'm4a';
                } elseif (str_contains($mimeTypeLower, 'wav')) {
                    $extension = 'wav';
                } elseif (str_contains($mimeTypeLower, 'webm')) {
                    $extension = 'webm';
                } else {
                    $extension = 'ogg';
                }
                
                \Log::info('Parsed data URL successfully', [
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'audio_size' => strlen($audioContent)
                ]);
            } else {
                // Regular HTTP URL
                $audioContent = @file_get_contents($audioUrl);
                $extension = $this->getAudioExtension($audioUrl);
            }
            
            if (empty($audioContent)) {
                \Log::error('Failed to get audio content');
                return null;
            }
            
            // Create temp file with proper extension
            $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'whatsapp_audio_' . uniqid() . '.' . $extension;
            file_put_contents($tempFilePath, $audioContent);
            
            \Log::info('Audio file saved to temp', [
                'path' => $tempFilePath,
                'size' => filesize($tempFilePath),
                'extension' => $extension
            ]);
            
            $apiKey = $user->aiApiSetting->openai_api_key;
            $audioBytes = file_get_contents($tempFilePath);

            // STEP 1: First attempt — auto-detect language using verbose_json so we get the
            // detected language back. We also provide a multilingual prompt that includes
            // common phrases in Arabic/Darija/French/English so Whisper biases its detection
            // toward these languages instead of obscure ones (e.g., Welsh).
            $multilingualPrompt = "Conversation client / محادثة زبون / Customer chat. "
                . "Darija: شنو كاين؟ بشحال؟ واخا. "
                . "Arabic: مرحبا، شكرا، من فضلك. "
                . "French: bonjour, merci, s'il vous plaît. "
                . "English: hello, thanks, please.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->timeout(120)
            ->attach('file', $audioBytes, 'audio.' . $extension)
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
                'prompt' => $multilingualPrompt,
                'response_format' => 'verbose_json',
                'temperature' => 0.0,
            ]);

            if (!$response->successful()) {
                \Log::error('OpenAI Whisper API error (auto-detect)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                if ($tempFilePath && file_exists($tempFilePath)) {
                    unlink($tempFilePath);
                }
                return null;
            }

            $result = $response->json();
            $transcription = $result['text'] ?? null;
            $detectedLang = $result['language'] ?? null;

            \Log::info('Whisper auto-detect result', [
                'detected_language' => $detectedLang,
                'transcription' => $transcription,
            ]);

            // STEP 2: If Whisper detected an unlikely language for our context
            // (e.g., Welsh, Irish, Maori, Hawaiian, etc.), this almost always means
            // it confused Darija/Arabic. Retry with Arabic forced.
            $unlikelyLanguages = [
                'welsh', 'cy',
                'irish', 'ga',
                'maori', 'mi',
                'hawaiian', 'haw',
                'icelandic', 'is',
                'malagasy', 'mg',
                'somali', 'so',
                'sundanese', 'su',
                'javanese', 'jv',
                'lao', 'lo',
                'khmer', 'km',
                'myanmar', 'my',
                'mongolian', 'mn',
                'tibetan', 'bo',
                'nynorsk', 'nn',
            ];

            $detectedLower = strtolower((string) $detectedLang);
            if (in_array($detectedLower, $unlikelyLanguages, true)) {
                \Log::warning('Whisper detected unlikely language, retrying as Arabic', [
                    'detected' => $detectedLang,
                    'original_text' => $transcription,
                ]);

                $arabicPrompt = 'هذه محادثة بالدارجة المغربية أو العربية الفصحى بين زبون والبائع.';

                $retryResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->timeout(120)
                ->attach('file', $audioBytes, 'audio.' . $extension)
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'ar',
                    'prompt' => $arabicPrompt,
                    'response_format' => 'json',
                    'temperature' => 0.0,
                ]);

                if ($retryResponse->successful()) {
                    $arabicText = $retryResponse->json()['text'] ?? null;
                    if (!empty($arabicText)) {
                        $transcription = $arabicText;
                        $detectedLang = 'arabic';
                        \Log::info('Arabic retry succeeded', [
                            'transcription' => $transcription,
                        ]);
                    }
                } else {
                    \Log::error('OpenAI Whisper API error (Arabic retry)', [
                        'status' => $retryResponse->status(),
                        'body' => $retryResponse->body(),
                    ]);
                }
            }

            // Clean up temp file
            if ($tempFilePath && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }

            \Log::info('Audio transcribed successfully', [
                'language' => $detectedLang,
                'transcription' => $transcription,
            ]);

            return $transcription;
            
        } catch (\Exception $e) {
            // Clean up temp file on error
            if ($tempFilePath && file_exists($tempFilePath)) {
                @unlink($tempFilePath);
            }
            
            \Log::error('Error transcribing audio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Get audio file extension from URL
     */
    private function getAudioExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Default to ogg for WhatsApp voice messages
        if (empty($extension) || !in_array($extension, ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm', 'ogg'])) {
            return 'ogg';
        }
        
        return $extension;
    }
}
