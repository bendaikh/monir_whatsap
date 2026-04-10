@extends('layouts.customer')

@section('content')

    <div class="space-y-6">
        <!-- Store Info Banner -->
        @if(isset($activeStore) && $activeStore)
        <div class="bg-gradient-to-r from-emerald-500/20 to-blue-500/20 border border-emerald-500/30 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-emerald-400">Currently Managing</p>
                        <p class="text-lg font-bold text-white">{{ $activeStore->name }}</p>
                    </div>
                </div>
                <a href="{{ route('stores.dashboard') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-lg transition">
                    Switch Store
                </a>
            </div>
        </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Products Card -->
            <a href="{{ route('app.products') }}" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-emerald-500/30 transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['products'] ?? 0 }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Products</p>
                </div>
            </a>

            <!-- Categories Card -->
            <a href="{{ route('app.categories') }}" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-blue-500/30 transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['categories'] ?? 0 }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Categories</p>
                </div>
            </a>

            <!-- Orders Card -->
            <a href="{{ route('app.leads') }}" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-purple-500/30 transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['orders'] ?? 0 }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Orders</p>
                </div>
            </a>

            <!-- Conversations Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['conversations'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Conversations</p>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['messages'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Messages</p>
                </div>
            </div>

            <!-- Active Profiles Card -->
            <a href="{{ route('app.whatsapp') }}" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-green-500/30 transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['active_profiles'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">WhatsApp Profiles</p>
                </div>
            </a>
        </div>

        <!-- Quick Actions for this Store -->
        <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('app.products.create') }}" class="flex flex-col items-center gap-2 p-4 bg-white/5 hover:bg-white/10 rounded-lg transition">
                    <div class="p-3 bg-emerald-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-300">Add Product</span>
                </a>
                <a href="{{ route('app.categories') }}" class="flex flex-col items-center gap-2 p-4 bg-white/5 hover:bg-white/10 rounded-lg transition">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-300">Categories</span>
                </a>
                <a href="{{ route('app.website-customization') }}" class="flex flex-col items-center gap-2 p-4 bg-white/5 hover:bg-white/10 rounded-lg transition">
                    <div class="p-3 bg-purple-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-300">Customize</span>
                </a>
                @if(isset($activeStore) && $activeStore)
                <a href="{{ route('store.home', $activeStore->subdomain) }}" target="_blank" class="flex flex-col items-center gap-2 p-4 bg-white/5 hover:bg-white/10 rounded-lg transition">
                    <div class="p-3 bg-cyan-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-300">Visit Store</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Alerts and Recent Conversations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Alerts -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-white">Alerts</h3>
                </div>
                
                @if($whatsapp_profiles->isEmpty())
                    <div class="flex items-center gap-3 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-400">No WhatsApp Connected</p>
                            <p class="text-xs text-gray-400 mt-1">Connect WhatsApp to start receiving orders automatically</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-emerald-400">All Systems Operational</p>
                            <p class="text-xs text-gray-400 mt-1">Everything is working normally</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Conversations -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Conversations</h3>
                
                @if($recent_conversations->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No conversations yet</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recent_conversations->take(5) as $conversation)
                            <a href="{{ route('app.conversation.detail', $conversation->id) }}" class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-lg transition">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-semibold text-sm">{{ substr($conversation->contact_name ?? $conversation->contact_phone, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $conversation->contact_name ?? $conversation->contact_phone }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ Str::limit($conversation->last_message, 40) }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs text-gray-500">{{ $conversation->last_message_at?->diffForHumans() ?? 'N/A' }}</p>
                                    @if($conversation->unread_count > 0)
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-500 text-white text-xs font-semibold rounded-full">{{ $conversation->unread_count }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Start Guide for Empty Store -->
        @if(($stats['products'] ?? 0) == 0)
            <div class="bg-gradient-to-br from-emerald-500/20 to-blue-500/20 border border-emerald-500/30 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 class="text-2xl font-bold text-white mb-2">Get Started with Your Store</h3>
                <p class="text-gray-400 mb-6">Add your first product to start selling</p>
                <a href="{{ route('app.products.create') }}" class="inline-block px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition">
                    Add Your First Product
                </a>
            </div>
        @endif
    </div>
@endsection
