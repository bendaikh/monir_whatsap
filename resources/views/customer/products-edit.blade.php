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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="Enter product description"
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
                </div>
            </div>

            <!-- Current Images Card -->
            @if($product->images && count($product->images) > 0)
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Current Images</h3>
                <p class="text-sm text-gray-400 mb-4">Check the images you want to delete</p>
                
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $index => $image)
                    <div class="relative group">
                        <img src="{{ Storage::url($image) }}" alt="Product image" class="w-full h-32 object-cover rounded-lg border border-white/10" />
                        <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center cursor-pointer">
                            <input type="checkbox" name="delete_images[]" value="{{ $image }}" class="w-5 h-5 text-red-500 rounded focus:ring-red-500">
                            <span class="text-white text-xs ml-2">Delete</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

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
                                    <img src="{{ \Storage::url($section['image']) }}" class="w-full h-32 object-cover rounded-lg border border-white/10" />
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

            <!-- Settings Card -->
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-6">Settings</h3>
                
                <div class="space-y-4">
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
@endsection
