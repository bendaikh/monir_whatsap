@extends('layouts.customer')

@section('content')

    <div class="space-y-6" x-data="whatsappManager()">
        <!-- Add New Profile Button -->
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-white">Connected Profiles</h3>
                <p class="text-sm text-gray-400 mt-1">You have {{$profiles->count() }} connected profile(s)</p>
            </div>
            <button @click="openQrModal()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition shadow-lg shadow-emerald-500/30">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Connect New WhatsApp
            </button>
        </div>

        <!-- Profiles Grid -->
        @if($profiles->isEmpty())
            <!-- Empty State -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-12 text-center">
                <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">No WhatsApp Profiles Connected</h3>
                <p class="text-gray-400 mb-6">Connect your first WhatsApp profile to start managing conversations</p>
                <button @click="openQrModal()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition shadow-lg shadow-emerald-500/30">
                    Connect Your First Profile
                </button>
            </div>
        @else
            <!-- Profiles List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($profiles as $profile)
                    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-emerald-500/50 transition">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                @if($profile->profile_picture)
                                    <img src="{{ $profile->profile_picture }}" alt="{{ $profile->name }}" class="w-12 h-12 rounded-full">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-lg">{{ substr($profile->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-white">{{ $profile->name }}</h4>
                                    <p class="text-sm text-gray-400">{{ $profile->phone_number }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $profile->status === 'connected' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $profile->status === 'connecting' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                {{ $profile->status === 'disconnected' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($profile->status) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400">Last Connected</span>
                                <span class="text-white">{{ $profile->last_connected_at?->diffForHumans() ?? 'Never' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400">Status</span>
                                <span class="text-white">
                                    @if($profile->is_active)
                                        <span class="text-green-400">Active</span>
                                    @else
                                        <span class="text-gray-400">Inactive</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            @if($profile->status === 'connected')
                                <button @click="openLiveChat('{{ $profile->session_id }}')" class="flex-1 py-2 px-4 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition">
                                    Open Live Chat
                                </button>
                            @else
                                <button @click="openQrModal()" class="flex-1 py-2 px-4 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition">
                                    Reconnect
                                </button>
                            @endif
                            <button @click="disconnectProfile({{ $profile->id }})" class="py-2 px-4 bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-medium rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- QR Code Modal -->
        <div x-show="showQrModal" x-cloak class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
            <div @click.away="closeQrModal()" class="bg-[#0f1c2e] border border-white/10 rounded-xl max-w-md w-full p-8">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">Scan QR Code</h3>
                    <p class="text-gray-400 mb-6">Open WhatsApp on your phone and scan this QR code</p>
                    
                    <div x-show="!qrConnected && qrCodeUrl" class="bg-white p-6 rounded-xl mb-6">
                        <img :src="qrCodeUrl" alt="QR Code" class="w-full h-auto">
                    </div>
                    
                    <div x-show="!qrCodeUrl && !qrConnected" class="mb-6">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-emerald-500 mx-auto"></div>
                        <p class="text-gray-400 mt-4">Generating QR code...</p>
                    </div>
                    
                    <div x-show="qrConnected" class="mb-6">
                        <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold text-white mb-2">Connected Successfully!</h4>
                        <p class="text-gray-400">Your WhatsApp account is now connected.</p>
                    </div>
                    
                    <div x-show="!qrConnected" class="space-y-3 text-left text-sm text-gray-300 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">1</span>
                            <span>Open WhatsApp on your phone</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">2</span>
                            <span>Tap Settings → Linked Devices</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">3</span>
                            <span>Tap "Link a Device" and scan this QR code</span>
                        </div>
                    </div>
                    
                    <button @click="closeQrModal()" class="w-full py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition">
                        <span x-text="qrConnected ? 'Done' : 'Cancel'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Chat Modal -->
        <div x-show="showChatModal" x-cloak class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
            <div @click.away="closeLiveChat()" class="bg-[#0f1c2e] border border-white/10 rounded-xl max-w-6xl w-full h-[80vh] flex">
                <!-- Conversations List -->
                <div class="w-1/3 border-r border-white/10 flex flex-col">
                    <div class="p-4 border-b border-white/10">
                        <h3 class="text-lg font-semibold text-white">Conversations</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <div x-show="loadingChats" class="flex items-center justify-center py-12">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto mb-4"></div>
                                <p class="text-gray-400">Loading conversations...</p>
                            </div>
                        </div>
                        
                        <div x-show="!loadingChats && conversations.length === 0" class="flex items-center justify-center py-12">
                            <p class="text-gray-400">No conversations found</p>
                        </div>
                        
                        <template x-for="conversation in conversations" :key="conversation.id">
                            <div @click="selectConversation(conversation.id)" 
                                 :class="selectedConversation === conversation.id ? 'bg-emerald-500/20' : 'hover:bg-white/5'"
                                 class="p-4 border-b border-white/10 cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-semibold" x-text="conversation.name ? conversation.name.charAt(0) : '?'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-white truncate" x-text="conversation.name"></h4>
                                        <p class="text-sm text-gray-400 truncate" x-text="conversation.lastMessage || 'No messages'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col">
                    <div class="p-4 border-b border-white/10 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white" x-text="selectedConversationName || 'Select a conversation'"></h3>
                        <button @click="closeLiveChat()" class="text-gray-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="message.sender === 'outgoing' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="message.sender === 'outgoing' ? 'bg-emerald-500' : 'bg-white/10'" 
                                     class="max-w-[70%] px-4 py-2 rounded-lg">
                                    <p class="text-white text-sm" x-text="message.body"></p>
                                    <p class="text-xs mt-1" :class="message.sender === 'outgoing' ? 'text-emerald-100' : 'text-gray-400'" 
                                       x-text="formatTime(message.timestamp)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="p-4 border-t border-white/10">
                        <form @submit.prevent="sendMessage()" class="flex gap-2">
                            <input x-model="newMessage" 
                                   type="text" 
                                   placeholder="Type a message..." 
                                   class="flex-1 px-4 py-3 bg-white/10 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-emerald-500">
                            <button type="submit" 
                                    :disabled="!newMessage.trim()"
                                    class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"></script>
    <script>
        function whatsappManager() {
            return {
                showQrModal: false,
                showChatModal: false,
                qrCodeUrl: '',
                qrConnected: false,
                socket: null,
                sessionId: null,
                
                conversations: [],
                messages: [],
                selectedConversation: null,
                selectedConversationName: '',
                currentSessionId: null,
                newMessage: '',
                loadingChats: false,
                
                init() {
                    const whatsappServiceUrl = '{{ env('WHATSAPP_SERVICE_URL', 'http://127.0.0.1:3000') }}';
                    this.socket = io(whatsappServiceUrl);
                    
                    this.socket.on('qr-code', (data) => {
                        console.log('QR Code received');
                        this.qrCodeUrl = data.qrCode;
                    });
                    
                    this.socket.on('whatsapp-connected', async (data) => {
                        console.log('WhatsApp connected:', data);
                        this.qrConnected = true;
                        
                        // Save to database
                        try {
                            const response = await fetch('/app/whatsapp/save-connection', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    session_id: data.sessionId,
                                    phone: data.phone,
                                    name: data.name
                                })
                            });
                            
                            const result = await response.json();
                            console.log('Save connection result:', result);
                            
                            if (!result.success) {
                                console.error('Failed to save connection:', result);
                            }
                        } catch (error) {
                            console.error('Error saving connection:', error);
                        }
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    });
                    
                    this.socket.on('new-message', (data) => {
                        console.log('New message:', data);
                        // Handle incoming message in real-time
                    });
                    
                    this.socket.on('chats-list', (data) => {
                        console.log('Chats received:', data.chats);
                        this.conversations = data.chats;
                        this.loadingChats = false;
                    });
                    
                    this.socket.on('whatsapp-already-connected', (data) => {
                        console.log('WhatsApp already connected:', data);
                    });
                    
                    this.socket.on('messages-list', (data) => {
                        this.messages = data.messages;
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    });
                    
                    this.socket.on('message-sent', (data) => {
                        console.log('Message sent successfully:', data);
                        // Message was sent successfully
                    });
                    
                    this.socket.on('new-message', (data) => {
                        console.log('New incoming message:', data);
                        
                        // Only add message if we're viewing a conversation
                        if (!this.selectedConversation || !this.showChatModal) {
                            return;
                        }
                        
                        // Check if this message belongs to the current conversation
                        const isIncoming = data.sender === 'incoming' && data.from === this.selectedConversation;
                        const isOutgoing = data.sender === 'outgoing' && data.to === this.selectedConversation;
                        
                        if (isIncoming || isOutgoing) {
                            console.log('Adding message to current conversation');
                            const newMsg = {
                                id: data.messageId,
                                body: data.body,
                                timestamp: data.timestamp,
                                sender: data.sender,
                                type: data.type
                            };
                            
                            // Check if message already exists (avoid duplicates)
                            const exists = this.messages.find(m => m.id === newMsg.id);
                            if (!exists) {
                                this.messages.push(newMsg);
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                });
                            }
                        }
                    });
                },
                
                openQrModal() {
                    this.showQrModal = true;
                    this.qrConnected = false;
                    this.qrCodeUrl = '';
                    this.sessionId = 'session_' + Date.now();
                    
                    this.socket.emit('init-whatsapp', {
                        sessionId: this.sessionId,
                        userId: {{ auth()->id() }}
                    });
                },
                
                closeQrModal() {
                    this.showQrModal = false;
                },
                
                async disconnectProfile(profileId) {
                    if (!confirm('Are you sure you want to DELETE this WhatsApp profile? This action cannot be undone and will remove all conversations and messages.')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/app/whatsapp/disconnect/${profileId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Failed to delete profile: ' + (data.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error deleting profile:', error);
                        alert('Failed to delete profile. Please try again.');
                    }
                },
                
                openLiveChat(sessionId) {
                    console.log('Opening live chat for session:', sessionId);
                    
                    if (!sessionId) {
                        alert('Session ID is missing. Please reconnect your WhatsApp account.');
                        return;
                    }
                    
                    this.currentSessionId = sessionId;
                    this.showChatModal = true;
                    this.conversations = [];
                    this.messages = [];
                    this.loadingChats = true;
                    
                    // First try to reconnect to the session
                    this.socket.emit('init-whatsapp', {
                        sessionId: sessionId,
                        userId: {{ auth()->id() }}
                    });
                    
                    // Also request chats
                    setTimeout(() => {
                        console.log('Requesting chats for session:', sessionId);
                        this.socket.emit('get-chats', { sessionId });
                    }, 2000);
                },
                
                closeLiveChat() {
                    this.showChatModal = false;
                },
                
                selectConversation(conversationId) {
                    this.selectedConversation = conversationId;
                    const conversation = this.conversations.find(c => c.id === conversationId);
                    this.selectedConversationName = conversation?.name || '';
                    
                    this.socket.emit('get-messages', {
                        sessionId: this.currentSessionId,
                        chatId: conversationId
                    });
                },
                
                sendMessage() {
                    if (!this.newMessage.trim() || !this.selectedConversation) {
                        return;
                    }
                    
                    const messageText = this.newMessage;
                    const tempMessage = {
                        id: 'temp_' + Date.now(),
                        body: messageText,
                        timestamp: Math.floor(Date.now() / 1000),
                        sender: 'outgoing',
                        type: 'chat'
                    };
                    
                    // Add message to UI immediately (optimistic update)
                    this.messages.push(tempMessage);
                    this.newMessage = '';
                    
                    // Scroll to bottom
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    
                    // Send to server
                    this.socket.emit('send-message', {
                        sessionId: this.currentSessionId,
                        to: this.selectedConversation,
                        message: messageText
                    });
                },
                
                scrollToBottom() {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                },
                
                formatTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp * 1000);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
