<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">Chatbot IA</h2>
            <p class="text-sm text-gray-400 mt-1">Configurez le comportement de votre assistant IA (Modèle: GPT-4o)</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session('openai_success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('openai_success') }}
        </div>
    @endif
    @if (session('openai_error'))
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ session('openai_error') }}
        </div>
    @endif
    @if (session('anthropic_success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('anthropic_success') }}
        </div>
    @endif
    @if (session('anthropic_error'))
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ session('anthropic_error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Settings -->
        <div class="lg:col-span-2 space-y-6">
            <!-- OpenAI connection -->
            <div id="openai-connect" class="scroll-mt-6 bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Connexion OpenAI</h3>
                        <p class="text-sm text-gray-400 mt-1">Collez votre clé API (sk-…). Elle est chiffrée côté serveur et n’apparaît jamais en clair après enregistrement.</p>
                    </div>
                    @if($aiSetting && $aiSetting->openai_api_key_encrypted)
                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300">Clé enregistrée</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-500/15 px-3 py-1 text-xs font-medium text-amber-200">Non configuré</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('app.ai-settings.openai.save') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="openai_api_key" class="block text-sm font-medium text-gray-300 mb-2">Clé API</label>
                        <input type="password" name="openai_api_key" id="openai_api_key" autocomplete="off"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition"
                            placeholder="{{ $aiSetting && $aiSetting->openai_api_key_encrypted ? 'Nouvelle clé pour remplacer l’actuelle' : 'sk-...' }}">
                        @error('openai_api_key')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="openai_model" class="block text-sm font-medium text-gray-300 mb-2">Modèle par défaut</label>
                        <input type="text" name="openai_model" id="openai_model" required
                            value="{{ old('openai_model', optional($aiSetting)->openai_model ?? 'gpt-4o') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition"
                            placeholder="gpt-4o">
                        @error('openai_model')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 text-sm text-gray-300 cursor-pointer select-none p-3 bg-[#0a1628] rounded-lg border border-white/10 hover:border-emerald-500/50 transition">
                            <input type="checkbox" name="auto_reply_enabled" value="1" 
                                {{ optional($aiSetting)->auto_reply_enabled ? 'checked' : '' }}
                                class="rounded border-white/20 bg-[#0a1628] text-emerald-500 focus:ring-emerald-500/30">
                            <div>
                                <div class="font-medium text-white">Enable AI Auto-Reply on WhatsApp</div>
                                <div class="text-xs text-gray-400 mt-0.5">AI will automatically respond to incoming WhatsApp messages</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer select-none">
                            <input type="checkbox" name="clear_openai_key" value="1" class="rounded border-white/20 bg-[#0a1628] text-cyan-500 focus:ring-cyan-500/30">
                            Supprimer la clé enregistrée
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold rounded-lg transition">
                            Enregistrer
                        </button>
                    </div>
                </form>

                @if($aiSetting && $aiSetting->openai_api_key_encrypted)
                    <form method="POST" action="{{ route('app.ai-settings.openai.test') }}" class="mt-4 pt-4 border-t border-white/10">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-[#0a1628] border border-white/15 hover:border-cyan-500/50 text-white text-sm font-medium rounded-lg transition">
                            Tester la connexion OpenAI
                        </button>
                    </form>
                @endif
            </div>

            <!-- Anthropic connection -->
            <div id="anthropic-connect" class="scroll-mt-6 bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Connexion Anthropic (Claude)</h3>
                        <p class="text-sm text-gray-400 mt-1">Alternative à OpenAI. Collez votre clé API Anthropic (sk-ant-…). Elle est chiffrée côté serveur.</p>
                    </div>
                    @if($aiSetting && $aiSetting->anthropic_api_key_encrypted)
                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300">Clé enregistrée</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-500/15 px-3 py-1 text-xs font-medium text-amber-200">Non configuré</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('app.ai-settings.anthropic.save') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="anthropic_api_key" class="block text-sm font-medium text-gray-300 mb-2">Clé API Anthropic</label>
                        <input type="password" name="anthropic_api_key" id="anthropic_api_key" autocomplete="off"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition"
                            placeholder="{{ $aiSetting && $aiSetting->anthropic_api_key_encrypted ? 'Nouvelle clé pour remplacer l\'actuelle' : 'sk-ant-...' }}">
                        @error('anthropic_api_key')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="anthropic_model" class="block text-sm font-medium text-gray-300 mb-2">Modèle par défaut</label>
                        <select name="anthropic_model" id="anthropic_model" required
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 focus:outline-none transition">
                            <option value="claude-3-5-sonnet-20241022" {{ old('anthropic_model', optional($aiSetting)->anthropic_model ?? 'claude-3-5-sonnet-20241022') == 'claude-3-5-sonnet-20241022' ? 'selected' : '' }}>Claude 3.5 Sonnet (Recommended)</option>
                            <option value="claude-3-5-haiku-20241022" {{ old('anthropic_model', optional($aiSetting)->anthropic_model) == 'claude-3-5-haiku-20241022' ? 'selected' : '' }}>Claude 3.5 Haiku (Faster, Cheaper)</option>
                            <option value="claude-3-opus-20240229" {{ old('anthropic_model', optional($aiSetting)->anthropic_model) == 'claude-3-opus-20240229' ? 'selected' : '' }}>Claude 3 Opus (Most Capable)</option>
                        </select>
                        @error('anthropic_model')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer select-none">
                        <input type="checkbox" name="clear_anthropic_key" value="1" class="rounded border-white/20 bg-[#0a1628] text-purple-500 focus:ring-purple-500/30">
                        Supprimer la clé enregistrée
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="px-5 py-2.5 bg-purple-500 hover:bg-purple-600 text-white text-sm font-semibold rounded-lg transition">
                            Enregistrer
                        </button>
                    </div>
                </form>

                @if($aiSetting && $aiSetting->anthropic_api_key_encrypted)
                    <form method="POST" action="{{ route('app.ai-settings.anthropic.test') }}" class="mt-4 pt-4 border-t border-white/10">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-[#0a1628] border border-white/15 hover:border-purple-500/50 text-white text-sm font-medium rounded-lg transition">
                            Tester la connexion Anthropic
                        </button>
                    </form>
                @endif
            </div>

            <!-- WhatsApp Profile Selection -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Profil WhatsApp</h3>
                @if($profiles->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-400 mb-4">Aucun profil WhatsApp connecté. 
                            <a href="{{ route('app.whatsapp') }}" class="text-cyan-400 hover:text-cyan-300">Connectez-en un d'abord.</a>
                        </p>
                    </div>
                @else
                    <select class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition">
                        <option>Sélectionner un profil</option>
                        @foreach($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }} ({{ $profile->phone_number }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Auto Response Toggle -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-cyan-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Réponses Automatiques IA</h3>
                            <p class="text-sm text-gray-400">Activer le chatbot IA pour répondre automatiquement</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
            </div>

            <!-- Intelligence Level -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Niveau d'intelligence</h3>
                <p class="text-sm text-gray-400 mb-6">Choisissez la puissance de votre assistant. Plus le niveau est élevé, plus il consomme de tokens.</p>
                
                <div class="space-y-3">
                    <!-- Eco Option -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-emerald-500 rounded-lg cursor-pointer hover:bg-white/5 transition">
                        <input type="radio" name="intelligence" value="eco" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2" checked>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z"/>
                                </svg>
                                <span class="font-semibold text-white">Éco</span>
                            </div>
                            <p class="text-sm text-gray-400">Économique - Idéal pour les réponses simples</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-emerald-500 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                    </label>

                    <!-- Standard Option -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-transparent rounded-lg cursor-pointer hover:bg-white/5 hover:border-white/10 transition">
                        <input type="radio" name="intelligence" value="standard" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-semibold text-white">Standard</span>
                            </div>
                            <p class="text-sm text-gray-400">Bon équilibre qualité/coût</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600"></div>
                    </label>

                    <!-- Expert Option -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-transparent rounded-lg cursor-pointer hover:bg-white/5 hover:border-white/10 transition">
                        <input type="radio" name="intelligence" value="expert" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-semibold text-white">Expert</span>
                            </div>
                            <p class="text-sm text-gray-400">Réponses avancées, consomme plus</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600"></div>
                    </label>
                </div>
            </div>

            <!-- Response Length -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Longueur des réponses</h3>
                <p class="text-sm text-gray-400 mb-6">Contrôlez la taille des réponses du bot. Plus court = moins de tokens consommés.</p>
                
                <div class="space-y-3">
                    <!-- Court -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-emerald-500 rounded-lg cursor-pointer hover:bg-white/5 transition">
                        <input type="radio" name="length" value="short" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2" checked>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z"/>
                                </svg>
                                <span class="font-semibold text-white">Court</span>
                            </div>
                            <p class="text-sm text-gray-400">Réponses rapides et concises</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-emerald-500 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                    </label>

                    <!-- Moyen -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-transparent rounded-lg cursor-pointer hover:bg-white/5 hover:border-white/10 transition">
                        <input type="radio" name="length" value="medium" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-semibold text-white">Moyen (Recommandé)</span>
                            </div>
                            <p class="text-sm text-gray-400">Réponses équilibrées</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600"></div>
                    </label>

                    <!-- Long -->
                    <label class="flex items-center gap-4 p-4 bg-[#0a1628] border-2 border-transparent rounded-lg cursor-pointer hover:bg-white/5 hover:border-white/10 transition">
                        <input type="radio" name="length" value="long" class="w-5 h-5 text-emerald-500 bg-[#0a1628] border-white/20 focus:ring-emerald-500 focus:ring-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                                <span class="font-semibold text-white">Long</span>
                            </div>
                            <p class="text-sm text-gray-400">Réponses détaillées et complètes</p>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Right Column - Prompt Editor -->
        <div class="space-y-6">
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden sticky top-6">
                <!-- Tabs -->
                <div class="flex border-b border-white/10">
                    <button class="flex-1 px-4 py-3 text-sm font-medium text-white bg-cyan-500/20 border-b-2 border-cyan-400">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                        Prompt Principal
                    </button>
                    <button class="flex-1 px-4 py-3 text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 transition">
                        Prompts par défaut
                    </button>
                    <button class="px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </button>
                    <button class="px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </button>
                </div>

                <!-- Prompt Content -->
                <div class="p-4">
                    <p class="text-sm text-gray-400 mb-4">Définissez le comportement et la personnalité de votre chatbot IA</p>
                    
                    <textarea 
                        class="w-full h-96 px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white text-sm font-mono resize-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none"
                        placeholder="Écrivez votre prompt ici..."
                    >Tu es un assistant commercial WhatsApp intelligent et professionnel.

🟢 LANGUE :
• Réponds TOUJOURS dans la langue du client (français, arabe, darija, anglais, etc.)
• Si le client demande de changer de langue, change immédiatement

🟣 RÈGLES PRINCIPALES :
• Sois concis et direct - c'est WhatsApp, pas un email
• Utilise des emojis avec modération pour rendre la conversation chaleureuse
• Tutoie le client pour créer une relation de proximité

🟠 INFORMATIONS PRODUITS - RÈGLE ABSOLUE :
• Quand un client demande des infos sur un produit, COPIE EXACTEMENT la description du catalogue
• N'invente JAMAIS d'information sur un produit qui est dans le catalogue
• Ne résume pas et ne minimise pas les descriptions - envoie-les EN ENTIER

🔴 TON & STYLE :
• Amical mais professionnel
• Réactif et serviable
• Ne jamais ignorer une question
• En cas de doute, demande des précisions

🔵 MESSAGES AUDIO :
• Les clients peuvent envoyer des audios en Darija (dialecte marocain), Arabe ou Français
• Même si la transcription n'est pas parfaite, essaie de comprendre le sens général
• Si tu ne comprends pas, demande poliment au client de répéter</textarea>

                    <p class="text-xs text-gray-500 mt-2">Ce prompt définit comment l'IA interagit avec les clients</p>
                </div>

                <!-- Save Button -->
                <div class="p-4 border-t border-white/10">
                    <button class="w-full py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Sauvegarder
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
