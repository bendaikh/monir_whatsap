@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Product
                </h2>
                <p class="text-sm text-gray-400 mt-1">Add a new product with E-Commerce Style landing page</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('app.products.select-theme') }}" class="px-4 py-2 bg-gray-700/50 hover:bg-gray-700 text-white font-medium rounded-lg transition flex items-center gap-2 text-sm border border-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Change Theme
                </a>
                <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
    </div>

    <!-- Selected Theme Badge -->
    <div class="mb-6 bg-[#0f1c2e] border border-cyan-500/30 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-cyan-500/20">
                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-semibold flex items-center gap-2">
                    Theme 2 - E-Commerce Style
                    <span class="text-xs bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded">Selected</span>
                </h3>
                <p class="text-sm text-gray-400">High-converting sales page with trust badges and social proof</p>
            </div>
        </div>
        <a href="{{ route('app.products.select-theme') }}" class="text-sm text-gray-400 hover:text-white transition">
            Change &rarr;
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $aiSetting = \App\Models\AiApiSetting::where('user_id', auth()->id())->first();
        $hasAiConfigured = $aiSetting && (!empty($aiSetting->openai_api_key_encrypted) || !empty($aiSetting->anthropic_api_key_encrypted));
    @endphp

    @if(!$hasAiConfigured)
    <div class="mb-6 bg-purple-500/20 border border-purple-500/50 text-purple-300 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <span class="font-semibold">AI Features Require Configuration</span>
            <span class="block text-sm mt-1">Please <a href="{{ route('workspaces.ai-settings') }}" class="underline hover:text-purple-200">configure your AI API settings</a> to use AI landing page and image generation features.</span>
        </div>
    </div>
    @endif

    <div class="max-w-4xl">
        <form action="{{ route('app.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="productForm">
            @csrf
            <input type="hidden" name="theme" value="{{ $theme }}">
            
            <!-- Landing Page Configuration (FIRST - Currency & Languages) -->
            <div class="bg-gradient-to-br from-indigo-900 to-purple-900 border border-indigo-500/50 rounded-xl p-6 shadow-xl">
                <h3 class="text-2xl font-bold text-white mb-2 flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    Landing Page Configuration
                </h3>
                <p class="text-indigo-200 text-sm mb-6">Configure currency and languages for your product's landing page. This affects pricing display and AI content generation.</p>

                <div class="space-y-6">
                    <!-- Currency Selection -->
                    <div>
                        <label for="landing_page_currency" class="block text-sm font-semibold text-white mb-3">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Currency *
                            </span>
                            <span class="block text-xs text-indigo-300 font-normal mt-1">All prices on the landing page will be displayed in this currency</span>
                        </label>
                        <select 
                            id="landing_page_currency" 
                            name="landing_page_currency"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                            onchange="updateCurrencyDisplay()"
                            required
                        >
                            <optgroup label="🌍 Popular Currencies">
                                <option value="USD" data-symbol="$">USD - US Dollar ($)</option>
                                <option value="EUR" data-symbol="€">EUR - Euro (€)</option>
                                <option value="GBP" data-symbol="£">GBP - British Pound (£)</option>
                                <option value="MAD" data-symbol="د.م." selected>MAD - Moroccan Dirham (د.م.)</option>
                                <option value="AED" data-symbol="د.إ">AED - UAE Dirham (د.إ)</option>
                                <option value="SAR" data-symbol="ر.س">SAR - Saudi Riyal (ر.س)</option>
                            </optgroup>
                            <optgroup label="💵 Americas">
                                <option value="ARS" data-symbol="$">ARS - Argentine Peso ($)</option>
                                <option value="BRL" data-symbol="R$">BRL - Brazilian Real (R$)</option>
                                <option value="CAD" data-symbol="$">CAD - Canadian Dollar ($)</option>
                                <option value="CLP" data-symbol="$">CLP - Chilean Peso ($)</option>
                                <option value="COP" data-symbol="$">COP - Colombian Peso ($)</option>
                                <option value="MXN" data-symbol="$">MXN - Mexican Peso ($)</option>
                                <option value="PEN" data-symbol="S/">PEN - Peruvian Sol (S/)</option>
                            </optgroup>
                            <optgroup label="🌏 Asia-Pacific">
                                <option value="AUD" data-symbol="$">AUD - Australian Dollar ($)</option>
                                <option value="BDT" data-symbol="৳">BDT - Bangladeshi Taka (৳)</option>
                                <option value="CNY" data-symbol="¥">CNY - Chinese Yuan (¥)</option>
                                <option value="HKD" data-symbol="$">HKD - Hong Kong Dollar ($)</option>
                                <option value="IDR" data-symbol="Rp">IDR - Indonesian Rupiah (Rp)</option>
                                <option value="INR" data-symbol="₹">INR - Indian Rupee (₹)</option>
                                <option value="JPY" data-symbol="¥">JPY - Japanese Yen (¥)</option>
                                <option value="KRW" data-symbol="₩">KRW - South Korean Won (₩)</option>
                                <option value="MYR" data-symbol="RM">MYR - Malaysian Ringgit (RM)</option>
                                <option value="NZD" data-symbol="$">NZD - New Zealand Dollar ($)</option>
                                <option value="PHP" data-symbol="₱">PHP - Philippine Peso (₱)</option>
                                <option value="PKR" data-symbol="₨">PKR - Pakistani Rupee (₨)</option>
                                <option value="SGD" data-symbol="$">SGD - Singapore Dollar ($)</option>
                                <option value="THB" data-symbol="฿">THB - Thai Baht (฿)</option>
                                <option value="VND" data-symbol="₫">VND - Vietnamese Dong (₫)</option>
                            </optgroup>
                            <optgroup label="🌍 Africa & Middle East">
                                <option value="EGP" data-symbol="£">EGP - Egyptian Pound (£)</option>
                                <option value="GHS" data-symbol="₵">GHS - Ghanaian Cedi (₵)</option>
                                <option value="ILS" data-symbol="₪">ILS - Israeli Shekel (₪)</option>
                                <option value="JOD" data-symbol="د.ا">JOD - Jordanian Dinar (د.ا)</option>
                                <option value="KES" data-symbol="KSh">KES - Kenyan Shilling (KSh)</option>
                                <option value="KWD" data-symbol="د.ك">KWD - Kuwaiti Dinar (د.ك)</option>
                                <option value="LBP" data-symbol="ل.ل">LBP - Lebanese Pound (ل.ل)</option>
                                <option value="NGN" data-symbol="₦">NGN - Nigerian Naira (₦)</option>
                                <option value="OMR" data-symbol="ر.ع.">OMR - Omani Rial (ر.ع.)</option>
                                <option value="QAR" data-symbol="ر.ق">QAR - Qatari Riyal (ر.ق)</option>
                                <option value="TND" data-symbol="د.ت">TND - Tunisian Dinar (د.ت)</option>
                                <option value="TRY" data-symbol="₺">TRY - Turkish Lira (₺)</option>
                                <option value="TZS" data-symbol="TSh">TZS - Tanzanian Shilling (TSh)</option>
                                <option value="ZAR" data-symbol="R">ZAR - South African Rand (R)</option>
                            </optgroup>
                            <optgroup label="🇪🇺 Europe">
                                <option value="BGN" data-symbol="лв">BGN - Bulgarian Lev (лв)</option>
                                <option value="CHF" data-symbol="Fr">CHF - Swiss Franc (Fr)</option>
                                <option value="CZK" data-symbol="Kč">CZK - Czech Koruna (Kč)</option>
                                <option value="DKK" data-symbol="kr">DKK - Danish Krone (kr)</option>
                                <option value="HUF" data-symbol="Ft">HUF - Hungarian Forint (Ft)</option>
                                <option value="NOK" data-symbol="kr">NOK - Norwegian Krone (kr)</option>
                                <option value="PLN" data-symbol="zł">PLN - Polish Złoty (zł)</option>
                                <option value="RON" data-symbol="lei">RON - Romanian Leu (lei)</option>
                                <option value="RUB" data-symbol="₽">RUB - Russian Ruble (₽)</option>
                                <option value="SEK" data-symbol="kr">SEK - Swedish Krona (kr)</option>
                                <option value="UAH" data-symbol="₴">UAH - Ukrainian Hryvnia (₴)</option>
                            </optgroup>
                        </select>
                        <p class="mt-2 text-xs text-indigo-200">
                            <span class="font-semibold">Selected:</span> <span id="selectedCurrencyDisplay" class="text-white">MAD - Moroccan Dirham (د.م.)</span>
                        </p>
                        @error('landing_page_currency')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Languages Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-3">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                                Landing Page Languages *
                            </span>
                            <span class="block text-xs text-indigo-300 font-normal mt-1">Select languages for AI to generate content in. Will show as tabs on landing page.</span>
                        </label>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-96 overflow-y-auto p-4 bg-white/5 rounded-lg border border-white/10">
                            <!-- Popular Languages -->
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="en" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇬🇧 English</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="fr" checked class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇫🇷 French</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ar" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇸🇦 Arabic</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="es" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇪🇸 Spanish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="de" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇩🇪 German</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="it" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇮🇹 Italian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="pt" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇵🇹 Portuguese</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="zh" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇨🇳 Chinese</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ja" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇯🇵 Japanese</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ko" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇰🇷 Korean</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ru" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇷🇺 Russian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="hi" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇮🇳 Hindi</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="tr" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇹🇷 Turkish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="nl" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇳🇱 Dutch</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="pl" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇵🇱 Polish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="sv" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇸🇪 Swedish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="no" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇳🇴 Norwegian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="da" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇩🇰 Danish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="fi" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇫🇮 Finnish</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="cs" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇨🇿 Czech</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="el" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇬🇷 Greek</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="th" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇹🇭 Thai</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="vi" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇻🇳 Vietnamese</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="id" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇮🇩 Indonesian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ms" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇲🇾 Malay</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="he" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇮🇱 Hebrew</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="uk" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇺🇦 Ukrainian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ro" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇷🇴 Romanian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="hu" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇭🇺 Hungarian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="bg" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇧🇬 Bulgarian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="sw" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇹🇿 Swahili</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="bn" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇧🇩 Bengali</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="fa" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇮🇷 Persian</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-3 p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition border border-white/20">
                                <input type="checkbox" name="landing_page_languages[]" value="ur" class="rounded bg-white/10 border-white/30 text-indigo-500 focus:ring-indigo-500 w-5 h-5" onchange="updateLanguagePreview()" />
                                <div class="flex-1 text-sm">
                                    <div class="text-white font-medium">🇵🇰 Urdu</div>
                                </div>
                            </label>
                        </div>
                        @error('landing_page_languages')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        
                        <!-- Language Preview -->
                        <div class="mt-4 p-4 bg-indigo-500/20 border border-indigo-400/50 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-indigo-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm text-indigo-100 flex-1">
                                    <p class="font-semibold mb-2">Selected Languages (will appear as tabs on landing page):</p>
                                    <div id="languageTabsPreview" class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1.5 bg-indigo-500 text-white rounded-lg text-xs font-medium">🇫🇷 French</span>
                                    </div>
                                    <p class="text-xs text-indigo-200 mt-3">✨ AI will generate landing page content in the selected languages</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Information Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Product Information
                </h3>
                
                <div class="space-y-4">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Enter product name"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Description (for hero section) -->
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-300 mb-2">Short Description (Hero Section)</label>
                        <input 
                            type="text" 
                            id="short_description" 
                            name="theme_data[short_description]" 
                            value="{{ old('theme_data.short_description') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="e.g., The Ultimate Cleaning Solution for Your Home"
                        />
                    </div>

                    <!-- Full Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Full Description</label>
                        <div id="description-editor" class="bg-white rounded-lg" style="min-height: 150px;"></div>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="hidden"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Compare Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-300 mb-2">
                                Sale Price (<span id="priceCurrencyLabel">MAD</span>) *
                            </label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                step="0.01"
                                min="0"
                                required
                                value="{{ old('price') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="299.00"
                            />
                            @error('price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="compare_at_price" class="block text-sm font-medium text-gray-300 mb-2">
                                Original Price (<span id="comparePriceCurrencyLabel">MAD</span>) <span class="text-yellow-400">- shows as crossed out</span>
                            </label>
                            <input 
                                type="number" 
                                id="compare_at_price" 
                                name="compare_at_price" 
                                step="0.01"
                                min="0"
                                value="{{ old('compare_at_price') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="599.00"
                            />
                            @error('compare_at_price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                        <select 
                            id="category_id" 
                            name="category_id"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        >
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock and SKU -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-300 mb-2">Stock</label>
                            <input 
                                type="number" 
                                id="stock" 
                                name="stock" 
                                min="0"
                                value="{{ old('stock', 0) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="0"
                            />
                            @error('stock')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
                            <input 
                                type="text" 
                                id="sku" 
                                name="sku" 
                                value="{{ old('sku') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                placeholder="Enter SKU"
                            />
                            @error('sku')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Variations Toggle -->
                    <div class="border-t border-white/10 pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="has_variations" class="block text-sm font-medium text-gray-300">Product has variations</label>
                                <p class="text-xs text-gray-500 mt-1">Enable if this product comes in different sizes, colors, or other options</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="has_variations" value="0">
                                <input 
                                    type="checkbox" 
                                    id="has_variations" 
                                    name="has_variations" 
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('has_variations') ? 'checked' : '' }}
                                    onchange="toggleVariations(this.checked)"
                                />
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Variations Card -->
            <div id="variationsCard" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hidden">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Product Variations</h3>
                        <p class="text-xs text-gray-500 mt-1">Add different options for this product (e.g., sizes, colors)</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addVariation()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Variation
                    </button>
                </div>

                <div id="variationsContainer" class="space-y-4">
                </div>

                <div id="noVariationsMessage" class="text-center py-8 text-gray-500 text-sm">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Click "Add Variation" to create your first product variation
                </div>
            </div>

            <!-- Quantity-Based Promotions Card -->
            <div id="promotionsCard" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Quantity-Based Pricing
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Set special prices when customers buy multiple items</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="has_promotions" value="0">
                            <input 
                                type="checkbox" 
                                id="has_promotions" 
                                name="has_promotions" 
                                value="1"
                                class="sr-only peer"
                                onchange="togglePromotions(this.checked)"
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                        </label>
                    </div>
                </div>

                <div id="promotionsContent" class="hidden">
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-300">
                                <p class="font-semibold mb-1">How it works:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Set different prices based on quantity purchased</li>
                                    <li>Example: Buy 1 for 100 MAD, Buy 2 for 90 MAD each, Buy 3+ for 80 MAD each</li>
                                    <li>Promotions apply automatically at checkout</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-300">Pricing Tiers</h4>
                        <button 
                            type="button" 
                            onclick="addPromotion()"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Tier
                        </button>
                    </div>

                    <div id="promotionsContainer" class="space-y-3">
                    </div>
                    
                    <input type="hidden" id="promotions_json" name="promotions_json" value="[]">

                    <div id="noPromotionsMessage" class="text-center py-8 border-2 border-dashed border-yellow-500/30 rounded-lg bg-yellow-500/5">
                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-yellow-300 font-semibold mb-1">No pricing tiers added yet!</p>
                        <p class="text-gray-400 text-sm">Click <strong class="text-yellow-400">"Add Tier"</strong> above to create quantity-based pricing</p>
                        <p class="text-xs mt-2 text-gray-500">Example: Buy 2+ items = Pay 90 MAD each instead of 100 MAD</p>
                    </div>
                </div>
            </div>

            <!-- Header Marquee Items -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-yellow-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Header Scrolling Banner
                        </h3>
                        <p class="text-sm text-gray-400 mt-1">Customize the scrolling promotional items at the top of your landing page</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addHeaderItem()"
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Item
                    </button>
                </div>

                <!-- Preview of header -->
                <div class="mb-4 p-3 bg-black rounded-lg overflow-hidden">
                    <div class="flex items-center gap-4 text-white text-xs font-bold whitespace-nowrap animate-pulse">
                        <span class="text-yellow-400">Preview:</span>
                        <div id="headerPreview" class="flex items-center gap-3">
                            <span>🔥 -50% OFF TODAY</span>
                            <span class="text-gray-500">•</span>
                            <span>🚚 Livraison gratuite</span>
                            <span class="text-gray-500">•</span>
                            <span>💵 Paiement à la livraison</span>
                        </div>
                    </div>
                </div>

                <div id="headerItemsContainer" class="space-y-3">
                    <!-- Header Item 1 -->
                    <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <div class="flex-shrink-0">
                            <select name="theme_data[header_items][0][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                <option value="🔥">🔥</option>
                                <option value="💰">💰</option>
                                <option value="🚚">🚚</option>
                                <option value="💵">💵</option>
                                <option value="⚡">⚡</option>
                                <option value="✨">✨</option>
                                <option value="🎁">🎁</option>
                                <option value="⭐">⭐</option>
                                <option value="🛡️">🛡️</option>
                                <option value="✅">✅</option>
                                <option value="📦">📦</option>
                                <option value="🏷️">🏷️</option>
                            </select>
                        </div>
                        <input 
                            type="text" 
                            name="theme_data[header_items][0][text]" 
                            value="{{ old('theme_data.header_items.0.text', '-50% OFF TODAY') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Enter header text (e.g., -50% OFF TODAY)"
                            onkeyup="updateHeaderPreview()"
                        />
                        <button type="button" onclick="removeHeaderItem(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Header Item 2 -->
                    <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <div class="flex-shrink-0">
                            <select name="theme_data[header_items][1][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                <option value="🔥">🔥</option>
                                <option value="💰">💰</option>
                                <option value="🚚" selected>🚚</option>
                                <option value="💵">💵</option>
                                <option value="⚡">⚡</option>
                                <option value="✨">✨</option>
                                <option value="🎁">🎁</option>
                                <option value="⭐">⭐</option>
                                <option value="🛡️">🛡️</option>
                                <option value="✅">✅</option>
                                <option value="📦">📦</option>
                                <option value="🏷️">🏷️</option>
                            </select>
                        </div>
                        <input 
                            type="text" 
                            name="theme_data[header_items][1][text]" 
                            value="{{ old('theme_data.header_items.1.text', 'Livraison gratuite') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Enter header text"
                            onkeyup="updateHeaderPreview()"
                        />
                        <button type="button" onclick="removeHeaderItem(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Header Item 3 -->
                    <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <div class="flex-shrink-0">
                            <select name="theme_data[header_items][2][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                <option value="🔥">🔥</option>
                                <option value="💰">💰</option>
                                <option value="🚚">🚚</option>
                                <option value="💵" selected>💵</option>
                                <option value="⚡">⚡</option>
                                <option value="✨">✨</option>
                                <option value="🎁">🎁</option>
                                <option value="⭐">⭐</option>
                                <option value="🛡️">🛡️</option>
                                <option value="✅">✅</option>
                                <option value="📦">📦</option>
                                <option value="🏷️">🏷️</option>
                            </select>
                        </div>
                        <input 
                            type="text" 
                            name="theme_data[header_items][2][text]" 
                            value="{{ old('theme_data.header_items.2.text', 'Paiement à la livraison') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Enter header text"
                            onkeyup="updateHeaderPreview()"
                        />
                        <button type="button" onclick="removeHeaderItem(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Header Item 4 -->
                    <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <div class="flex-shrink-0">
                            <select name="theme_data[header_items][3][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                <option value="🔥">🔥</option>
                                <option value="💰">💰</option>
                                <option value="🚚">🚚</option>
                                <option value="💵">💵</option>
                                <option value="⚡" selected>⚡</option>
                                <option value="✨">✨</option>
                                <option value="🎁">🎁</option>
                                <option value="⭐">⭐</option>
                                <option value="🛡️">🛡️</option>
                                <option value="✅">✅</option>
                                <option value="📦">📦</option>
                                <option value="🏷️">🏷️</option>
                            </select>
                        </div>
                        <input 
                            type="text" 
                            name="theme_data[header_items][3][text]" 
                            value="{{ old('theme_data.header_items.3.text', 'HIFADHI NDOGO') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Enter header text"
                            onkeyup="updateHeaderPreview()"
                        />
                        <button type="button" onclick="removeHeaderItem(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-4">
                    <span class="text-yellow-400">Tip:</span> These items will scroll continuously at the top of your landing page. Add promotional messages, trust badges, or special offers.
                </p>
            </div>

            <!-- Hero Section Customization -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Hero Section & Promotion
                </h3>

                <div class="space-y-4">
                    <!-- Promo Badge -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="promo_badge" class="block text-sm font-medium text-gray-300 mb-2">Promo Badge Text</label>
                            <input 
                                type="text" 
                                id="promo_badge" 
                                name="theme_data[promo_badge]" 
                                value="{{ old('theme_data.promo_badge', '-50% OFF TODAY') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="-50% OFF TODAY"
                            />
                        </div>
                        <div>
                            <label for="promo_badge_color" class="block text-sm font-medium text-gray-300 mb-2">Badge Color</label>
                            <select 
                                id="promo_badge_color" 
                                name="theme_data[promo_badge_color]"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            >
                                <option value="red" {{ old('theme_data.promo_badge_color', 'red') == 'red' ? 'selected' : '' }}>Red</option>
                                <option value="orange" {{ old('theme_data.promo_badge_color') == 'orange' ? 'selected' : '' }}>Orange</option>
                                <option value="green" {{ old('theme_data.promo_badge_color') == 'green' ? 'selected' : '' }}>Green</option>
                                <option value="blue" {{ old('theme_data.promo_badge_color') == 'blue' ? 'selected' : '' }}>Blue</option>
                                <option value="purple" {{ old('theme_data.promo_badge_color') == 'purple' ? 'selected' : '' }}>Purple</option>
                            </select>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="cta_text" class="block text-sm font-medium text-gray-300 mb-2">CTA Button Text</label>
                            <input 
                                type="text" 
                                id="cta_text" 
                                name="theme_data[cta_text]" 
                                value="{{ old('theme_data.cta_text', 'ORDER NOW') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                placeholder="ORDER NOW"
                            />
                        </div>
                        <div>
                            <label for="cta_color" class="block text-sm font-medium text-gray-300 mb-2">CTA Button Color</label>
                            <select 
                                id="cta_color" 
                                name="theme_data[cta_color]"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            >
                                <option value="orange" {{ old('theme_data.cta_color', 'orange') == 'orange' ? 'selected' : '' }}>Orange</option>
                                <option value="green" {{ old('theme_data.cta_color') == 'green' ? 'selected' : '' }}>Green</option>
                                <option value="red" {{ old('theme_data.cta_color') == 'red' ? 'selected' : '' }}>Red</option>
                                <option value="blue" {{ old('theme_data.cta_color') == 'blue' ? 'selected' : '' }}>Blue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Title Styling -->
                    <div class="border-t border-white/10 pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                            </svg>
                            Title Styling
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="title_color" class="block text-sm font-medium text-gray-300 mb-2">Title Color</label>
                                <div class="flex gap-2">
                                    <input 
                                        type="color" 
                                        id="title_color_picker" 
                                        value="{{ old('theme_data.title_color', '#ffffff') }}"
                                        onchange="document.getElementById('title_color').value = this.value; updateTitlePreview()"
                                        class="w-12 h-12 rounded-lg cursor-pointer border border-white/10 bg-transparent"
                                    />
                                    <input 
                                        type="text" 
                                        id="title_color" 
                                        name="theme_data[title_color]" 
                                        value="{{ old('theme_data.title_color', '#ffffff') }}"
                                        onchange="document.getElementById('title_color_picker').value = this.value; updateTitlePreview()"
                                        class="flex-1 px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent uppercase"
                                        placeholder="#ffffff"
                                        pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                    />
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Choose the color for your product title</p>
                            </div>
                            <div>
                                <label for="title_font" class="block text-sm font-medium text-gray-300 mb-2">Title Font</label>
                                <select 
                                    id="title_font" 
                                    name="theme_data[title_font]"
                                    onchange="updateTitlePreview()"
                                    class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                >
                                    <option value="bebas" {{ old('theme_data.title_font', 'bebas') == 'bebas' ? 'selected' : '' }}>Bebas Neue (Bold Display)</option>
                                    <option value="inter" {{ old('theme_data.title_font') == 'inter' ? 'selected' : '' }}>Inter (Modern Sans)</option>
                                    <option value="cairo" {{ old('theme_data.title_font') == 'cairo' ? 'selected' : '' }}>Cairo (Arabic-friendly)</option>
                                    <option value="oswald" {{ old('theme_data.title_font') == 'oswald' ? 'selected' : '' }}>Oswald (Condensed)</option>
                                    <option value="montserrat" {{ old('theme_data.title_font') == 'montserrat' ? 'selected' : '' }}>Montserrat (Geometric)</option>
                                    <option value="playfair" {{ old('theme_data.title_font') == 'playfair' ? 'selected' : '' }}>Playfair Display (Elegant)</option>
                                    <option value="roboto" {{ old('theme_data.title_font') == 'roboto' ? 'selected' : '' }}>Roboto (Clean)</option>
                                    <option value="poppins" {{ old('theme_data.title_font') == 'poppins' ? 'selected' : '' }}>Poppins (Friendly)</option>
                                    <option value="anton" {{ old('theme_data.title_font') == 'anton' ? 'selected' : '' }}>Anton (Impact)</option>
                                    <option value="raleway" {{ old('theme_data.title_font') == 'raleway' ? 'selected' : '' }}>Raleway (Elegant Sans)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Choose the font style for your title</p>
                            </div>
                        </div>
                        
                        <!-- Title Background Color -->
                        <div class="mt-4">
                            <label for="title_background_color" class="block text-sm font-medium text-gray-300 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                    Title Background Color <span class="text-gray-500 font-normal">(optional)</span>
                                </span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    type="color" 
                                    id="title_background_color_picker" 
                                    value="{{ old('theme_data.title_background_color', '#000000') }}"
                                    onchange="document.getElementById('title_background_color').value = this.value; updateTitlePreview()"
                                    class="w-12 h-12 rounded-lg cursor-pointer border border-white/10 bg-transparent"
                                />
                                <input 
                                    type="text" 
                                    id="title_background_color" 
                                    name="theme_data[title_background_color]" 
                                    value="{{ old('theme_data.title_background_color', '') }}"
                                    onchange="document.getElementById('title_background_color_picker').value = this.value || '#000000'; updateTitlePreview()"
                                    class="flex-1 px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase"
                                    placeholder="#000000 (leave empty for no background)"
                                    pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                />
                                <button 
                                    type="button" 
                                    onclick="document.getElementById('title_background_color').value = ''; updateTitlePreview();"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition text-sm"
                                >
                                    Clear
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Add a background color behind your title and product name. Leave empty for no background.</p>
                        </div>
                        
                        <!-- Title Preview -->
                        <div class="mt-4 p-4 bg-gradient-to-r from-red-500 via-red-600 to-red-700 rounded-lg">
                            <p class="text-xs text-white/70 mb-2">Preview:</p>
                            <h2 id="titlePreview" class="text-3xl font-black uppercase inline-block px-3 py-1 rounded" style="color: {{ old('theme_data.title_color', '#ffffff') }}; font-family: 'Bebas Neue', sans-serif;">
                                YOUR PRODUCT TITLE
                            </h2>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="border-t border-white/10 pt-4 mt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Trust Badges
                            </h4>
                            <button 
                                type="button" 
                                onclick="addTrustBadge()"
                                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition flex items-center gap-1 text-xs"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Customize the trust badges displayed below the order form</p>

                        <!-- Preview of trust badges -->
                        <div class="mb-3 p-3 bg-white rounded-lg">
                            <p class="text-xs text-gray-500 mb-2">Preview:</p>
                            <div id="trustBadgesPreview" class="flex flex-wrap gap-2">
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>🚚</span>
                                    <span>Free Shipping</span>
                                </div>
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>💰</span>
                                    <span>Money Back Guarantee</span>
                                </div>
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>🔒</span>
                                    <span>Secure Payment</span>
                                </div>
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>✅</span>
                                    <span>1 Year Warranty</span>
                                </div>
                            </div>
                        </div>

                        <div id="trustBadgesContainer" class="space-y-2">
                            <div class="trust-badge-item flex gap-2 items-center bg-[#0a1628] rounded-lg p-2 border border-white/5">
                                <div class="flex-shrink-0">
                                    <select name="theme_data[trust_badges][0][emoji]" class="w-14 px-1 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                                        <option value="🚚" selected>🚚</option>
                                        <option value="💰">💰</option>
                                        <option value="🔒">🔒</option>
                                        <option value="✅">✅</option>
                                        <option value="💵">💵</option>
                                        <option value="⚡">⚡</option>
                                        <option value="🛡️">🛡️</option>
                                        <option value="⭐">⭐</option>
                                        <option value="🎁">🎁</option>
                                        <option value="📦">📦</option>
                                        <option value="🏷️">🏷️</option>
                                        <option value="✨">✨</option>
                                        <option value="💎">💎</option>
                                        <option value="🔥">🔥</option>
                                        <option value="💯">💯</option>
                                        <option value="🏆">🏆</option>
                                    </select>
                                </div>
                                <input 
                                    type="text" 
                                    name="theme_data[trust_badges][0][text]" 
                                    value="{{ old('theme_data.trust_badges.0.text', 'Free Shipping') }}"
                                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                    placeholder="Enter badge text"
                                    onkeyup="updateTrustBadgesPreview()"
                                />
                                <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="trust-badge-item flex gap-2 items-center bg-[#0a1628] rounded-lg p-2 border border-white/5">
                                <div class="flex-shrink-0">
                                    <select name="theme_data[trust_badges][1][emoji]" class="w-14 px-1 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                                        <option value="🚚">🚚</option>
                                        <option value="💰" selected>💰</option>
                                        <option value="🔒">🔒</option>
                                        <option value="✅">✅</option>
                                        <option value="💵">💵</option>
                                        <option value="⚡">⚡</option>
                                        <option value="🛡️">🛡️</option>
                                        <option value="⭐">⭐</option>
                                        <option value="🎁">🎁</option>
                                        <option value="📦">📦</option>
                                        <option value="🏷️">🏷️</option>
                                        <option value="✨">✨</option>
                                        <option value="💎">💎</option>
                                        <option value="🔥">🔥</option>
                                        <option value="💯">💯</option>
                                        <option value="🏆">🏆</option>
                                    </select>
                                </div>
                                <input 
                                    type="text" 
                                    name="theme_data[trust_badges][1][text]" 
                                    value="{{ old('theme_data.trust_badges.1.text', 'Money Back Guarantee') }}"
                                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                    placeholder="Enter badge text"
                                    onkeyup="updateTrustBadgesPreview()"
                                />
                                <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="trust-badge-item flex gap-2 items-center bg-[#0a1628] rounded-lg p-2 border border-white/5">
                                <div class="flex-shrink-0">
                                    <select name="theme_data[trust_badges][2][emoji]" class="w-14 px-1 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                                        <option value="🚚">🚚</option>
                                        <option value="💰">💰</option>
                                        <option value="🔒" selected>🔒</option>
                                        <option value="✅">✅</option>
                                        <option value="💵">💵</option>
                                        <option value="⚡">⚡</option>
                                        <option value="🛡️">🛡️</option>
                                        <option value="⭐">⭐</option>
                                        <option value="🎁">🎁</option>
                                        <option value="📦">📦</option>
                                        <option value="🏷️">🏷️</option>
                                        <option value="✨">✨</option>
                                        <option value="💎">💎</option>
                                        <option value="🔥">🔥</option>
                                        <option value="💯">💯</option>
                                        <option value="🏆">🏆</option>
                                    </select>
                                </div>
                                <input 
                                    type="text" 
                                    name="theme_data[trust_badges][2][text]" 
                                    value="{{ old('theme_data.trust_badges.2.text', 'Secure Payment') }}"
                                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                    placeholder="Enter badge text"
                                    onkeyup="updateTrustBadgesPreview()"
                                />
                                <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="trust-badge-item flex gap-2 items-center bg-[#0a1628] rounded-lg p-2 border border-white/5">
                                <div class="flex-shrink-0">
                                    <select name="theme_data[trust_badges][3][emoji]" class="w-14 px-1 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                                        <option value="🚚">🚚</option>
                                        <option value="💰">💰</option>
                                        <option value="🔒">🔒</option>
                                        <option value="✅" selected>✅</option>
                                        <option value="💵">💵</option>
                                        <option value="⚡">⚡</option>
                                        <option value="🛡️">🛡️</option>
                                        <option value="⭐">⭐</option>
                                        <option value="🎁">🎁</option>
                                        <option value="📦">📦</option>
                                        <option value="🏷️">🏷️</option>
                                        <option value="✨">✨</option>
                                        <option value="💎">💎</option>
                                        <option value="🔥">🔥</option>
                                        <option value="💯">💯</option>
                                        <option value="🏆">🏆</option>
                                    </select>
                                </div>
                                <input 
                                    type="text" 
                                    name="theme_data[trust_badges][3][text]" 
                                    value="{{ old('theme_data.trust_badges.3.text', '1 Year Warranty') }}"
                                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                    placeholder="Enter badge text"
                                    onkeyup="updateTrustBadgesPreview()"
                                />
                                <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-2">
                            <span class="text-emerald-400">Tip:</span> Trust badges help increase conversions by showing customers your product guarantees.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Social Proof / Stats Section -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Social Proof & Statistics
                </h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Happy Customers</label>
                        <input 
                            type="text" 
                            name="theme_data[stats_customers]" 
                            value="{{ old('theme_data.stats_customers', '325') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="325"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Rating (e.g., 4.8)</label>
                        <input 
                            type="text" 
                            name="theme_data[stats_rating]" 
                            value="{{ old('theme_data.stats_rating', '4.8') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="4.8"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Reviews Count</label>
                        <input 
                            type="text" 
                            name="theme_data[stats_reviews]" 
                            value="{{ old('theme_data.stats_reviews', '127') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="127"
                        />
                    </div>
                </div>
            </div>

            <!-- Product Features -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Product Features
                    </h3>
                    <button 
                        type="button" 
                        onclick="addFeature()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Feature
                    </button>
                </div>

                <div id="featuresContainer" class="space-y-3">
                    <!-- Feature 1 -->
                    <div class="feature-item flex gap-3 items-start bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <select name="theme_data[features][0][icon]" class="w-24 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm">
                            <option value="steam">🔥 Steam</option>
                            <option value="clean">✨ Clean</option>
                            <option value="fast">⚡ Fast</option>
                            <option value="eco">🌿 Eco</option>
                            <option value="power">💪 Power</option>
                            <option value="safe">🛡️ Safe</option>
                            <option value="timer">⏱️ Timer</option>
                            <option value="warranty">📋 Warranty</option>
                        </select>
                        <input 
                            type="text" 
                            name="theme_data[features][0][text]" 
                            value="{{ old('theme_data.features.0.text', 'Chemical-free deep cleaning') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Feature description"
                        />
                        <button type="button" onclick="removeFeature(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Feature 2 -->
                    <div class="feature-item flex gap-3 items-start bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <select name="theme_data[features][1][icon]" class="w-24 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm">
                            <option value="steam">🔥 Steam</option>
                            <option value="clean">✨ Clean</option>
                            <option value="fast" selected>⚡ Fast</option>
                            <option value="eco">🌿 Eco</option>
                            <option value="power">💪 Power</option>
                            <option value="safe">🛡️ Safe</option>
                            <option value="timer">⏱️ Timer</option>
                            <option value="warranty">📋 Warranty</option>
                        </select>
                        <input 
                            type="text" 
                            name="theme_data[features][1][text]" 
                            value="{{ old('theme_data.features.1.text', 'Ready in 30 seconds') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Feature description"
                        />
                        <button type="button" onclick="removeFeature(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Feature 3 -->
                    <div class="feature-item flex gap-3 items-start bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <select name="theme_data[features][2][icon]" class="w-24 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm">
                            <option value="steam">🔥 Steam</option>
                            <option value="clean">✨ Clean</option>
                            <option value="fast">⚡ Fast</option>
                            <option value="eco" selected>🌿 Eco</option>
                            <option value="power">💪 Power</option>
                            <option value="safe">🛡️ Safe</option>
                            <option value="timer">⏱️ Timer</option>
                            <option value="warranty">📋 Warranty</option>
                        </select>
                        <input 
                            type="text" 
                            name="theme_data[features][2][text]" 
                            value="{{ old('theme_data.features.2.text', 'Eco-friendly & safe for family') }}"
                            class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                            placeholder="Feature description"
                        />
                        <button type="button" onclick="removeFeature(this)" class="text-red-400 hover:text-red-300 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Trust Badges
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="free_shipping" checked class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">🚚 Free Shipping</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="money_back" checked class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">💰 Money Back Guarantee</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="secure_payment" checked class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">🔒 Secure Payment</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="warranty" checked class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">✅ 1 Year Warranty</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="cod" class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">💵 Cash on Delivery</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-[#0a1628] rounded-lg cursor-pointer hover:bg-[#0a1628]/80 transition">
                        <input type="checkbox" name="theme_data[badges][]" value="fast_delivery" class="rounded bg-[#0f1c2e] border-white/20 text-yellow-500 focus:ring-yellow-500">
                        <span class="text-white text-sm">⚡ Fast Delivery (24-48h)</span>
                    </label>
                </div>
            </div>

            <!-- Product Images Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Product Images
                </h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Images (Multiple)</label>
                    <p class="text-xs text-gray-500 mb-3">First image will be the main hero image. Additional images will appear in the gallery.</p>
                    <div class="border-2 border-dashed border-white/10 rounded-lg p-8 text-center hover:border-cyan-500/50 transition">
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            multiple
                            accept="image/*"
                            class="hidden"
                            onchange="previewImages(event)"
                        />
                        <label for="images" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-400 mb-1">Click to upload images</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        </label>
                    </div>
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                    <input type="hidden" id="mainImageIndex" name="main_image_index" value="0">
                    @error('images')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content Sections (with images) -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Content Sections
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Add image-based sections for your landing page</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addSection()"
                        class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Section
                    </button>
                </div>

                <div id="sectionsContainer" class="space-y-4">
                    <div class="text-center py-8 text-gray-500 text-sm" id="noSectionsMessage">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Click "Add Section" to create content sections with images
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Settings</h3>
                
                <div class="space-y-4">
                    <!-- AI Landing Page Generation Toggle -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label for="generate_landing_page" class="block text-sm font-medium text-gray-300">AI Landing Page</label>
                            <p class="text-xs text-gray-500 mt-1">Automatically generate a professional landing page using AI</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="generate_landing_page" 
                                name="generate_landing_page" 
                                class="sr-only peer"
                                {{ old('generate_landing_page') ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-blue-600"></div>
                        </label>
                    </div>

                    <!-- AI Product Images Generation Toggle -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label for="generate_product_images" class="block text-sm font-medium text-gray-300">AI Product Images</label>
                            <p class="text-xs text-gray-500 mt-1">Generate 5 realistic product images using AI (requires at least 1 uploaded image)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="generate_product_images" 
                                name="generate_product_images" 
                                class="sr-only peer"
                                {{ old('generate_product_images') ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-orange-600 peer-checked:to-yellow-600"></div>
                        </label>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-300">Active</label>
                            <p class="text-xs text-gray-500 mt-1">Make this product visible on your website</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                class="sr-only peer"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                        </label>
                    </div>

                    <!-- Featured Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="is_featured" class="block text-sm font-medium text-gray-300">Featured</label>
                            <p class="text-xs text-gray-500 mt-1">Show this product in featured section</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="is_featured" 
                                name="is_featured" 
                                class="sr-only peer"
                                {{ old('is_featured') ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let featureCounter = 3;
        let sectionCounter = 0;
        let headerItemCounter = 4;
        let trustBadgeCounter = 4;
        let variationCounter = 0;
        let promotionCounter = 0;
        let currentCurrency = 'MAD';

        function addHeaderItem() {
            const container = document.getElementById('headerItemsContainer');
            const headerDiv = document.createElement('div');
            headerDiv.className = 'header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5';
            headerDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <select name="theme_data[header_items][${headerItemCounter}][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                        <option value="🔥">🔥</option>
                        <option value="💰">💰</option>
                        <option value="🚚">🚚</option>
                        <option value="💵">💵</option>
                        <option value="⚡">⚡</option>
                        <option value="✨">✨</option>
                        <option value="🎁">🎁</option>
                        <option value="⭐">⭐</option>
                        <option value="🛡️">🛡️</option>
                        <option value="✅">✅</option>
                        <option value="📦">📦</option>
                        <option value="🏷️">🏷️</option>
                    </select>
                </div>
                <input 
                    type="text" 
                    name="theme_data[header_items][${headerItemCounter}][text]" 
                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                    placeholder="Enter header text"
                    onkeyup="updateHeaderPreview()"
                />
                <button type="button" onclick="removeHeaderItem(this)" class="text-red-400 hover:text-red-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(headerDiv);
            headerItemCounter++;
            updateHeaderPreview();
        }

        function removeHeaderItem(btn) {
            btn.closest('.header-item').remove();
            updateHeaderPreview();
        }

        function updateHeaderPreview() {
            const items = document.querySelectorAll('#headerItemsContainer .header-item');
            const preview = document.getElementById('headerPreview');
            let previewHtml = '';
            
            items.forEach((item, index) => {
                const emoji = item.querySelector('select').value;
                const text = item.querySelector('input[type="text"]').value || 'Enter text...';
                
                if (index > 0) {
                    previewHtml += '<span class="text-gray-500">•</span>';
                }
                previewHtml += `<span>${emoji} ${text}</span>`;
            });
            
            preview.innerHTML = previewHtml || '<span class="text-gray-500">No items added</span>';
        }

        // Trust Badges Functions
        function addTrustBadge() {
            const container = document.getElementById('trustBadgesContainer');
            if (!container) return;
            
            const badgeDiv = document.createElement('div');
            badgeDiv.className = 'trust-badge-item flex gap-2 items-center bg-[#0a1628] rounded-lg p-2 border border-white/5';
            badgeDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <select name="theme_data[trust_badges][${trustBadgeCounter}][emoji]" class="w-14 px-1 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                        <option value="🚚">🚚</option>
                        <option value="💰">💰</option>
                        <option value="🔒">🔒</option>
                        <option value="✅">✅</option>
                        <option value="💵">💵</option>
                        <option value="⚡">⚡</option>
                        <option value="🛡️">🛡️</option>
                        <option value="⭐">⭐</option>
                        <option value="🎁">🎁</option>
                        <option value="📦">📦</option>
                        <option value="🏷️">🏷️</option>
                        <option value="✨">✨</option>
                        <option value="💎">💎</option>
                        <option value="🔥">🔥</option>
                        <option value="💯">💯</option>
                        <option value="🏆">🏆</option>
                    </select>
                </div>
                <input 
                    type="text" 
                    name="theme_data[trust_badges][${trustBadgeCounter}][text]" 
                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                    placeholder="Enter badge text"
                    onkeyup="updateTrustBadgesPreview()"
                />
                <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(badgeDiv);
            trustBadgeCounter++;
            updateTrustBadgesPreview();
        }

        function removeTrustBadge(btn) {
            btn.closest('.trust-badge-item').remove();
            updateTrustBadgesPreview();
        }

        function updateTrustBadgesPreview() {
            const items = document.querySelectorAll('#trustBadgesContainer .trust-badge-item');
            const preview = document.getElementById('trustBadgesPreview');
            if (!preview) return;
            
            let previewHtml = '';
            
            items.forEach((item) => {
                const emoji = item.querySelector('select').value;
                const text = item.querySelector('input[type="text"]').value || 'Enter text...';
                
                previewHtml += `<div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                    <span>${emoji}</span>
                    <span>${text}</span>
                </div>`;
            });
            
            preview.innerHTML = previewHtml || '<span class="text-gray-500">No badges added</span>';
        }

        // Language name mapping for preview
        const languageNames = {
            'en': { flag: '🇬🇧', name: 'English' },
            'fr': { flag: '🇫🇷', name: 'French' },
            'ar': { flag: '🇸🇦', name: 'Arabic' },
            'es': { flag: '🇪🇸', name: 'Spanish' },
            'de': { flag: '🇩🇪', name: 'German' },
            'it': { flag: '🇮🇹', name: 'Italian' },
            'pt': { flag: '🇵🇹', name: 'Portuguese' },
            'zh': { flag: '🇨🇳', name: 'Chinese' },
            'ja': { flag: '🇯🇵', name: 'Japanese' },
            'ko': { flag: '🇰🇷', name: 'Korean' },
            'ru': { flag: '🇷🇺', name: 'Russian' },
            'hi': { flag: '🇮🇳', name: 'Hindi' },
            'tr': { flag: '🇹🇷', name: 'Turkish' },
            'nl': { flag: '🇳🇱', name: 'Dutch' },
            'pl': { flag: '🇵🇱', name: 'Polish' },
            'sv': { flag: '🇸🇪', name: 'Swedish' },
            'no': { flag: '🇳🇴', name: 'Norwegian' },
            'da': { flag: '🇩🇰', name: 'Danish' },
            'fi': { flag: '🇫🇮', name: 'Finnish' },
            'cs': { flag: '🇨🇿', name: 'Czech' },
            'el': { flag: '🇬🇷', name: 'Greek' },
            'th': { flag: '🇹🇭', name: 'Thai' },
            'vi': { flag: '🇻🇳', name: 'Vietnamese' },
            'id': { flag: '🇮🇩', name: 'Indonesian' },
            'ms': { flag: '🇲🇾', name: 'Malay' },
            'he': { flag: '🇮🇱', name: 'Hebrew' },
            'uk': { flag: '🇺🇦', name: 'Ukrainian' },
            'ro': { flag: '🇷🇴', name: 'Romanian' },
            'hu': { flag: '🇭🇺', name: 'Hungarian' },
            'bg': { flag: '🇧🇬', name: 'Bulgarian' },
            'sw': { flag: '🇹🇿', name: 'Swahili' },
            'bn': { flag: '🇧🇩', name: 'Bengali' },
            'fa': { flag: '🇮🇷', name: 'Persian' },
            'ur': { flag: '🇵🇰', name: 'Urdu' }
        };

        function updateCurrencyDisplay() {
            const select = document.getElementById('landing_page_currency');
            const selectedOption = select.options[select.selectedIndex];
            const currencyCode = selectedOption.value;
            const currencyText = selectedOption.text;
            
            currentCurrency = currencyCode;
            
            // Update the currency display text
            document.getElementById('selectedCurrencyDisplay').textContent = currencyText;
            
            // Update price labels
            document.getElementById('priceCurrencyLabel').textContent = currencyCode;
            document.getElementById('comparePriceCurrencyLabel').textContent = currencyCode;
            
            // Update all variation price labels
            document.querySelectorAll('.variation-price-label').forEach(label => {
                label.textContent = `Price (${currencyCode}) *`;
            });
            document.querySelectorAll('.variation-compare-price-label').forEach(label => {
                label.textContent = `Compare at Price (${currencyCode})`;
            });
            
            // Update all promotion price labels
            document.querySelectorAll('.promotion-price-label').forEach(label => {
                label.textContent = `Price per Unit (${currencyCode}) *`;
            });
            
            // Update promotion example text
            document.querySelectorAll('.promotion-example-text').forEach(el => {
                el.innerHTML = `<strong>Example:</strong> Min: 2, Max: 4, Price: 90.00 = Customers buying 2-4 items pay 90 ${currencyCode} per item`;
            });
        }

        function toggleVariations(enabled) {
            const variationsCard = document.getElementById('variationsCard');
            const priceField = document.getElementById('price');
            const comparePriceField = document.getElementById('compare_at_price');
            const stockField = document.getElementById('stock');
            const basicPriceFields = [priceField, comparePriceField, stockField];
            
            if (enabled) {
                variationsCard.classList.remove('hidden');
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = true;
                        field.value = '';
                        field.classList.add('opacity-50', 'cursor-not-allowed');
                        field.placeholder = 'Set in variations';
                    }
                });
            } else {
                variationsCard.classList.add('hidden');
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = false;
                        field.classList.remove('opacity-50', 'cursor-not-allowed');
                        if (field.id === 'price') field.placeholder = '299.00';
                        else if (field.id === 'compare_at_price') field.placeholder = '599.00';
                        else if (field.id === 'stock') field.placeholder = '0';
                    }
                });
            }
        }

        function addVariation() {
            const container = document.getElementById('variationsContainer');
            const noVariationsMsg = document.getElementById('noVariationsMessage');
            
            if (noVariationsMsg) {
                noVariationsMsg.style.display = 'none';
            }
            
            const variationId = variationCounter++;
            
            const variationDiv = document.createElement('div');
            variationDiv.className = 'border border-blue-500/30 rounded-lg p-4 bg-[#0a1628] relative';
            variationDiv.id = `variation-${variationId}`;
            variationDiv.innerHTML = `
                <button 
                    type="button" 
                    onclick="removeVariation(${variationId})"
                    class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h4 class="text-sm font-semibold text-blue-400 mb-4">Variation ${variationId + 1}</h4>
                
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-medium text-gray-300">Attributes</label>
                            <button 
                                type="button" 
                                onclick="addAttribute(${variationId})"
                                class="text-xs px-2 py-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded transition"
                            >
                                + Add Attribute
                            </button>
                        </div>
                        <div id="attributes-container-${variationId}" class="space-y-2">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1 variation-price-label">Price (${currentCurrency}) *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][price]" 
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="299.00"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1 variation-compare-price-label">Compare at Price (${currentCurrency})</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][compare_at_price]" 
                                step="0.01"
                                min="0"
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="599.00"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Stock *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][stock]" 
                                min="0"
                                value="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="0"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">SKU</label>
                            <input 
                                type="text" 
                                name="variations[${variationId}][sku]" 
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="SKU-${variationId + 1}"
                            />
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_default]" 
                                value="1"
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-xs text-gray-300">Default variation</span>
                        </label>
                        
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_active]" 
                                value="1"
                                checked
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-xs text-gray-300">Active</span>
                        </label>
                    </div>
                </div>
            `;
            
            container.appendChild(variationDiv);
            addAttribute(variationId);
        }

        function addAttribute(variationId) {
            const container = document.getElementById(`attributes-container-${variationId}`);
            const attributeId = container.children.length;
            
            const attributeDiv = document.createElement('div');
            attributeDiv.className = 'flex gap-2 items-center';
            attributeDiv.innerHTML = `
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][name]" 
                    placeholder="Attribute (e.g., Color)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                />
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][value]" 
                    placeholder="Value (e.g., Red)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                />
                <button 
                    type="button" 
                    onclick="this.parentElement.remove()"
                    class="text-red-400 hover:text-red-300 p-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(attributeDiv);
        }

        function removeVariation(variationId) {
            const variationDiv = document.getElementById(`variation-${variationId}`);
            if (variationDiv) {
                variationDiv.remove();
            }
            
            const container = document.getElementById('variationsContainer');
            if (container.children.length === 0) {
                document.getElementById('noVariationsMessage').style.display = 'block';
            }
        }

        function togglePromotions(enabled) {
            const promotionsContent = document.getElementById('promotionsContent');
            if (enabled) {
                promotionsContent.classList.remove('hidden');
            } else {
                promotionsContent.classList.add('hidden');
            }
        }

        function updatePromotionsJson() {
            const container = document.getElementById('promotionsContainer');
            const promotions = [];
            const promotionDivs = container.querySelectorAll('[id^="promotion-"]');
            
            promotionDivs.forEach((div) => {
                const minQty = div.querySelector('input[name*="[min_quantity]"]');
                const maxQty = div.querySelector('input[name*="[max_quantity]"]');
                const price = div.querySelector('input[name*="[price]"]');
                const label = div.querySelector('input[name*="[label]"]');
                
                if (minQty && price) {
                    promotions.push({
                        min_quantity: minQty.value || '',
                        max_quantity: maxQty ? (maxQty.value || null) : null,
                        price: price.value || '',
                        label: label ? (label.value || '') : ''
                    });
                }
            });
            
            document.getElementById('promotions_json').value = JSON.stringify(promotions);
            console.log('Updated promotions JSON:', promotions);
        }

        function addPromotion() {
            const container = document.getElementById('promotionsContainer');
            const noPromotionsMsg = document.getElementById('noPromotionsMessage');
            
            if (noPromotionsMsg) {
                noPromotionsMsg.style.display = 'none';
            }
            
            const promotionId = promotionCounter++;
            
            const promotionDiv = document.createElement('div');
            promotionDiv.className = 'border border-yellow-500/30 rounded-lg p-4 bg-[#0a1628] relative';
            promotionDiv.id = `promotion-${promotionId}`;
            promotionDiv.innerHTML = `
                <button 
                    type="button" 
                    onclick="removePromotion(${promotionId})"
                    class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <!-- Custom Label (Required) -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-yellow-400 mb-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Display Label * <span class="text-gray-500 font-normal">(shown to customers)</span>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="promotions[${promotionId}][label]" 
                        required
                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-yellow-500/50 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="e.g., Buy 2 Get 10% Off, Pack of 3, Family Bundle..."
                        oninput="updatePromotionsJson()"
                    />
                    <p class="text-xs text-gray-500 mt-1">This label will be displayed on the landing page instead of quantity/price details</p>
                </div>
                
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Min Quantity *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][min_quantity]" 
                            min="1"
                            required
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="2"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Max Quantity</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][max_quantity]" 
                            min="1"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Leave empty for unlimited"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1 promotion-price-label">Price per Unit (${currentCurrency}) *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][price]" 
                            step="0.01"
                            min="0"
                            required
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="90.00"
                            oninput="updatePromotionsJson()"
                        />
                    </div>
                </div>
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded p-2 text-xs text-yellow-300 promotion-example-text mt-3">
                    <strong>Example:</strong> Min: 2, Max: 4, Price: 90.00 = Customers buying 2-4 items pay 90 ${currentCurrency} per item
                </div>
            `;
            container.appendChild(promotionDiv);
            updatePromotionsJson();
        }

        function removePromotion(promotionId) {
            const promotionDiv = document.getElementById(`promotion-${promotionId}`);
            if (promotionDiv) {
                promotionDiv.remove();
            }
            
            const container = document.getElementById('promotionsContainer');
            if (container.children.length === 0) {
                document.getElementById('noPromotionsMessage').style.display = 'block';
            }
            updatePromotionsJson();
        }

        function updateLanguagePreview() {
            const checkboxes = document.querySelectorAll('input[name="landing_page_languages[]"]:checked');
            const preview = document.getElementById('languageTabsPreview');
            
            preview.innerHTML = '';
            
            if (checkboxes.length === 0) {
                preview.innerHTML = '<span class="text-gray-400 text-xs italic">No languages selected</span>';
            } else {
                checkboxes.forEach(checkbox => {
                    const lang = checkbox.value;
                    const langInfo = languageNames[lang] || { flag: '🌐', name: lang.toUpperCase() };
                    const span = document.createElement('span');
                    span.className = 'px-3 py-1.5 bg-indigo-500 text-white rounded-lg text-xs font-medium';
                    span.textContent = `${langInfo.flag} ${langInfo.name}`;
                    preview.appendChild(span);
                });
            }
        }

        // Load Google Fonts for title preview
        (function() {
            const link = document.createElement('link');
            link.href = 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cairo:wght@700;900&family=Inter:wght@700;900&family=Oswald:wght@700&family=Montserrat:wght@700;900&family=Playfair+Display:wght@700;900&family=Roboto:wght@700;900&family=Poppins:wght@700;900&family=Anton&family=Raleway:wght@700;900&display=swap';
            link.rel = 'stylesheet';
            document.head.appendChild(link);
        })();

        // Font mappings for title preview
        const fontFamilies = {
            'bebas': "'Bebas Neue', sans-serif",
            'inter': "'Inter', sans-serif",
            'cairo': "'Cairo', sans-serif",
            'oswald': "'Oswald', sans-serif",
            'montserrat': "'Montserrat', sans-serif",
            'playfair': "'Playfair Display', serif",
            'roboto': "'Roboto', sans-serif",
            'poppins': "'Poppins', sans-serif",
            'anton': "'Anton', sans-serif",
            'raleway': "'Raleway', sans-serif"
        };

        function updateTitlePreview() {
            const titlePreview = document.getElementById('titlePreview');
            const colorInput = document.getElementById('title_color');
            const fontSelect = document.getElementById('title_font');
            const bgColorInput = document.getElementById('title_background_color');
            
            if (titlePreview && colorInput) {
                titlePreview.style.color = colorInput.value;
            }
            
            if (titlePreview && fontSelect) {
                const fontKey = fontSelect.value;
                titlePreview.style.fontFamily = fontFamilies[fontKey] || fontFamilies['bebas'];
            }
            
            if (titlePreview && bgColorInput) {
                if (bgColorInput.value && bgColorInput.value.trim() !== '') {
                    titlePreview.style.backgroundColor = bgColorInput.value;
                } else {
                    titlePreview.style.backgroundColor = 'transparent';
                }
            }
        }

        function addFeature() {
            const container = document.getElementById('featuresContainer');
            const featureDiv = document.createElement('div');
            featureDiv.className = 'feature-item flex gap-3 items-start bg-[#0a1628] rounded-lg p-3 border border-white/5';
            featureDiv.innerHTML = `
                <select name="theme_data[features][${featureCounter}][icon]" class="w-24 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm">
                    <option value="steam">🔥 Steam</option>
                    <option value="clean">✨ Clean</option>
                    <option value="fast">⚡ Fast</option>
                    <option value="eco">🌿 Eco</option>
                    <option value="power">💪 Power</option>
                    <option value="safe">🛡️ Safe</option>
                    <option value="timer">⏱️ Timer</option>
                    <option value="warranty">📋 Warranty</option>
                </select>
                <input 
                    type="text" 
                    name="theme_data[features][${featureCounter}][text]" 
                    class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                    placeholder="Feature description"
                />
                <button type="button" onclick="removeFeature(this)" class="text-red-400 hover:text-red-300 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(featureDiv);
            featureCounter++;
        }

        function removeFeature(btn) {
            btn.closest('.feature-item').remove();
        }

        function addSection() {
            const container = document.getElementById('sectionsContainer');
            const noSectionsMsg = document.getElementById('noSectionsMessage');
            if (noSectionsMsg) {
                noSectionsMsg.style.display = 'none';
            }

            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'section-item border border-pink-500/30 rounded-lg p-4 bg-[#0a1628] relative';
            sectionDiv.innerHTML = `
                <button 
                    type="button" 
                    onclick="removeSection(this)"
                    class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <h4 class="text-sm font-semibold text-pink-400 mb-4">Section ${sectionCounter + 1}</h4>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-2">Section Image</label>
                        <input 
                            type="file" 
                            name="landing_sections[${sectionCounter}][image]" 
                            accept="image/*"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-pink-600 file:text-white hover:file:bg-pink-700"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-2">Section Title</label>
                        <input 
                            type="text" 
                            name="landing_sections[${sectionCounter}][title_fr]" 
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                            placeholder="Enter section title"
                        />
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="block text-xs font-medium text-gray-300 mb-2">Section Description</label>
                    <textarea 
                        name="landing_sections[${sectionCounter}][description_fr]" 
                        rows="2"
                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                        placeholder="Enter section description"
                    ></textarea>
                </div>
            `;
            
            container.appendChild(sectionDiv);
            sectionCounter++;
        }

        function removeSection(btn) {
            btn.closest('.section-item').remove();
            const container = document.getElementById('sectionsContainer');
            if (container.querySelectorAll('.section-item').length === 0) {
                document.getElementById('noSectionsMessage').style.display = 'block';
            }
        }

        function previewImages(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            const files = event.target.files;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs">${i === 0 ? 'Hero Image' : 'Image ' + (i + 1)}</span>
                        </div>
                        ${i === 0 ? '<div class="absolute top-2 left-2 bg-cyan-500 text-white text-xs px-2 py-0.5 rounded">Hero</div>' : ''}
                    `;
                    preview.appendChild(div);
                }
                
                reader.readAsDataURL(file);
            }
        }
    </script>

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <style>
        #description-editor {
            min-height: 150px;
            background: white;
            border-radius: 0 0 0.5rem 0.5rem;
        }
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background: white;
            border-color: rgba(255,255,255,0.1) !important;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: rgba(255,255,255,0.1) !important;
            min-height: 130px;
            font-size: 16px;
        }
        .ql-editor {
            min-height: 130px;
            color: #1f2937;
        }
        .ql-editor.ql-blank::before {
            color: #9ca3af;
            font-style: normal;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descriptionTextarea = document.getElementById('description');
            const editorElement = document.getElementById('description-editor');
            
            if (editorElement && descriptionTextarea) {
                const quill = new Quill('#description-editor', {
                    theme: 'snow',
                    placeholder: 'Enter product description...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });

                if (descriptionTextarea.value) {
                    quill.root.innerHTML = descriptionTextarea.value;
                }

                const form = descriptionTextarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        descriptionTextarea.value = quill.root.innerHTML;
                        
                        // Validate promotions
                        const hasPromotionsCheckbox = document.getElementById('has_promotions');
                        const promotionsContainer = document.getElementById('promotionsContainer');
                        
                        if (hasPromotionsCheckbox && hasPromotionsCheckbox.checked) {
                            const promotions = [];
                            const promotionDivs = promotionsContainer.querySelectorAll('[id^="promotion-"]');
                            
                            promotionDivs.forEach((div) => {
                                const minQty = div.querySelector('input[name*="[min_quantity]"]');
                                const price = div.querySelector('input[name*="[price]"]');
                                const maxQty = div.querySelector('input[name*="[max_quantity]"]');
                                
                                if (minQty && price && minQty.value && price.value) {
                                    promotions.push({
                                        min_quantity: minQty.value,
                                        max_quantity: maxQty ? maxQty.value : null,
                                        price: price.value
                                    });
                                }
                            });
                            
                            document.getElementById('promotions_json').value = JSON.stringify(promotions);
                            
                            if (promotions.length === 0) {
                                e.preventDefault();
                                
                                const notification = document.createElement('div');
                                notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3';
                                notification.innerHTML = `
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold">Promotions enabled but no tiers added!</p>
                                        <p class="text-sm">Please click "Add Tier" to add at least one pricing tier, or disable Quantity-Based Pricing.</p>
                                    </div>
                                `;
                                document.body.appendChild(notification);
                                
                                document.getElementById('promotionsCard').scrollIntoView({ behavior: 'smooth', block: 'center' });
                                
                                setTimeout(() => {
                                    notification.remove();
                                }, 5000);
                                
                                return false;
                            }
                        }
                    });
                }

                quill.on('text-change', function() {
                    descriptionTextarea.value = quill.root.innerHTML;
                });
            }
        });
    </script>
@endsection
