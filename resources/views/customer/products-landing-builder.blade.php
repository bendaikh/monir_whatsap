@extends('layouts.landing-builder')

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@php
    $td = $product->theme_data ?? [];
    $defaultTheme = [
        'promo_badge' => '-50% OFF TODAY',
        'promo_badge_color' => 'red',
        'cta_text' => 'ORDER NOW',
        'cta_color' => 'orange',
        'cta_bg_color' => '',
        'marquee_bg_color' => '#000000',
        'hero_bg_color' => '',
        'stats_bg_color' => '#dc2626',
        'features_bg_color' => '#111827',
        'testimonials_bg_color' => '#fffbeb',
        'trust_bg_color' => '#ffffff',
        'title_color' => '#ffffff',
        'title_background_color' => '',
        'title_font' => 'bebas',
        'title_size' => 'large',
        'stats_customers' => '325',
        'stats_rating' => '4.8',
        'stats_reviews' => '127',
        'header_items' => [['emoji' => '🔥', 'text' => '-50% OFF TODAY']],
        'features' => [],
        'trust_badges' => [['emoji' => '🚚', 'text' => 'Free Shipping']],
        'reviewer_names' => [
            ['name' => 'Karim', 'city' => 'Casablanca'],
            ['name' => 'Fatima', 'city' => 'Rabat'],
            ['name' => 'Youssef', 'city' => 'Marrakech'],
        ],
        'builder_sections' => [
            'marquee' => true, 'stats' => true, 'image_sections' => true,
            'features' => true, 'cta' => true, 'testimonials' => true, 'trust_badges' => true,
        ],
    ];
    $themeData = array_replace_recursive($defaultTheme, $td);
    $sections = $product->landing_page_sections ?? [];
    // Merge image section titles from translations into sections for editor
    foreach ($enabledLanguages as $lang) {
        $imgSections = $translations[$lang]['image_sections'] ?? [];
        foreach ($imgSections as $i => $imgSec) {
            if (!isset($sections[$i])) {
                $sections[$i] = ['image' => null];
            }
            $sections[$i]["title_{$lang}"] = $imgSec['title'] ?? ($sections[$i]["title_{$lang}"] ?? '');
            $sections[$i]["description_{$lang}"] = $imgSec['description'] ?? ($sections[$i]["description_{$lang}"] ?? '');
        }
    }
    $mainImage = $product->first_image ?? ($product->all_images[0] ?? null);
    $heroImagePath = $product->main_image ?? (($product->images ?? [])[0] ?? null);
    if (!$heroImagePath && !empty($product->ai_generated_images)) {
        $heroImagePath = $product->ai_generated_images[0];
    }
    $previewUrl = $store ? ($store->domain ? 'https://' . $store->domain . '/product/' . $product->slug : route('store.product.show', [$store->subdomain, $product->slug])) : null;
    $sectionColors = ['bg-gray-900', 'bg-red-500', 'bg-amber-400', 'bg-emerald-600', 'bg-indigo-600'];
