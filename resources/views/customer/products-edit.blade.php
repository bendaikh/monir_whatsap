@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Product
                </h2>
                <p class="text-sm text-gray-400 mt-1">Update product information</p>
            </div>
            <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Products
            </a>
        </div>
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

    <div class="max-w-4xl">
        <form action="{{ route('app.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Product Information Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Product Information</h3>
                
                <div class="space-y-4">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            value="{{ old('name', $product->name) }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter product name"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nickname (Internal Use Only) -->
                    <div>
                        <label for="nickname" class="block text-sm font-medium text-gray-300 mb-2">
                            Nickname 
                            <span class="text-xs text-gray-500 font-normal">(internal use only - not shown on landing page)</span>
                        </label>
                        <input 
                            type="text" 
                            id="nickname" 
                            name="nickname" 
                            value="{{ old('nickname', $product->nickname) }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="e.g., Summer promo version, Test batch #1..."
                        />
                        <p class="mt-1 text-xs text-gray-500">Use this to identify different versions or campaigns for the same product</p>
                        @error('nickname')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <!-- Quill Rich Text Editor -->
                        <div id="description-editor" class="bg-white rounded-lg" style="min-height: 200px;"></div>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="hidden"
                        >{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Compare Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price (MAD) *</label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                step="0.01"
                                min="0"
                                required
                                value="{{ old('price', $product->price) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0.00"
                            />
                            @error('price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="compare_at_price" class="block text-sm font-medium text-gray-300 mb-2">Compare at Price (MAD)</label>
                            <input 
                                type="number" 
                                id="compare_at_price" 
                                name="compare_at_price" 
                                step="0.01"
                                min="0"
                                value="{{ old('compare_at_price', $product->compare_at_price) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="0.00"
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
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                value="{{ old('stock', $product->stock) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
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
                                value="{{ old('sku', $product->sku) }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
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
                                <!-- Hidden input to ensure a value is always sent -->
                                <input type="hidden" name="has_variations" value="0">
                                <input 
                                    type="checkbox" 
                                    id="has_variations" 
                                    name="has_variations" 
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('has_variations', $product->has_variations) ? 'checked' : '' }}
                                    onchange="toggleVariations(this.checked)"
                                />
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            @if($product->theme === 'theme2')
            <!-- Header Marquee Items (Theme 2 Only) -->
            @php
                $headerItems = $product->theme_data['header_items'] ?? [];
            @endphp
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
                            @if(!empty($headerItems))
                                @foreach($headerItems as $index => $item)
                                    @if(!empty($item['text']))
                                        @if($index > 0)<span class="text-gray-500">•</span>@endif
                                        <span>{{ $item['emoji'] ?? '🔥' }} {{ $item['text'] }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span>🔥 -50% OFF TODAY</span>
                                <span class="text-gray-500">•</span>
                                <span>🚚 Livraison gratuite</span>
                                <span class="text-gray-500">•</span>
                                <span>💵 Paiement à la livraison</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="headerItemsContainer" class="space-y-3">
                    @if(!empty($headerItems))
                        @foreach($headerItems as $index => $item)
                        <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[header_items][{{ $index }}][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                    <option value="🔥" {{ ($item['emoji'] ?? '') == '🔥' ? 'selected' : '' }}>🔥</option>
                                    <option value="💰" {{ ($item['emoji'] ?? '') == '💰' ? 'selected' : '' }}>💰</option>
                                    <option value="🚚" {{ ($item['emoji'] ?? '') == '🚚' ? 'selected' : '' }}>🚚</option>
                                    <option value="💵" {{ ($item['emoji'] ?? '') == '💵' ? 'selected' : '' }}>💵</option>
                                    <option value="⚡" {{ ($item['emoji'] ?? '') == '⚡' ? 'selected' : '' }}>⚡</option>
                                    <option value="✨" {{ ($item['emoji'] ?? '') == '✨' ? 'selected' : '' }}>✨</option>
                                    <option value="🎁" {{ ($item['emoji'] ?? '') == '🎁' ? 'selected' : '' }}>🎁</option>
                                    <option value="⭐" {{ ($item['emoji'] ?? '') == '⭐' ? 'selected' : '' }}>⭐</option>
                                    <option value="🛡️" {{ ($item['emoji'] ?? '') == '🛡️' ? 'selected' : '' }}>🛡️</option>
                                    <option value="✅" {{ ($item['emoji'] ?? '') == '✅' ? 'selected' : '' }}>✅</option>
                                    <option value="📦" {{ ($item['emoji'] ?? '') == '📦' ? 'selected' : '' }}>📦</option>
                                    <option value="🏷️" {{ ($item['emoji'] ?? '') == '🏷️' ? 'selected' : '' }}>🏷️</option>
                                </select>
                            </div>
                            <input 
                                type="text" 
                                name="theme_data[header_items][{{ $index }}][text]" 
                                value="{{ $item['text'] ?? '' }}"
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
                        @endforeach
                    @else
                        <!-- Default items if none exist -->
                        <div class="header-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[header_items][0][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateHeaderPreview()">
                                    <option value="🔥" selected>🔥</option>
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
                                value="-50% OFF TODAY"
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
                                value="Livraison gratuite"
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
                                value="Paiement à la livraison"
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
                    @endif
                </div>
                
                <p class="text-xs text-gray-500 mt-4">
                    <span class="text-yellow-400">Tip:</span> These items will scroll continuously at the top of your landing page. Add promotional messages, trust badges, or special offers.
                </p>
            </div>

            <!-- Title Styling (Theme 2 Only) -->
            @php
                $titleColor = $product->theme_data['title_color'] ?? '#ffffff';
                $titleFont = $product->theme_data['title_font'] ?? 'bebas';
            @endphp
            <div class="bg-gradient-to-br from-orange-900/50 to-red-900/50 border border-orange-500/30 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                    Title Styling
                </h3>
                <p class="text-sm text-gray-400 mb-4">Customize the color and font of your product title on the landing page</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="title_color" class="block text-sm font-medium text-gray-300 mb-2">Title Color</label>
                        <div class="flex gap-2">
                            <input 
                                type="color" 
                                id="title_color_picker" 
                                value="{{ $titleColor }}"
                                onchange="document.getElementById('title_color').value = this.value; updateTitlePreview()"
                                class="w-12 h-12 rounded-lg cursor-pointer border border-white/10 bg-transparent"
                            />
                            <input 
                                type="text" 
                                id="title_color" 
                                name="theme_data[title_color]" 
                                value="{{ $titleColor }}"
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
                            <option value="bebas" {{ $titleFont == 'bebas' ? 'selected' : '' }}>Bebas Neue (Bold Display)</option>
                            <option value="inter" {{ $titleFont == 'inter' ? 'selected' : '' }}>Inter (Modern Sans)</option>
                            <option value="cairo" {{ $titleFont == 'cairo' ? 'selected' : '' }}>Cairo (Arabic-friendly)</option>
                            <option value="oswald" {{ $titleFont == 'oswald' ? 'selected' : '' }}>Oswald (Condensed)</option>
                            <option value="montserrat" {{ $titleFont == 'montserrat' ? 'selected' : '' }}>Montserrat (Geometric)</option>
                            <option value="playfair" {{ $titleFont == 'playfair' ? 'selected' : '' }}>Playfair Display (Elegant)</option>
                            <option value="roboto" {{ $titleFont == 'roboto' ? 'selected' : '' }}>Roboto (Clean)</option>
                            <option value="poppins" {{ $titleFont == 'poppins' ? 'selected' : '' }}>Poppins (Friendly)</option>
                            <option value="anton" {{ $titleFont == 'anton' ? 'selected' : '' }}>Anton (Impact)</option>
                            <option value="raleway" {{ $titleFont == 'raleway' ? 'selected' : '' }}>Raleway (Elegant Sans)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Choose the font style for your title</p>
                    </div>
                </div>
                
                <!-- Title Preview -->
                <div class="mt-4 p-4 bg-gradient-to-r from-red-500 via-red-600 to-red-700 rounded-lg">
                    <p class="text-xs text-white/70 mb-2">Preview:</p>
                    <h2 id="titlePreview" class="text-3xl font-black uppercase" style="color: {{ $titleColor }}; font-family: '{{ $titleFont == 'bebas' ? 'Bebas Neue' : ($titleFont == 'playfair' ? 'Playfair Display' : ucfirst($titleFont)) }}', sans-serif;">
                        {{ $product->name }}
                    </h2>
                </div>
            </div>

            <!-- Trust Badges (Theme 2 Only) -->
            @php
                $trustBadges = $product->theme_data['trust_badges'] ?? [];
            @endphp
            <div class="bg-gradient-to-br from-emerald-900/50 to-green-900/50 border border-emerald-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Trust Badges
                        </h3>
                        <p class="text-sm text-gray-400 mt-1">Customize the trust badges displayed below the order form</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addTrustBadge()"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Badge
                    </button>
                </div>

                <!-- Preview of trust badges -->
                <div class="mb-4 p-4 bg-white rounded-lg">
                    <p class="text-xs text-gray-500 mb-2">Preview:</p>
                    <div id="trustBadgesPreview" class="flex flex-wrap gap-2">
                        @if(!empty($trustBadges))
                            @foreach($trustBadges as $badge)
                                @if(!empty($badge['text']))
                                <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                    <span>{{ $badge['emoji'] ?? '✅' }}</span>
                                    <span>{{ $badge['text'] }}</span>
                                </div>
                                @endif
                            @endforeach
                        @else
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
                        @endif
                    </div>
                </div>

                <div id="trustBadgesContainer" class="space-y-3">
                    @if(!empty($trustBadges))
                        @foreach($trustBadges as $index => $badge)
                        <div class="trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[trust_badges][{{ $index }}][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
                                    <option value="🚚" {{ ($badge['emoji'] ?? '') == '🚚' ? 'selected' : '' }}>🚚</option>
                                    <option value="💰" {{ ($badge['emoji'] ?? '') == '💰' ? 'selected' : '' }}>💰</option>
                                    <option value="🔒" {{ ($badge['emoji'] ?? '') == '🔒' ? 'selected' : '' }}>🔒</option>
                                    <option value="✅" {{ ($badge['emoji'] ?? '') == '✅' ? 'selected' : '' }}>✅</option>
                                    <option value="💵" {{ ($badge['emoji'] ?? '') == '💵' ? 'selected' : '' }}>💵</option>
                                    <option value="⚡" {{ ($badge['emoji'] ?? '') == '⚡' ? 'selected' : '' }}>⚡</option>
                                    <option value="🛡️" {{ ($badge['emoji'] ?? '') == '🛡️' ? 'selected' : '' }}>🛡️</option>
                                    <option value="⭐" {{ ($badge['emoji'] ?? '') == '⭐' ? 'selected' : '' }}>⭐</option>
                                    <option value="🎁" {{ ($badge['emoji'] ?? '') == '🎁' ? 'selected' : '' }}>🎁</option>
                                    <option value="📦" {{ ($badge['emoji'] ?? '') == '📦' ? 'selected' : '' }}>📦</option>
                                    <option value="🏷️" {{ ($badge['emoji'] ?? '') == '🏷️' ? 'selected' : '' }}>🏷️</option>
                                    <option value="✨" {{ ($badge['emoji'] ?? '') == '✨' ? 'selected' : '' }}>✨</option>
                                    <option value="💎" {{ ($badge['emoji'] ?? '') == '💎' ? 'selected' : '' }}>💎</option>
                                    <option value="🔥" {{ ($badge['emoji'] ?? '') == '🔥' ? 'selected' : '' }}>🔥</option>
                                    <option value="💯" {{ ($badge['emoji'] ?? '') == '💯' ? 'selected' : '' }}>💯</option>
                                    <option value="🏆" {{ ($badge['emoji'] ?? '') == '🏆' ? 'selected' : '' }}>🏆</option>
                                </select>
                            </div>
                            <input 
                                type="text" 
                                name="theme_data[trust_badges][{{ $index }}][text]" 
                                value="{{ $badge['text'] ?? '' }}"
                                class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                placeholder="Enter badge text"
                                onkeyup="updateTrustBadgesPreview()"
                            />
                            <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <!-- Default trust badges if none exist -->
                        <div class="trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[trust_badges][0][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
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
                                value="Free Shipping"
                                class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                placeholder="Enter badge text"
                                onkeyup="updateTrustBadgesPreview()"
                            />
                            <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[trust_badges][1][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
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
                                value="Money Back Guarantee"
                                class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                placeholder="Enter badge text"
                                onkeyup="updateTrustBadgesPreview()"
                            />
                            <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[trust_badges][2][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
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
                                value="Secure Payment"
                                class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                placeholder="Enter badge text"
                                onkeyup="updateTrustBadgesPreview()"
                            />
                            <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5">
                            <div class="flex-shrink-0">
                                <select name="theme_data[trust_badges][3][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
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
                                value="1 Year Warranty"
                                class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 text-sm"
                                placeholder="Enter badge text"
                                onkeyup="updateTrustBadgesPreview()"
                            />
                            <button type="button" onclick="removeTrustBadge(this)" class="text-red-400 hover:text-red-300 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                
                <p class="text-xs text-gray-500 mt-4">
                    <span class="text-emerald-400">Tip:</span> Trust badges help increase conversions by showing customers your product guarantees and benefits.
                </p>
            </div>
            @endif

            <!-- Product Variations Card -->
            <div id="variationsCard" class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 {{ $product->has_variations ? '' : 'hidden' }}">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Product Variations</h3>
                        <p class="text-xs text-gray-500 mt-1">Manage different options for this product</p>
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
                    @if($product->variations && $product->variations->count() > 0)
                        @foreach($product->variations as $variation)
                        <div class="border border-blue-500/30 rounded-lg p-4 bg-[#0a1628] relative" id="variation-existing-{{ $variation->id }}">
                            <button 
                                type="button" 
                                onclick="removeVariation('existing-{{ $variation->id }}')"
                                class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            
                            <h4 class="text-sm font-semibold text-blue-400 mb-4">{{ $variation->attributes_display }}</h4>
                            <input type="hidden" name="variations[{{ $loop->index }}][id]" value="{{ $variation->id }}">
                            
                            <div class="space-y-3">
                                <div class="border border-white/10 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-xs font-medium text-gray-300">Attributes</label>
                                        <button 
                                            type="button" 
                                            onclick="addAttributeToExisting('existing-{{ $variation->id }}', {{ $loop->index }})"
                                            class="text-xs px-2 py-1 bg-cyan-600 hover:bg-cyan-700 text-white rounded transition"
                                        >
                                            + Add Attribute
                                        </button>
                                    </div>
                                    <div id="attributes-container-existing-{{ $variation->id }}" class="space-y-2">
                                        @foreach($variation->attributes as $attrName => $attrValue)
                                        <div class="flex gap-2">
                                            <input 
                                                type="text" 
                                                name="variations[{{ $loop->parent->index }}][attributes][{{ $loop->index }}][name]" 
                                                value="{{ $attrName }}"
                                                placeholder="Attribute"
                                                class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                                            />
                                            <input 
                                                type="text" 
                                                name="variations[{{ $loop->parent->index }}][attributes][{{ $loop->index }}][value]" 
                                                value="{{ $attrValue }}"
                                                placeholder="Value"
                                                class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                                            />
                                            <button 
                                                type="button" 
                                                onclick="this.parentElement.remove()"
                                                class="text-red-400 hover:text-red-300"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-300 mb-1">Price (MAD) *</label>
                                        <input 
                                            type="number" 
                                            name="variations[{{ $loop->index }}][price]" 
                                            value="{{ $variation->price }}"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-300 mb-1">Compare at Price</label>
                                        <input 
                                            type="number" 
                                            name="variations[{{ $loop->index }}][compare_at_price]" 
                                            value="{{ $variation->compare_at_price }}"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                        />
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-300 mb-1">Stock *</label>
                                        <input 
                                            type="number" 
                                            name="variations[{{ $loop->index }}][stock]" 
                                            value="{{ $variation->stock }}"
                                            min="0"
                                            required
                                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-300 mb-1">SKU</label>
                                        <input 
                                            type="text" 
                                            name="variations[{{ $loop->index }}][sku]" 
                                            value="{{ $variation->sku }}"
                                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                        />
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2">
                                        <input 
                                            type="checkbox" 
                                            name="variations[{{ $loop->index }}][is_default]" 
                                            value="1"
                                            {{ $variation->is_default ? 'checked' : '' }}
                                            class="rounded bg-[#0f1c2e] border-white/10 text-blue-600"
                                        />
                                        <span class="text-xs text-gray-300">Default variation</span>
                                    </label>
                                    
                                    <label class="flex items-center gap-2">
                                        <input 
                                            type="checkbox" 
                                            name="variations[{{ $loop->index }}][is_active]" 
                                            value="1"
                                            {{ $variation->is_active ? 'checked' : '' }}
                                            class="rounded bg-[#0f1c2e] border-white/10 text-blue-600"
                                        />
                                        <span class="text-xs text-gray-300">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <div id="noVariationsMessage" class="text-center py-8 text-gray-500 text-sm {{ ($product->variations && $product->variations->count() > 0) ? 'hidden' : '' }}">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Click "Add Variation" to create your first product variation
                </div>
            </div>

            <!-- Current Images Card -->
            @if($product->images && count($product->images) > 0)
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Current Images
                </h3>
                <p class="text-sm text-gray-400 mb-4">Manage your product images - click on an image to delete or replace it</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($product->images as $index => $image)
                    <div class="relative group border-2 border-transparent hover:border-cyan-500 rounded-xl transition" id="image-container-{{ $index }}">
                        <img src="/storage/{{ $image }}" alt="Product image {{ $index + 1 }}" class="w-full h-40 object-cover rounded-lg" />
                        
                        <!-- Image number badge -->
                        <div class="absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                            #{{ $index + 1 }}
                        </div>
                        
                        <!-- Action buttons overlay -->
                        <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition rounded-lg flex flex-col items-center justify-center gap-2 p-3">
                            <!-- Delete checkbox -->
                            <label class="flex items-center gap-2 px-3 py-2 bg-red-600/80 hover:bg-red-600 rounded-lg cursor-pointer transition w-full justify-center">
                                <input type="checkbox" name="delete_images[]" value="{{ $image }}" class="w-4 h-4 text-red-500 rounded focus:ring-red-500">
                                <span class="text-white text-sm font-medium">Delete</span>
                            </label>
                            
                            <!-- Replace image input -->
                            <label class="flex items-center gap-2 px-3 py-2 bg-cyan-600/80 hover:bg-cyan-600 rounded-lg cursor-pointer transition w-full justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-white text-sm font-medium">Replace</span>
                                <input type="file" name="replace_image_{{ $index }}" accept="image/*" class="hidden" onchange="previewReplaceImage(this, {{ $index }})">
                            </label>
                        </div>
                        
                        <!-- Preview of replacement image -->
                        <div id="replace-preview-{{ $index }}" class="hidden absolute inset-0 rounded-lg overflow-hidden">
                            <img id="replace-preview-img-{{ $index }}" src="" alt="New image preview" class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">New</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-3 bg-gray-800/50 border border-gray-700 rounded-lg">
                    <p class="text-xs text-gray-400">
                        <span class="text-yellow-400 font-medium">Tip:</span> Hover over an image to see delete and replace options. Checked images will be deleted when you save.
                    </p>
                </div>
            </div>
            @endif
            
            <!-- Default Language Selection -->
            <div class="bg-gradient-to-br from-indigo-900 to-purple-900 border border-indigo-500/50 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    Landing Page Default Language
                </h3>
                <p class="text-sm text-indigo-200 mb-4">Choose the language that will be displayed by default when visitors first open the landing page</p>
                
                <select
                    id="default_language"
                    name="default_language"
                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                >
                    <option value="">-- Use first enabled language --</option>
                    @php
                        $defaultLang = $product->default_language ?? '';
                        $languageOptions = [
                            'fr' => '🇫🇷 French',
                            'en' => '🇬🇧 English',
                            'ar' => '🇸🇦 Arabic',
                            'es' => '🇪🇸 Spanish',
                            'de' => '🇩🇪 German',
                            'it' => '🇮🇹 Italian',
                            'pt' => '🇵🇹 Portuguese',
                            'zh' => '🇨🇳 Chinese',
                            'ja' => '🇯🇵 Japanese',
                            'ko' => '🇰🇷 Korean',
                            'ru' => '🇷🇺 Russian',
                            'hi' => '🇮🇳 Hindi',
                            'tr' => '🇹🇷 Turkish',
                            'nl' => '🇳🇱 Dutch',
                            'pl' => '🇵🇱 Polish',
                            'sw' => '🇹🇿 Swahili',
                        ];
                    @endphp
                    @foreach($languageOptions as $code => $name)
                        <option value="{{ $code }}" {{ $defaultLang === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                
                <p class="mt-2 text-xs text-indigo-200">
                    <span class="font-semibold">Current enabled languages:</span> 
                    {{ implode(', ', array_map('strtoupper', $product->landing_page_languages ?? ['fr'])) }}
                </p>
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
                                {{ $product->has_promotions ? 'checked' : '' }}
                                class="sr-only peer"
                                onchange="togglePromotions(this.checked)"
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                        </label>
                    </div>
                </div>

                <div id="promotionsContent" class="{{ $product->has_promotions ? '' : 'hidden' }}">
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
                        @foreach($product->promotions ?? [] as $promotion)
                        <div class="border border-yellow-500/30 rounded-lg p-4 bg-[#0a1628] relative" id="promotion-{{ $loop->index }}">
                            <input type="hidden" name="promotions[{{ $loop->index }}][id]" value="{{ $promotion->id }}">
                            <button 
                                type="button" 
                                onclick="removePromotion({{ $loop->index }})"
                                class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            
                            <!-- Display Label -->
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-yellow-400 mb-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Display Label <span class="text-gray-500 font-normal">(shown to customers)</span>
                                    </span>
                                </label>
                                <input 
                                    type="text" 
                                    name="promotions[{{ $loop->index }}][label]" 
                                    value="{{ $promotion->label }}"
                                    class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-yellow-500/50 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                    placeholder="e.g., Buy 2 Get 10% Off, Pack of 3, Family Bundle..."
                                />
                                <p class="text-xs text-gray-500 mt-1">This label will be displayed on the landing page instead of quantity/price details</p>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-300 mb-1">Min Quantity *</label>
                                    <input 
                                        type="number" 
                                        name="promotions[{{ $loop->index }}][min_quantity]" 
                                        value="{{ $promotion->min_quantity }}"
                                        min="1"
                                        required
                                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-300 mb-1">Max Quantity</label>
                                    <input 
                                        type="number" 
                                        name="promotions[{{ $loop->index }}][max_quantity]" 
                                        value="{{ $promotion->max_quantity }}"
                                        min="1"
                                        placeholder="Leave empty for unlimited"
                                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-300 mb-1">Price per Unit (MAD) *</label>
                                    <input 
                                        type="number" 
                                        name="promotions[{{ $loop->index }}][price]" 
                                        value="{{ $promotion->price }}"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                                    />
                                </div>
                            </div>
                            
                            <div class="bg-yellow-500/10 border border-yellow-500/20 rounded p-2 text-xs text-yellow-300">
                                <strong>Current:</strong> {{ $promotion->label ?: 'No label' }} | Min: {{ $promotion->min_quantity }}, Max: {{ $promotion->max_quantity ?? 'unlimited' }}, Price: {{ number_format($promotion->price, 2) }} MAD
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div id="noPromotionsMessage" class="text-center py-8 border-2 border-dashed border-yellow-500/30 rounded-lg bg-yellow-500/5" style="display: {{ $product->promotions->isEmpty() ? 'block' : 'none' }};">
                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-yellow-300 font-semibold mb-1">⚠️ No pricing tiers added yet!</p>
                        <p class="text-gray-400 text-sm">Click <strong class="text-yellow-400">"Add Tier"</strong> above to create quantity-based pricing</p>
                        <p class="text-xs mt-2 text-gray-500">Example: Buy 2+ items → Pay 90 MAD each instead of 100 MAD</p>
                    </div>
                    
                    <!-- Hidden field to ensure promotions data is always captured -->
                    <input type="hidden" id="promotions_json" name="promotions_json" value="[]">
                </div>
            </div>

            <!-- Product Images Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Add New Images</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Images</label>
                    <div class="border-2 border-dashed border-white/10 rounded-lg p-8 text-center hover:border-emerald-500/50 transition">
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
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </label>
                    </div>
                    <div id="imagePreview" class="grid grid-cols-4 gap-4 mt-4"></div>
                    @error('images')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Landing Page Sections (Image with Description) -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Landing Page Sections</h3>
                        <p class="text-xs text-gray-500 mt-1">Add images with descriptions for your landing page (optional)</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="addLandingSection()"
                        class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Section
                    </button>
                </div>
                
                <div id="landingSectionsContainer" class="space-y-4">
                    @if($product->landing_page_sections && count($product->landing_page_sections) > 0)
                        @foreach($product->landing_page_sections as $index => $section)
                        <div class="border border-white/10 rounded-lg p-4 bg-[#0a1628]" id="existing-section-{{ $index }}">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-300">Section {{ $index + 1 }}</h4>
                                <button type="button" onclick="removeExistingSection({{ $index }})" 
                                        class="text-red-400 hover:text-red-300 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-3">
                                @if(!empty($section['image']))
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Current Image</label>
                                    <img src="/storage/{{ $section['image'] }}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                                </div>
                                @endif
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Replace Image</label>
                                    <input type="file" 
                                           name="landing_sections[{{ $index }}][image]" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-700 cursor-pointer">
                                    <input type="hidden" name="landing_sections[{{ $index }}][existing_image]" value="{{ $section['image'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Title (FR)</label>
                                    <input type="text" 
                                           name="landing_sections[{{ $index }}][title_fr]" 
                                           value="{{ old('landing_sections.'.$index.'.title_fr', $section['title_fr'] ?? '') }}"
                                           class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Description (FR)</label>
                                    <textarea name="landing_sections[{{ $index }}][description_fr]" 
                                              rows="2"
                                              class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('landing_sections.'.$index.'.description_fr', $section['description_fr'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Title (EN)</label>
                                    <input type="text" 
                                           name="landing_sections[{{ $index }}][title_en]" 
                                           value="{{ old('landing_sections.'.$index.'.title_en', $section['title_en'] ?? '') }}"
                                           class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Description (EN)</label>
                                    <textarea name="landing_sections[{{ $index }}][description_en]" 
                                              rows="2"
                                              class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('landing_sections.'.$index.'.description_en', $section['description_en'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Title (AR)</label>
                                    <input type="text" 
                                           name="landing_sections[{{ $index }}][title_ar]" 
                                           value="{{ old('landing_sections.'.$index.'.title_ar', $section['title_ar'] ?? '') }}"
                                           class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Description (AR)</label>
                                    <textarea name="landing_sections[{{ $index }}][description_ar]" 
                                              rows="2"
                                              class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('landing_sections.'.$index.'.description_ar', $section['description_ar'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Form Fields Configuration Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Landing Page Form Fields
                </h3>
                <p class="text-sm text-gray-400 mb-6">Configure which fields appear on the landing page order form</p>
                
                @php
                    $formFields = $product->theme_data['form_fields'] ?? [
                        'name' => ['enabled' => true, 'required' => true],
                        'phone' => ['enabled' => true, 'required' => true],
                        'email' => ['enabled' => false, 'required' => false],
                        'city' => ['enabled' => false, 'required' => false],
                        'address' => ['enabled' => false, 'required' => false],
                        'note' => ['enabled' => true, 'required' => false],
                    ];
                @endphp

                <div class="space-y-3">
                    <!-- Name Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-white font-medium">Name</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][name][enabled]" 
                                       value="1"
                                       {{ ($formFields['name']['enabled'] ?? true) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][name][required]" 
                                       value="1"
                                       {{ ($formFields['name']['required'] ?? true) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-white font-medium">Phone</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][phone][enabled]" 
                                       value="1"
                                       {{ ($formFields['phone']['enabled'] ?? true) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][phone][required]" 
                                       value="1"
                                       {{ ($formFields['phone']['required'] ?? true) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-white font-medium">Email</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][email][enabled]" 
                                       value="1"
                                       {{ ($formFields['email']['enabled'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][email][required]" 
                                       value="1"
                                       {{ ($formFields['email']['required'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>

                    <!-- City Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-white font-medium">City</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][city][enabled]" 
                                       value="1"
                                       {{ ($formFields['city']['enabled'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][city][required]" 
                                       value="1"
                                       {{ ($formFields['city']['required'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>

                    <!-- Address Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="text-white font-medium">Address</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][address][enabled]" 
                                       value="1"
                                       {{ ($formFields['address']['enabled'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][address][required]" 
                                       value="1"
                                       {{ ($formFields['address']['required'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>

                    <!-- Note Field -->
                    <div class="flex items-center justify-between p-3 bg-[#0a1628] rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="text-white font-medium">Note / Message</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][note][enabled]" 
                                       value="1"
                                       {{ ($formFields['note']['enabled'] ?? true) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-cyan-600">
                                <span class="text-gray-300">Show</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" 
                                       name="theme_data[form_fields][note][required]" 
                                       value="1"
                                       {{ ($formFields['note']['required'] ?? false) ? 'checked' : '' }}
                                       class="rounded bg-[#0f1c2e] border-white/10 text-orange-600">
                                <span class="text-gray-300">Required</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Settings</h3>
                
                <div class="space-y-4">
                    <!-- AI Landing Page Regeneration -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">AI Landing Page</label>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($product->landing_page_status === 'completed')
                                    <span class="text-emerald-400">Landing page generated</span> - Click to regenerate
                                @elseif($product->landing_page_status === 'processing')
                                    <span class="text-yellow-400">Generation in progress...</span>
                                @elseif($product->landing_page_status === 'failed')
                                    <span class="text-red-400">Generation failed</span> - Click to retry
                                @else
                                    Generate a professional landing page using AI
                                @endif
                            </p>
                        </div>
                        <button 
                            type="button"
                            id="regenerate-landing-page-btn"
                            onclick="regenerateLandingPage({{ $product->id }})"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $product->landing_page_status === 'processing' ? 'disabled' : '' }}
                        >
                            <svg class="w-4 h-4 {{ $product->landing_page_status === 'processing' ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($product->landing_page_status === 'processing')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                @endif
                            </svg>
                            @if($product->landing_page_status === 'processing')
                                Generating...
                            @elseif($product->landing_page_status === 'completed')
                                Regenerate
                            @else
                                Generate AI
                            @endif
                        </button>
                    </div>

                    <!-- AI Product Images Generation -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">AI Product Images</label>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($product->ai_images_status === 'completed')
                                    <span class="text-emerald-400">{{ count($product->ai_generated_images ?? []) }} images generated</span> - Click to generate more
                                @elseif($product->ai_images_status === 'processing')
                                    <span class="text-yellow-400">Generation in progress ({{ $product->ai_images_progress ?? 0 }}%)...</span>
                                @elseif($product->ai_images_status === 'failed')
                                    <span class="text-red-400">Generation failed</span> - Click to retry
                                @else
                                    Generate realistic product images using AI
                                @endif
                            </p>
                        </div>
                        <button 
                            type="button"
                            id="regenerate-images-btn"
                            onclick="regenerateProductImages({{ $product->id }})"
                            class="px-4 py-2 bg-gradient-to-r from-orange-600 to-yellow-600 hover:from-orange-700 hover:to-yellow-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $product->ai_images_status === 'processing' ? 'disabled' : '' }}
                        >
                            <svg class="w-4 h-4 {{ $product->ai_images_status === 'processing' ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($product->ai_images_status === 'processing')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                @endif
                            </svg>
                            @if($product->ai_images_status === 'processing')
                                Generating...
                            @elseif($product->ai_images_status === 'completed')
                                Generate More
                            @else
                                Generate Images
                            @endif
                        </button>
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
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
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
                                {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                            />
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let sectionCounter = {{ $product->landing_page_sections ? count($product->landing_page_sections) : 0 }};
        let variationCounter = {{ $product->variations ? $product->variations->count() : 0 }};
        let promotionCounter = {{ $product->promotions ? $product->promotions->count() : 0 }};
        let headerItemCounter = {{ isset($product->theme_data['header_items']) ? count($product->theme_data['header_items']) : 3 }};
        let trustBadgeCounter = {{ isset($product->theme_data['trust_badges']) ? count($product->theme_data['trust_badges']) : 4 }};

        // Preview for image replacement
        function previewReplaceImage(input, index) {
            const previewContainer = document.getElementById(`replace-preview-${index}`);
            const previewImg = document.getElementById(`replace-preview-img-${index}`);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
            }
        }
        
        // Preview for newly uploaded images
        function previewImages(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (event.target.files) {
                Array.from(event.target.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">New</div>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function addHeaderItem() {
            const container = document.getElementById('headerItemsContainer');
            if (!container) return;
            
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
            if (!preview) return;
            
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
            badgeDiv.className = 'trust-badge-item flex gap-3 items-center bg-[#0a1628] rounded-lg p-3 border border-white/5';
            badgeDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <select name="theme_data[trust_badges][${trustBadgeCounter}][emoji]" class="w-16 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm text-center" onchange="updateTrustBadgesPreview()">
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            
            if (titlePreview && colorInput) {
                titlePreview.style.color = colorInput.value;
            }
            
            if (titlePreview && fontSelect) {
                const fontKey = fontSelect.value;
                titlePreview.style.fontFamily = fontFamilies[fontKey] || fontFamilies['bebas'];
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
                
                <!-- Display Label -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-yellow-400 mb-1">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Display Label <span class="text-gray-500 font-normal">(shown to customers)</span>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="promotions[${promotionId}][label]" 
                        class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-yellow-500/50 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="e.g., Buy 2 Get 10% Off, Pack of 3, Family Bundle..."
                    />
                    <p class="text-xs text-gray-500 mt-1">This label will be displayed on the landing page instead of quantity/price details</p>
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Min Quantity *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][min_quantity]" 
                            min="1"
                            required
                            placeholder="e.g., 2"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Max Quantity</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][max_quantity]" 
                            min="1"
                            placeholder="Leave empty for unlimited"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Price per Unit (MAD) *</label>
                        <input 
                            type="number" 
                            name="promotions[${promotionId}][price]" 
                            step="0.01"
                            min="0"
                            required
                            placeholder="e.g., 90.00"
                            class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>
                </div>
                
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded p-2 text-xs text-yellow-300">
                    <strong>Example:</strong> Min: 2, Max: 4, Price: 90.00 → Customers buying 2-4 items pay 90 MAD per item
                </div>
            `;
            
            container.appendChild(promotionDiv);
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
        }

        function toggleVariations(enabled) {
            const variationsCard = document.getElementById('variationsCard');
            const priceField = document.getElementById('price');
            const comparePriceField = document.getElementById('compare_at_price');
            const stockField = document.getElementById('stock');
            const skuField = document.getElementById('sku');
            const basicPriceFields = [priceField, comparePriceField, stockField, skuField];
            
            if (enabled) {
                variationsCard.classList.remove('hidden');
                // Disable and clear basic price/stock fields when variations are enabled
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = true;
                        field.classList.add('opacity-50', 'cursor-not-allowed');
                        // Remove required attribute and set value to empty or 0
                        field.removeAttribute('required');
                        if (field.type === 'number') {
                            field.value = '0';
                        }
                    }
                });
            } else {
                variationsCard.classList.add('hidden');
                // Re-enable basic price/stock fields
                basicPriceFields.forEach(field => {
                    if (field) {
                        field.disabled = false;
                        field.classList.remove('opacity-50', 'cursor-not-allowed');
                        // Re-add required attribute for price field
                        if (field.id === 'price') {
                            field.setAttribute('required', 'required');
                        }
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
                
                <h4 class="text-sm font-semibold text-blue-400 mb-4">New Variation ${variationId + 1}</h4>
                
                <div class="space-y-3">
                    <div class="border border-white/10 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-medium text-gray-300">Attributes</label>
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
                            <label class="block text-xs font-medium text-gray-300 mb-1">Price (MAD) *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][price]" 
                                step="0.01"
                                min="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Compare at Price</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][compare_at_price]" 
                                step="0.01"
                                min="0"
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                            />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">Stock *</label>
                            <input 
                                type="number" 
                                name="variations[${variationId}][stock]" 
                                min="0"
                                value="0"
                                required
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-300 mb-1">SKU</label>
                            <input 
                                type="text" 
                                name="variations[${variationId}][sku]" 
                                class="w-full px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white"
                            />
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_default]" 
                                value="1"
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600"
                            />
                            <span class="text-xs text-gray-300">Default variation</span>
                        </label>
                        
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                name="variations[${variationId}][is_active]" 
                                value="1"
                                checked
                                class="rounded bg-[#0f1c2e] border-white/10 text-blue-600"
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
            attributeDiv.className = 'flex gap-2';
            attributeDiv.innerHTML = `
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][name]" 
                    placeholder="Attribute (e.g., Color)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                />
                <input 
                    type="text" 
                    name="variations[${variationId}][attributes][${attributeId}][value]" 
                    placeholder="Value (e.g., Red)"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                />
                <button 
                    type="button" 
                    onclick="this.parentElement.remove()"
                    class="text-red-400 hover:text-red-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            
            container.appendChild(attributeDiv);
        }

        function addAttributeToExisting(existingId, variationIndex) {
            const container = document.getElementById(`attributes-container-${existingId}`);
            const attributeId = container.children.length;
            
            const attributeDiv = document.createElement('div');
            attributeDiv.className = 'flex gap-2';
            attributeDiv.innerHTML = `
                <input 
                    type="text" 
                    name="variations[${variationIndex}][attributes][${attributeId}][name]" 
                    placeholder="Attribute"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                />
                <input 
                    type="text" 
                    name="variations[${variationIndex}][attributes][${attributeId}][value]" 
                    placeholder="Value"
                    class="flex-1 px-3 py-2 text-sm bg-[#0f1c2e] border border-white/10 rounded text-white placeholder-gray-500"
                />
                <button 
                    type="button" 
                    onclick="this.parentElement.remove()"
                    class="text-red-400 hover:text-red-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            
            container.appendChild(attributeDiv);
        }

        function removeVariation(variationId) {
            const variationDiv = document.getElementById(`variation-${variationId}`);
            const existingDiv = document.getElementById(`variation-existing-${variationId}`);
            
            if (variationDiv) {
                variationDiv.remove();
            } else if (existingDiv && confirm('Are you sure you want to remove this variation?')) {
                existingDiv.remove();
            }
            
            const container = document.getElementById('variationsContainer');
            if (container.children.length === 0) {
                document.getElementById('noVariationsMessage').style.display = 'block';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const hasVariations = document.getElementById('has_variations').checked;
            if (hasVariations) {
                toggleVariations(true);
            }

            // Add form validation for promotions
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const hasPromotionsCheckbox = document.getElementById('has_promotions');
                const promotionsContainer = document.getElementById('promotionsContainer');
                
                // Collect promotions data and store in hidden JSON field as backup
                const promotions = [];
                const promotionDivs = promotionsContainer.querySelectorAll('[id^="promotion-"]');
                
                console.log('Found promotion divs:', promotionDivs.length);
                
                promotionDivs.forEach((div, index) => {
                    const minQty = div.querySelector('input[name*="[min_quantity]"]');
                    const maxQty = div.querySelector('input[name*="[max_quantity]"]');
                    const price = div.querySelector('input[name*="[price]"]');
                    const idField = div.querySelector('input[name*="[id]"]');
                    
                    if (minQty && price && minQty.value && price.value) {
                        const promo = {
                            min_quantity: minQty.value,
                            max_quantity: maxQty ? maxQty.value : null,
                            price: price.value
                        };
                        if (idField && idField.value) {
                            promo.id = idField.value;
                        }
                        promotions.push(promo);
                    }
                });
                
                console.log('Collected promotions:', promotions);
                document.getElementById('promotions_json').value = JSON.stringify(promotions);
                
                if (hasPromotionsCheckbox.checked && promotions.length === 0) {
                    e.preventDefault();
                    
                    // Show error notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3';
                    notification.innerHTML = `
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Promotions enabled but no tiers added!</p>
                            <p class="text-sm">Please click "Add Tier" to add at least one pricing tier, or uncheck "Enable Quantity-Based Pricing".</p>
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Scroll to promotions section
                    document.getElementById('promotionsCard').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Remove notification after 5 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 5000);
                    
                    return false;
                }
            });
        });

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
                            <span class="text-white text-xs">New Image ${i + 1}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                }
                
                reader.readAsDataURL(file);
            }
        }

        function addLandingSection() {
            const container = document.getElementById('landingSectionsContainer');
            const sectionId = sectionCounter++;
            
            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'border border-white/10 rounded-lg p-4 bg-[#0a1628]';
            sectionDiv.id = `section-${sectionId}`;
            sectionDiv.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-300">Section ${sectionId + 1}</h4>
                    <button type="button" onclick="removeLandingSection(${sectionId})" 
                            class="text-red-400 hover:text-red-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Section Image</label>
                        <input type="file" 
                               name="landing_sections[${sectionId}][image]" 
                               accept="image/*"
                               onchange="previewSectionImage(event, ${sectionId})"
                               class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-700 cursor-pointer">
                        <div id="section-image-preview-${sectionId}" class="mt-2"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Title (FR)</label>
                        <input type="text" 
                               name="landing_sections[${sectionId}][title_fr]" 
                               placeholder="e.g., Protection efficace"
                               class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Description (FR)</label>
                        <textarea name="landing_sections[${sectionId}][description_fr]" 
                                  rows="2"
                                  placeholder="Description en français..."
                                  class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Title (EN)</label>
                        <input type="text" 
                               name="landing_sections[${sectionId}][title_en]" 
                               placeholder="e.g., Effective protection"
                               class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Description (EN)</label>
                        <textarea name="landing_sections[${sectionId}][description_en]" 
                                  rows="2"
                                  placeholder="Description in English..."
                                  class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Title (AR)</label>
                        <input type="text" 
                               name="landing_sections[${sectionId}][title_ar]" 
                               placeholder="مثال: حماية فعّالة"
                               class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Description (AR)</label>
                        <textarea name="landing_sections[${sectionId}][description_ar]" 
                                  rows="2"
                                  placeholder="الوصف بالعربية..."
                                  class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
                    </div>
                </div>
            `;
            
            container.appendChild(sectionDiv);
        }

        function removeLandingSection(sectionId) {
            const section = document.getElementById(`section-${sectionId}`);
            if (section) {
                section.remove();
            }
        }

        function removeExistingSection(sectionId) {
            const section = document.getElementById(`existing-section-${sectionId}`);
            if (section && confirm('Are you sure you want to remove this section?')) {
                section.remove();
            }
        }

        function previewSectionImage(event, sectionId) {
            const preview = document.getElementById(`section-image-preview-${sectionId}`);
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                    `;
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
            min-height: 200px;
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
            min-height: 180px;
            font-size: 16px;
        }
        .ql-editor {
            min-height: 180px;
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
                const uploadUrl = '{{ route("app.quill.upload-image") }}';
                const csrfToken = '{{ csrf_token() }}';
                
                function imageHandler() {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();
                    
                    const quill = this.quill;
                    
                    input.onchange = function() {
                        const file = input.files[0];
                        if (!file) return;
                        
                        const formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', csrfToken);
                        
                        const range = quill.getSelection(true);
                        quill.insertText(range.index, 'Uploading...', { italic: true });
                        
                        fetch(uploadUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => {
                            quill.deleteText(range.index, 'Uploading...'.length);
                            if (data.success && data.url) {
                                quill.insertEmbed(range.index, 'image', data.url);
                                quill.setSelection(range.index + 1);
                            } else {
                                alert('Image upload failed');
                            }
                        })
                        .catch(err => {
                            quill.deleteText(range.index, 'Uploading...'.length);
                            console.error(err);
                            alert('Image upload failed');
                        });
                    };
                }
                
                const quill = new Quill('#description-editor', {
                    theme: 'snow',
                    placeholder: 'Enter product description...',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'align': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'image', 'video'],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                ['blockquote', 'code-block'],
                                ['clean']
                            ],
                            handlers: { 'image': imageHandler }
                        }
                    }
                });

                if (descriptionTextarea.value) {
                    quill.root.innerHTML = descriptionTextarea.value;
                }

                const form = descriptionTextarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        descriptionTextarea.value = quill.root.innerHTML;
                    });
                }

                quill.on('text-change', function() {
                    descriptionTextarea.value = quill.root.innerHTML;
                });
            }
        });

        // AI Landing Page Regeneration
        function regenerateLandingPage(productId) {
            const btn = document.getElementById('regenerate-landing-page-btn');
            const originalContent = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generating...
            `;
            
            fetch(`/app/products/${productId}/generate-landing-page`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showNotification('error', data.message || 'An error occurred');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            })
            .catch(error => {
                showNotification('error', 'An error occurred while generating the landing page');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        }

        // AI Product Images Regeneration
        function regenerateProductImages(productId) {
            const btn = document.getElementById('regenerate-images-btn');
            const originalContent = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generating...
            `;
            
            fetch(`/app/products/${productId}/generate-images`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    pollImageProgress(productId);
                } else {
                    showNotification('error', data.message || 'An error occurred');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            })
            .catch(error => {
                showNotification('error', 'An error occurred while generating images');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        }

        function pollImageProgress(productId) {
            const interval = setInterval(() => {
                fetch(`/app/products/${productId}/check-image-progress`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'completed' || data.status === 'failed') {
                            clearInterval(interval);
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    });
            }, 3000);
        }

        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-emerald-500' : 'bg-red-500'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' 
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>
@endsection
