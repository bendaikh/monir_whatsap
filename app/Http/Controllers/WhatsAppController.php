<?php

namespace App\Http\Controllers;

use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiChatService;
use Illuminate\Http\Request;
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
            
            \Log::info('Processing message with AI', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'from' => $from,
                'message' => $messageContent
            ]);
            
            // Skip if message is empty or null (media messages, etc.)
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
            
            // Save the incoming message
            Message::create([
                'conversation_id' => $conversation->id,
                'whatsapp_profile_id' => $profile->id,
                'message_id' => $messageId,
                'sender' => 'incoming',
                'content' => $messageContent,
                'type' => 'text',
                'status' => 'received',
                'timestamp' => now()
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
            
            // Generate AI response
            $aiResponse = $aiService->generateResponse($messageContent, $history);
            
            \Log::info('AI response result', ['has_response' => !empty($aiResponse)]);
            
            if ($aiResponse) {
                // Save AI response to database
                Message::create([
                    'conversation_id' => $conversation->id,
                    'whatsapp_profile_id' => $profile->id,
                    'message_id' => 'ai_' . Str::uuid(),
                    'sender' => 'outgoing',
                    'content' => $aiResponse,
                    'type' => 'text',
                    'status' => 'sent',
                    'timestamp' => now()
                ]);
                
                $conversation->update(['last_message_at' => now()]);
                
                \Log::info('AI response generated', [
                    'conversation_id' => $conversation->id,
                    'response_length' => strlen($aiResponse)
                ]);
                
                return response()->json([
                    'success' => true,
                    'ai_response' => $aiResponse
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
}
