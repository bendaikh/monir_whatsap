@extends('layouts.customer')

@section('content')

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Conversations Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="p-3 bg-emerald-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['messages'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Message</p>
                </div>
            </div>

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
                    <p class="text-3xl font-bold text-white">{{ $stats['orders'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Commandes</p>
                </div>
            </a>

            <!-- Active Profiles Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['active_profiles'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Profils Actifs</p>
                </div>
            </div>

            <!-- AI Tokens Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['ai_tokens'] }}</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Tokens IA</p>
                </div>
            </div>

            <!-- Sales Percentage Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-pink-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['sales_percentage'] }}%</p>
                    <p class="text-sm text-gray-400 mt-1 uppercase tracking-wide">Ventes %</p>
                </div>
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
                    <h3 class="text-lg font-semibold text-white">Alertes</h3>
                </div>
                
                @if($whatsapp_profiles->isEmpty())
                    <div class="flex items-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-emerald-400">Aucune alerte</p>
                            <p class="text-xs text-gray-400 mt-1">Connectez votre premier profil WhatsApp pour commencer</p>
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
                            <p class="text-sm font-medium text-emerald-400">Aucune alerte</p>
                            <p class="text-xs text-gray-400 mt-1">Tout fonctionne normalement</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Conversations -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Conversations récentes</h3>
                
                @if($recent_conversations->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Aucune conversation</p>
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

        <!-- Quick Actions -->
        @if($whatsapp_profiles->isEmpty())
            <div class="bg-gradient-to-br from-emerald-500/20 to-blue-500/20 border border-emerald-500/30 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 text-emerald-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <h3 class="text-2xl font-bold text-white mb-2">Connectez votre WhatsApp</h3>
                <p class="text-gray-400 mb-6">Commencez à automatiser vos conversations en connectant votre premier profil WhatsApp</p>
                <a href="{{ route('app.whatsapp') }}" class="inline-block px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition">
                    Connecter WhatsApp
                </a>
            </div>
        @endif
    </div>
@endsection
