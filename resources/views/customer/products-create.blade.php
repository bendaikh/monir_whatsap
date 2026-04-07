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
                <p class="text-sm text-gray-400 mt-1">Add a new product to your catalog</p>
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
    
    @if(session('warning'))
    <div class="mb-6 bg-yellow-500/20 border border-yellow-500/50 text-yellow-400 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>{{ session('warning') }}</span>
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
            <span class="font-semibold">AI Landing Page Feature Requires Configuration</span>
            <span class="block text-sm mt-1">Please <a href="{{ route('app.ai-settings') }}" class="underline hover:text-purple-200">configure your AI API settings</a> to use the AI landing page generation feature.</span>
        </div>
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
        <form action="{{ route('app.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Product Information Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Product Information</h3>
                    <button 
                        type="button" 
                        id="aiGenerateBtn"
                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-lg transition flex items-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span id="aiGenerateBtnText">AI Generate Landing Page</span>
                    </button>
                </div>
                
                <div id="aiNotice" class="mb-4 bg-blue-500/20 border border-blue-500/50 text-blue-400 px-4 py-3 rounded-lg flex items-start gap-2 hidden">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">AI Landing Page Generation</p>
                        <p class="text-sm mt-1">Fill in the product details below, then click the button above to generate a professional landing page using AI.</p>
                    </div>
                </div>
                
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
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter product name"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter product description"
                        >{{ old('description') }}</textarea>
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
                                value="{{ old('price') }}"
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
                                value="{{ old('compare_at_price') }}"
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
                                value="{{ old('sku') }}"
                                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                placeholder="Enter SKU"
                            />
                            @error('sku')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Product Images</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Images (Multiple)</label>
                    <p class="text-xs text-gray-500 mb-3">First image will be set as main image by default. You can change it later.</p>
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

            <!-- Landing Page Sections (Image with Description) -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <div class="mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Landing Page Sections</h3>
                        <p class="text-xs text-gray-500 mt-1">Auto-created from uploaded images. AI will generate titles & descriptions when you enable AI Landing Page Generation.</p>
                    </div>
                </div>
                
                <div id="landingSectionsContainer" class="space-y-4">
                    <div class="text-center py-8 text-gray-500 text-sm" id="noSectionsMessage">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Upload images above to auto-create sections
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
                                {{ old('is_featured') ? 'checked' : '' }}
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
                    Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        let sectionCounter = 0;
        let uploadedFiles = [];

        function previewImages(event) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('landingSectionsContainer');
            preview.innerHTML = '';
            container.innerHTML = '';
            
            const files = event.target.files;
            uploadedFiles = Array.from(files);
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs">Image ${i + 1}${i === 0 ? ' (Hero)' : ''}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                    
                    // Auto-create landing section for each image (except first one which is hero)
                    if (i > 0) {
                        createAutoLandingSection(i, e.target.result);
                    }
                }
                
                reader.readAsDataURL(file);
            }
            
            sectionCounter = files.length - 1; // Exclude hero image from counter
        }

        function createAutoLandingSection(imageIndex, imageDataUrl) {
            const container = document.getElementById('landingSectionsContainer');
            const noSectionsMsg = document.getElementById('noSectionsMessage');
            if (noSectionsMsg) {
                noSectionsMsg.style.display = 'none';
            }
            
            const sectionId = imageIndex - 1; // 0-based for sections (since we skip hero)
            
            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'border border-cyan-500/30 rounded-lg p-4 bg-[#0a1628]';
            sectionDiv.id = `section-${sectionId}`;
            sectionDiv.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-cyan-400">Section ${sectionId + 1} - Image ${imageIndex + 1}</h4>
                    <span class="text-xs text-gray-500 px-2 py-1 bg-purple-900/30 rounded">AI will generate descriptions</span>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="mb-2">
                            <img src="${imageDataUrl}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        </div>
                        <input type="hidden" name="landing_sections[${sectionId}][auto_generated]" value="1">
                        <input type="hidden" name="landing_sections[${sectionId}][image_index]" value="${imageIndex}">
                    </div>
                    <div class="bg-purple-500/10 border border-purple-500/30 rounded p-3 text-center">
                        <svg class="w-8 h-8 text-purple-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="text-xs text-purple-300">AI will generate titles and descriptions in French, English, and Arabic for this image when you enable AI Landing Page Generation</p>
                    </div>
                </div>
            `;
            
            container.appendChild(sectionDiv);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const generateToggle = document.getElementById('generate_landing_page');
            const aiNotice = document.getElementById('aiNotice');
            
            generateToggle.addEventListener('change', function() {
                if (this.checked) {
                    aiNotice.classList.remove('hidden');
                } else {
                    aiNotice.classList.add('hidden');
                }
            });

            const aiGenerateBtn = document.getElementById('aiGenerateBtn');
            const aiGenerateBtnText = document.getElementById('aiGenerateBtnText');
            
            aiGenerateBtn.addEventListener('click', function() {
                const nameField = document.getElementById('name');
                const descriptionField = document.getElementById('description');
                const categoryField = document.getElementById('category_id');
                const priceField = document.getElementById('price');
                
                if (!nameField.value || !priceField.value) {
                    alert('Please fill in at least the product name and price before generating the landing page.');
                    return;
                }
                
                const generateToggle = document.getElementById('generate_landing_page');
                generateToggle.checked = true;
                aiNotice.classList.remove('hidden');
                
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3';
                notification.innerHTML = `
                    <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <div>
                        <p class="font-semibold">AI Landing Page Enabled</p>
                        <p class="text-sm">Your landing page will be generated when you create the product</p>
                    </div>
                `;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 4000);
            });
        });
    </script>
@endsection