@endphp
<style>
    .lb-grid { display: grid; grid-template-columns: 400px 1fr; height: 100vh; }
    @media (max-width: 1100px) { .lb-grid { grid-template-columns: 1fr; grid-template-rows: auto 1fr; height: auto; min-height: 100vh; } }
    .lb-editor { overflow-y: auto; height: 100vh; border-right: 1px solid rgba(255,255,255,0.08); }
    .lb-preview { overflow-y: auto; height: 100vh; background: #e8e8e3; }
    .lb-preview-inner { max-width: 420px; margin: 0 auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    .preview-block { cursor: pointer; transition: outline 0.15s; outline: 2px solid transparent; outline-offset: -2px; }
    .preview-block:hover, .preview-block.ring-active { outline-color: #8b5cf6; }
    .tab-btn.active { background: #7c3aed; color: white; }
    .ql-container { min-height: 100px; background: white; color: #111; }
</style>
@endpush

@section('content')
<div x-data="landingBuilder()" class="lb-grid">
    <!-- Editor -->
    <aside class="lb-editor bg-[#0a1628] flex flex-col">
        <div class="p-3 border-b border-white/10 flex items-center justify-between gap-2 flex-shrink-0">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('app.landing-builder') }}" class="text-gray-400 hover:text-white text-sm">←</a>
                <span class="text-white font-bold text-sm truncate">{{ $product->name }}</span>
            </div>
            <div class="flex gap-1 flex-shrink-0">
                <template x-for="lang in enabledLangs" :key="lang">
                    <button @click="currentLang = lang" :class="currentLang === lang ? 'bg-violet-600 text-white' : 'text-gray-500'"
                            class="px-2 py-1 rounded text-xs font-bold uppercase" x-text="lang"></button>
                </template>
            </div>
        </div>

        <div class="p-2 border-b border-white/10 flex flex-wrap gap-1 flex-shrink-0">
            <template x-for="tab in tabs" :key="tab.id">
                <button @click="activeTab = tab.id" :class="activeTab === tab.id ? 'tab-btn active' : 'bg-white/5 text-gray-400'"
                        class="px-2 py-1 rounded text-xs font-semibold" x-text="tab.label"></button>
            </template>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 text-sm">
            <!-- Sections visibility -->
            <div x-show="activeTab === 'sections'" class="space-y-2">
                <p class="text-gray-500 text-xs mb-2">Click a block in the preview to jump here. Toggle visibility:</p>
                <template x-for="(label, key) in sectionLabels" :key="key">
                    <label class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2 cursor-pointer">
                        <span class="text-gray-200" x-text="label"></span>
                        <input type="checkbox" class="rounded" :checked="builderSections[key]" @change="builderSections[key] = $event.target.checked">
                    </label>
                </template>
            </div>

            <!-- Theme -->
            <div x-show="activeTab === 'theme'" class="space-y-3">
                <div><label class="text-gray-400 text-xs">Promo badge</label>
                    <input x-model="themeData.promo_badge" class="w-full mt-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-gray-400 text-xs">Badge color</label>
                        <select x-model="themeData.promo_badge_color" class="w-full mt-1 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white">
                            <option value="red">Red</option><option value="orange">Orange</option><option value="green">Green</option><option value="blue">Blue</option><option value="purple">Purple</option>
                        </select></div>
                    <div><label class="text-gray-400 text-xs">CTA color</label>
                        <select x-model="themeData.cta_color" class="w-full mt-1 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white">
                            <option value="orange">Orange</option><option value="green">Green</option><option value="red">Red</option><option value="blue">Blue</option>
                        </select></div>
                </div>
                <div><label class="text-gray-400 text-xs">CTA button text</label>
                    <input x-model="themeData.cta_text" class="w-full mt-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></div>
                <div><label class="text-gray-400 text-xs">CTA Banner Background (Optional)</label>
                    <input type="color" x-model="themeData.cta_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-gray-400 text-xs">Title color</label>
                        <input type="color" x-model="themeData.title_color" class="w-full h-9 mt-1 rounded"></div>
                    <div><label class="text-gray-400 text-xs">Title background</label>
                        <input type="color" x-model="themeData.title_background_color" class="w-full h-9 mt-1 rounded"></div>
                </div>
            </div>

            <!-- Hero -->
            <div x-show="activeTab === 'hero'" class="space-y-3">
                <div>
                    <label class="text-gray-400 text-xs">Hero image</label>
                    <label class="mt-1 block border border-dashed border-white/20 rounded p-3 text-center text-gray-500 text-xs cursor-pointer">
                        <img x-show="heroImage" :src="imgUrl(heroImage)" class="max-h-32 mx-auto mb-2 rounded-lg object-cover">
                        <span x-text="heroImage ? 'Change hero image' : 'Upload hero image'"></span>
                        <input type="file" accept="image/*" class="hidden" @change="uploadHeroImage($event)">
                    </label>
                    <p class="text-gray-500 text-xs mt-1">This is the main product image shown at the top of the hero section.</p>
                </div>
                <div><label class="text-gray-400 text-xs">Hero title (<span x-text="currentLang"></span>)</label>
                    <input :value="getPageField('hero_title')" @input="setPageField('hero_title', $event.target.value)"
                           class="w-full mt-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white font-bold"></div>
                <div><label class="text-gray-400 text-xs">Hero description</label>
                    <textarea rows="2" :value="getPageField('hero_description')" @input="setPageField('hero_description', $event.target.value)"
                              class="w-full mt-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></textarea></div>
                <div><label class="text-gray-400 text-xs">Hero Background Color (Optional)</label>
                    <input type="color" x-model="themeData.hero_bg_color" class="w-full h-9 mt-1 rounded"></div>
            </div>

            <!-- Marquee -->
            <div x-show="activeTab === 'marquee'" class="space-y-2">
                <div><label class="text-gray-400 text-xs">Marquee Background</label>
                    <input type="color" x-model="themeData.marquee_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <template x-for="(item, i) in themeData.header_items" :key="i">
                    <div class="flex gap-2">
                        <input x-model="themeData.header_items[i].emoji" class="w-12 text-center bg-[#0f1c2e] border border-white/10 rounded text-white">
                        <input x-model="themeData.header_items[i].text" class="flex-1 px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white">
                    </div>
                </template>
                <button @click="themeData.header_items.push({emoji:'✨',text:''})" class="text-violet-400 text-xs">+ Add item</button>
            </div>

            <!-- Stats -->
            <div x-show="activeTab === 'stats'" class="space-y-3">
                <div><label class="text-gray-400 text-xs">Stats Background Color</label>
                    <input type="color" x-model="themeData.stats_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="text-gray-400 text-xs">Customers</label><input x-model="themeData.stats_customers" class="w-full mt-1 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></div>
                    <div><label class="text-gray-400 text-xs">Rating</label><input x-model="themeData.stats_rating" class="w-full mt-1 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></div>
                    <div><label class="text-gray-400 text-xs">Reviews</label><input x-model="themeData.stats_reviews" class="w-full mt-1 px-2 py-2 bg-[#0f1c2e] border border-white/10 rounded text-white"></div>
                </div>
            </div>

            <!-- Image sections (the big banners on your page) -->
            <div x-show="activeTab === 'blocks'" class="space-y-4">
                <label class="flex items-center gap-2 text-gray-300">
                    <input type="checkbox" x-model="showProductSections" class="rounded"> Show content blocks
                </label>
                <template x-for="(section, index) in sections" :key="index">
                    <div class="bg-white/5 border border-white/10 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-violet-300 font-bold text-xs">Block <span x-text="index + 1"></span></span>
                            <button @click="sections.splice(index, 1)" class="text-red-400 text-xs">Remove</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="text-gray-400 text-xs">Band Color</label>
                                <input type="color" x-model="section.band_color" class="w-full h-8 mt-1 rounded"></div>
                            <div><label class="text-gray-400 text-xs">Content Bg</label>
                                <input type="color" x-model="section.bg_color" class="w-full h-8 mt-1 rounded"></div>
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs">Banner title (uppercase bar)</label>
                            <input x-model="section['title_' + currentLang]" class="w-full mt-1 px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white">
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs">Image</label>
                            <label class="mt-1 block border border-dashed border-white/20 rounded p-3 text-center text-gray-500 text-xs cursor-pointer">
                                <img x-show="section.image" :src="imgUrl(section.image)" class="max-h-24 mx-auto mb-1 rounded">
                                <span x-text="section.image ? 'Change image' : 'Upload image'"></span>
                                <input type="file" accept="image/*" class="hidden" @change="uploadImage($event, index)">
                            </label>
                        </div>
                        <div>
                            <label class="text-gray-400 text-xs">Description below image</label>
                            <textarea rows="3" x-model="section['description_' + currentLang]" class="w-full mt-1 px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white"></textarea>
                        </div>
                    </div>
                </template>
                <button @click="addSection()" class="w-full py-2 border border-dashed border-violet-500/50 text-violet-400 rounded-lg text-sm">+ Add content block</button>
            </div>

            <!-- AI Features (Why choose section) -->
            <div x-show="activeTab === 'features'" class="space-y-3">
                <div><label class="text-gray-400 text-xs">Features Background Color</label>
                    <input type="color" x-model="themeData.features_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <p class="text-gray-500 text-xs">These appear in the "Why choose" section. Leave empty to use theme defaults.</p>
                <template x-for="(feat, i) in getFeatures()" :key="i">
                    <div class="bg-white/5 rounded-lg p-3 space-y-2 border border-white/10">
                        <input x-model="feat.title" @input="updateFeature(i, 'title', feat.title)" placeholder="Title" class="w-full px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white font-bold">
                        <textarea rows="2" x-model="feat.description" @input="updateFeature(i, 'description', feat.description)" placeholder="Description" class="w-full px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white"></textarea>
                        <input x-model="feat.icon" @input="updateFeature(i, 'icon', feat.icon)" placeholder="Icon emoji" class="w-20 px-2 py-1 bg-[#0f1c2e] border border-white/10 rounded text-white text-center">
                    </div>
                </template>
                <button @click="addFeature()" class="text-violet-400 text-xs">+ Add feature</button>
                <hr class="border-white/10">
                <p class="text-gray-500 text-xs">Or use simple feature chips (fallback):</p>
                <template x-for="(feat, i) in themeData.features" :key="'s'+i">
                    <input x-model="themeData.features[i].text" placeholder="Short feature text" class="w-full px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white">
                </template>
            </div>

            <!-- Testimonials -->
            <div x-show="activeTab === 'testimonials'" class="space-y-2">
                <div><label class="text-gray-400 text-xs">Testimonials Background Color</label>
                    <input type="color" x-model="themeData.testimonials_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <template x-for="(rev, i) in themeData.reviewer_names" :key="i">
                    <div class="grid grid-cols-2 gap-2">
                        <input x-model="themeData.reviewer_names[i].name" placeholder="Name" class="px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white">
                        <input x-model="themeData.reviewer_names[i].city" placeholder="City" class="px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white">
                    </div>
                </template>
            </div>

            <!-- Trust badges -->
            <div x-show="activeTab === 'trust'" class="space-y-2">
                <div><label class="text-gray-400 text-xs">Trust Badges Background Color</label>
                    <input type="color" x-model="themeData.trust_bg_color" class="w-full h-9 mt-1 rounded"></div>
                <template x-for="(badge, i) in themeData.trust_badges" :key="i">
                    <div class="flex gap-2">
                        <input x-model="themeData.trust_badges[i].emoji" class="w-12 text-center bg-[#0f1c2e] border border-white/10 rounded text-white">
                        <input x-model="themeData.trust_badges[i].text" class="flex-1 px-2 py-1.5 bg-[#0f1c2e] border border-white/10 rounded text-white">
                    </div>
                </template>
                <button @click="themeData.trust_badges.push({emoji:'✅',text:''})" class="text-violet-400 text-xs">+ Add badge</button>
            </div>
        </div>

        <div class="p-3 border-t border-white/10 flex gap-2 flex-shrink-0">
            @if($previewUrl)
            <a href="{{ $previewUrl }}" target="_blank" class="flex-1 py-2.5 bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg text-center">Preview Live ↗</a>
            @endif
            <button @click="save()" :disabled="saving" class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white text-sm font-bold rounded-lg">
                <span x-text="saving ? 'Saving…' : 'Save Changes'"></span>
            </button>
        </div>
        <div x-show="saved" x-transition class="absolute bottom-20 left-4 right-4 bg-emerald-500 text-white text-center py-2 rounded-lg text-sm">✓ Saved!</div>
    </aside>

    <!-- Live preview (matches storefront layout) -->
    <main class="lb-preview p-4 md:p-6">
        <p class="text-center text-gray-600 text-xs font-semibold uppercase tracking-wider mb-3">Live Preview — click a section to edit</p>
        <div class="lb-preview-inner bg-[#f5f5f0] rounded-lg overflow-hidden">

            <!-- Marquee -->
            <div x-show="builderSections.marquee" @click="activeTab = 'marquee'" class="preview-block text-white text-[10px] font-bold py-1.5 overflow-hidden" :class="activeTab === 'marquee' ? 'ring-active' : ''" :style="'background-color:'+themeData.marquee_bg_color">
                <div class="whitespace-nowrap animate-marquee inline-block">
                    <template x-for="item in themeData.header_items"><span x-show="item.text" class="mx-4" x-text="(item.emoji||'')+' '+item.text"></span></template>
                </div>
            </div>

            <!-- Hero -->
            <div @click="activeTab = 'hero'" class="preview-block p-4 text-white stripe-bg" :class="[activeTab === 'hero' ? 'ring-active' : '']" :style="heroStyle()">
                <div class="text-center mb-2"><span class="bg-yellow-300 text-gray-900 text-[10px] font-extrabold px-2 py-0.5 rounded-full" x-text="themeData.promo_badge"></span></div>
                <img x-show="heroImage" :src="imgUrl(heroImage)" class="w-full h-32 object-cover rounded-lg mb-2 shadow">
                <h1 class="font-black uppercase text-center text-xl mb-1" :style="'color:'+themeData.title_color" x-text="getPageField('hero_title') || @js($product->name)"></h1>
                <p class="text-center text-xs opacity-90 mb-2" x-text="getPageField('hero_description')"></p>
                <div class="bg-white/95 rounded-xl p-3 text-center text-gray-800">
                    <div class="text-red-600 font-black">{{ number_format($product->price, 0) }} {{ $product->landing_page_currency ?? 'MAD' }}</div>
                    <button type="button" class="mt-2 w-full py-2 rounded-lg text-white font-black text-xs uppercase" :class="ctaBtn()" x-text="themeData.cta_text"></button>
                </div>
            </div>

            <!-- Stats -->
            <div x-show="builderSections.stats" @click="activeTab = 'stats'" class="preview-block text-white py-3 grid grid-cols-3 text-center text-[10px] stripe-bg" :class="activeTab === 'stats' ? 'ring-active' : ''" :style="'background-color:'+themeData.stats_bg_color">
                <div><div class="font-black text-lg text-yellow-300" x-text="themeData.stats_customers+'+'"></div>Clients</div>
                <div class="border-x border-white/20"><div class="font-black text-lg text-yellow-300" x-text="themeData.stats_rating"></div>Rating</div>
                <div><div class="font-black text-lg text-yellow-300" x-text="themeData.stats_reviews"></div>Reviews</div>
            </div>

            <!-- Image content blocks -->
            <template x-if="builderSections.image_sections && showProductSections">
                <template x-for="(section, idx) in sections" :key="'sec'+idx">
                    <div>
                        <div @click="activeTab = 'blocks'; selectedBlock = idx" class="preview-block py-3 text-center text-white font-black uppercase text-sm stripe-bg"
                             :class="[activeTab === 'blocks' && selectedBlock === idx ? 'ring-active' : '']"
                             :style="'background-color:'+(section.band_color || bandColors[idx % bandColors.length].replace('bg-', ''))"
                             x-text="section['title_' + currentLang] || 'Section title'"></div>
                        <div @click="activeTab = 'blocks'; selectedBlock = idx" class="preview-block p-3" :class="activeTab === 'blocks' && selectedBlock === idx ? 'ring-active' : ''" :style="'background-color:'+(section.bg_color || '#ffffff')">
                            <img x-show="section.image" :src="imgUrl(section.image)" class="w-full rounded-lg mb-2">
                            <p class="text-gray-700 text-xs text-center" x-text="section['description_' + currentLang]"></p>
                        </div>
                    </div>
                </template>
            </template>

            <!-- Features -->
            <div x-show="builderSections.features" @click="activeTab = 'features'" class="preview-block text-white p-4 stripe-bg" :class="activeTab === 'features' ? 'ring-active' : ''" :style="'background-color:'+themeData.features_bg_color">
                <h3 class="font-black text-center uppercase text-yellow-300 text-xs mb-2">Why choose?</h3>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="feat in displayFeatures()">
                        <div class="bg-white text-gray-900 rounded-lg p-2 text-center text-[9px]">
                            <div class="text-lg mb-0.5" x-text="feat.icon || '✨'"></div>
                            <div class="font-bold" x-text="feat.title || feat.text"></div>
                            <div class="text-gray-500" x-text="feat.description"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- CTA -->
            <div x-show="builderSections.cta" @click="activeTab = 'theme'" class="preview-block p-4 text-center text-white stripe-bg" :class="[activeTab === 'theme' ? 'ring-active' : '']" :style="ctaStyle()">
                <p class="text-xs font-bold mb-2" x-text="themeData.promo_badge"></p>
                <button type="button" class="bg-white text-gray-900 font-black px-4 py-2 rounded-xl text-xs" x-text="'➤ '+themeData.cta_text"></button>
            </div>

            <!-- Testimonials -->
            <div x-show="builderSections.testimonials" @click="activeTab = 'testimonials'" class="preview-block p-3" :class="activeTab === 'testimonials' ? 'ring-active' : ''" :style="'background-color:'+themeData.testimonials_bg_color">
                <h3 class="font-black text-center text-xs mb-2">Testimonials</h3>
                <div class="grid grid-cols-3 gap-1">
                    <template x-for="rev in themeData.reviewer_names">
                        <div class="bg-white rounded p-1.5 text-center shadow-sm text-[9px]">
                            <div class="font-bold text-gray-900" x-text="rev.name"></div><div class="text-gray-500" x-text="rev.city"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Trust -->
            <div x-show="builderSections.trust_badges" @click="activeTab = 'trust'" class="preview-block p-2 grid grid-cols-2 gap-1" :class="activeTab === 'trust' ? 'ring-active' : ''" :style="'background-color:'+themeData.trust_bg_color">
                <template x-for="b in themeData.trust_badges">
                    <div x-show="b.text" class="flex items-center gap-1 bg-gray-50 border-l-2 border-emerald-500 px-2 py-1 text-[9px] font-bold">
                        <span x-text="b.emoji"></span><span class="text-gray-900" x-text="b.text"></span>
                    </div>
                </template>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<style>@keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}.animate-marquee{animation:marquee 12s linear infinite}.stripe-bg{background-image:repeating-linear-gradient(45deg,rgba(255,255,255,.08) 0,rgba(255,255,255,.08) 8px,transparent 8px,transparent 16px)}</style>
<script>
function landingBuilder() {
    return {
        currentLang: @json($product->getDefaultLanguageCode()),
        enabledLangs: @json($enabledLanguages),
        activeTab: 'blocks',
        selectedBlock: 0,
        saving: false,
        saved: false,
        heroImage: @json($heroImagePath),
        showProductSections: @json(($translations[$product->getDefaultLanguageCode()] ?? [])['show_product_sections'] ?? true),
        sections: @json($sections),
        pageData: @json($translations),
        themeData: @json($themeData),
        builderSections: {},
        bandColors: @json($sectionColors),
        tabs: [
            {id:'sections',label:'Sections'},{id:'theme',label:'Colors'},{id:'hero',label:'Hero'},
            {id:'marquee',label:'Marquee'},{id:'stats',label:'Stats'},{id:'blocks',label:'Content Blocks'},
            {id:'features',label:'Features'},{id:'testimonials',label:'Reviews'},{id:'trust',label:'Badges'},
        ],
        sectionLabels: {
            marquee:'Top marquee', stats:'Stats bar', image_sections:'Content blocks (images)',
            features:'Why choose', cta:'CTA banner', testimonials:'Testimonials', trust_badges:'Trust badges',
        },
        heroMap: {red:'from-red-500 to-red-700',orange:'from-orange-500 to-amber-600',green:'from-emerald-500 to-green-700',blue:'from-blue-500 to-indigo-700',purple:'from-purple-500 to-fuchsia-700'},
        ctaMap: {orange:'from-orange-500 to-red-600',green:'from-green-500 to-emerald-600',red:'from-red-500 to-rose-600',blue:'from-blue-500 to-indigo-600'},
        init() {
            this.builderSections = {...(this.themeData.builder_sections || {})};
            ['marquee','stats','image_sections','features','cta','testimonials','trust_badges'].forEach(k => {
                if (this.builderSections[k] === undefined) this.builderSections[k] = true;
            });
            // Ensure sections have default colors if missing
            const defaultBands = ['#111827', '#ef4444', '#fbbf24', '#059669', '#4f46e5'];
            this.sections.forEach((s, i) => {
                if (!s.bg_color) s.bg_color = '#ffffff';
                if (!s.band_color) s.band_color = defaultBands[i % defaultBands.length];
            });
        },
        getPageField(key) {
            return this.pageData[this.currentLang]?.[key] || '';
        },
        setPageField(key, val) {
            if (!this.pageData[this.currentLang]) this.pageData[this.currentLang] = {};
            this.pageData[this.currentLang][key] = val;
        },
        getFeatures() {
            const f = this.pageData[this.currentLang]?.features;
            if (f && f.length) return f;
            return this.pageData['en']?.features || this.pageData['fr']?.features || [];
        },
        updateFeature(i, key, val) {
            if (!this.pageData[this.currentLang]) this.pageData[this.currentLang] = {};
            if (!this.pageData[this.currentLang].features) this.pageData[this.currentLang].features = [];
            if (!this.pageData[this.currentLang].features[i]) this.pageData[this.currentLang].features[i] = {};
            this.pageData[this.currentLang].features[i][key] = val;
        },
        addFeature() {
            if (!this.pageData[this.currentLang]) this.pageData[this.currentLang] = {};
            if (!this.pageData[this.currentLang].features) this.pageData[this.currentLang].features = [];
            this.pageData[this.currentLang].features.push({title:'',description:'',icon:'✨'});
        },
        displayFeatures() {
            const ai = this.getFeatures().filter(f => f.title || f.text);
            if (ai.length) return ai;
            return (this.themeData.features || []).filter(f => f.text);
        },
        heroGrad() { return 'bg-gradient-to-br ' + (this.heroMap[this.themeData.promo_badge_color] || this.heroMap.orange); },
        heroStyle() {
            if (this.themeData.hero_bg_color) return 'background-color:' + this.themeData.hero_bg_color;
            return '';
        },
        ctaGrad() { return 'bg-gradient-to-r ' + (this.ctaMap[this.themeData.cta_color] || this.ctaMap.orange); },
        ctaStyle() {
            if (this.themeData.cta_bg_color) return 'background-color:' + this.themeData.cta_bg_color;
            return '';
        },
        ctaBtn() { const m={orange:'bg-orange-500',green:'bg-emerald-500',red:'bg-red-500',blue:'bg-blue-500'}; return m[this.themeData.cta_color]||m.orange; },
        bandColor(i) { return this.bandColors[i % this.bandColors.length] + ' text-white'; },
        imgUrl(p) {
            if (!p) return '';
            if (p.startsWith('http')) return p;
            if (p.startsWith('/storage/') || p.startsWith('/')) return p;
            if (p.startsWith('storage/')) return '/' + p;
            return '/storage/' + p;
        },
        addSection() {
            this.sections.push({title_fr:'',description_fr:'',title_en:'',description_en:'',title_ar:'',description_ar:'',image:null,bg_color:'#ffffff',band_color:'#111827'});
        },
        uploadImage(ev, idx) {
            const fd = new FormData(); fd.append('image', ev.target.files[0]);
            fetch(@json(route('app.products.upload-image', $product->id)), {method:'POST',headers:{'X-CSRF-TOKEN':@json(csrf_token())},body:fd})
                .then(r=>r.json()).then(d=>{ if(d.success) this.sections[idx].image = d.path; });
        },
        uploadHeroImage(ev) {
            const file = ev.target.files[0];
            if (!file) return;
            const fd = new FormData(); fd.append('image', file);
            fetch(@json(route('app.products.upload-image', $product->id)), {method:'POST',headers:{'X-CSRF-TOKEN':@json(csrf_token())},body:fd})
                .then(r=>r.json()).then(d=>{
                    if (!d.success) return;
                    fetch(@json(route('app.products.set-main-image', $product->id)), {
                        method:'POST',
                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':@json(csrf_token())},
                        body: JSON.stringify({image_path: d.path})
                    }).then(r=>r.json()).then(res=>{
                        if (res.success) this.heroImage = d.path;
                    });
                });
        },
        save() {
            this.saving = true;
            this.themeData.builder_sections = this.builderSections;
            const pd = {};
            this.enabledLangs.forEach(lang => {
                pd[lang] = this.pageData[lang] || {};
                pd[lang].show_product_sections = this.showProductSections;
            });
            fetch(@json(route('app.products.save-landing-builder', $product->id)), {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':@json(csrf_token())},
                body: JSON.stringify({
                    sections: this.sections,
                    page_data: pd,
                    theme_data: this.themeData,
                    builder_sections: this.builderSections,
                    show_product_sections: this.showProductSections,
                })
            }).then(r=>r.json()).then(d=>{
                if(d.success){ this.saved=true; setTimeout(()=>this.saved=false,2500); }
                else alert('Save failed');
            }).finally(()=>this.saving=false);
        }
    };
}
</script>
@endpush
