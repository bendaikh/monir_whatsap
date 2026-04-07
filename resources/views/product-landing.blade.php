<!DOCTYPE html>
<html lang="fr" class="scroll-smooth" x-data="{ currentLang: 'fr' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }}</title>
    <meta name="description" content="{{ Str::limit($product->description, 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .rtl { direction: rtl; font-family: 'Cairo', sans-serif; }
        .section-divider {
            height: 40px;
            background: linear-gradient(180deg, #1e40af 0%, #3b82f6 100%);
        }
    </style>
</head>
<body class="antialiased bg-white" :class="{'rtl': currentLang === 'ar'}">
    @php
        // Prepare default testimonials in PHP to avoid JavaScript issues
        $defaultTestimonials = [
            'fr' => [
                ['name' => 'Ahmed', 'text' => "J'ai commandé ce produit et je suis vraiment impressionné par la qualité. Le service client était excellent et la livraison très rapide. Je recommande vivement!", 'rating' => 5],
                ['name' => 'Fatima', 'text' => "Exactement ce que je recherchais! Le rapport qualité-prix est imbattable. Mes amies m'ont déjà demandé où je l'ai acheté. Très satisfaite de mon achat!", 'rating' => 5],
                ['name' => 'Hassan', 'text' => "Produit conforme à la description. L'équipe a été très professionnelle du début à la fin. Je commanderai à nouveau sans hésiter. Merci beaucoup!", 'rating' => 5],
            ],
            'en' => [
                ['name' => 'Ahmed', 'text' => "I ordered this product and I'm really impressed with the quality. Customer service was excellent and delivery was very fast. Highly recommend!", 'rating' => 5],
                ['name' => 'Fatima', 'text' => "Exactly what I was looking for! The value for money is unbeatable. My friends already asked me where I bought it. Very satisfied with my purchase!", 'rating' => 5],
                ['name' => 'Hassan', 'text' => "Product matches the description. The team was very professional from start to finish. I will order again without hesitation. Thank you so much!", 'rating' => 5],
            ],
            'ar' => [
                ['name' => 'أحمد', 'text' => "طلبت هذا المنتج وأنا معجب جداً بالجودة. خدمة العملاء كانت ممتازة والتوصيل سريع جداً. أنصح به بشدة!", 'rating' => 5],
                ['name' => 'فاطمة', 'text' => "بالضبط ما كنت أبحث عنه! القيمة مقابل المال لا تُضاهى. صديقاتي سألنني من أين اشتريته. راضية جداً عن مشترياتي!", 'rating' => 5],
                ['name' => 'حسن', 'text' => "المنتج مطابق للوصف. الفريق كان محترفاً جداً من البداية إلى النهاية. سأطلب مرة أخرى دون تردد. شكراً جزيلاً!", 'rating' => 5],
            ],
        ];

        // Function to fix testimonials
        function fixTestimonials($data, $lang, $defaults) {
            if (!$data || !is_array($data)) {
                return ['testimonials' => $defaults[$lang] ?? $defaults['fr']];
            }
            
            $testimonials = $data['testimonials'] ?? null;
            $defaultLang = $defaults[$lang] ?? $defaults['fr'];
            
            if (!$testimonials || !is_array($testimonials) || count($testimonials) === 0) {
                $data['testimonials'] = $defaultLang;
                return $data;
            }
            
            $invalidTexts = ['testimonial text', 'test', 'testimonial', 'positive testimonial quote', 'customer review', 'detailed', 'authentic'];
            
            foreach ($testimonials as $index => &$t) {
                $text = $t['text'] ?? $t['review'] ?? $t['comment'] ?? '';
                $isInvalid = empty($text) || strlen(trim($text)) < 10;
                
                if (!$isInvalid) {
                    foreach ($invalidTexts as $invalid) {
                        if (stripos($text, $invalid) !== false) {
                            $isInvalid = true;
                            break;
                        }
                    }
                }
                
                if ($isInvalid) {
                    $t['text'] = $defaultLang[$index % count($defaultLang)]['text'];
                }
                
                if (empty($t['name'])) {
                    $t['name'] = $defaultLang[$index % count($defaultLang)]['name'];
                }
                
                if (empty($t['rating'])) {
                    $t['rating'] = 5;
                }
            }
            
            $data['testimonials'] = $testimonials;
            return $data;
        }

        // Function to sanitize strings for JavaScript
        function sanitizeForJs($data) {
            if (is_string($data)) {
                // Remove control characters and normalize whitespace
                $data = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $data);
                $data = preg_replace('/\s+/', ' ', $data);
                return trim($data);
            }
            if (is_array($data)) {
                return array_map('sanitizeForJs', $data);
            }
            return $data;
        }

        // Fix all language data
        $fixedFr = sanitizeForJs(fixTestimonials($product->landing_page_fr, 'fr', $defaultTestimonials));
        $fixedEn = sanitizeForJs(fixTestimonials($product->landing_page_en, 'en', $defaultTestimonials));
        $fixedAr = sanitizeForJs(fixTestimonials($product->landing_page_ar, 'ar', $defaultTestimonials));
        
        // Sanitize product name and description for JavaScript
        $safeName = sanitizeForJs($product->name);
        $safeDescription = sanitizeForJs($product->description ?? '');
    @endphp
    <script>
        const productName = {!! json_encode($safeName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!};
        const productDescription = {!! json_encode($safeDescription, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!};
        const pageData = {
            fr: {!! json_encode($fixedFr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!},
            en: {!! json_encode($fixedEn, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!},
            ar: {!! json_encode($fixedAr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}
        };
    </script>

    <!-- Language Switcher - Fixed Top Right -->
    <div class="fixed top-6 right-6 z-50 bg-white shadow-2xl rounded-xl p-2 flex gap-2 border border-gray-200">
        <button @click="currentLang = 'fr'" 
                :class="currentLang === 'fr' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
            🇫🇷 FR
        </button>
        <button @click="currentLang = 'en'" 
                :class="currentLang === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
            🇬🇧 EN
        </button>
        <button @click="currentLang = 'ar'" 
                :class="currentLang === 'ar' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
            🇸🇦 AR
        </button>
    </div>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-700 via-blue-600 to-blue-800 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                <!-- Left Side: Title + Image + Price -->
                <div class="space-y-8">
                    <!-- Title and Description -->
                    <div class="text-white" x-cloak>
                        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-black mb-6 leading-tight drop-shadow-lg" 
                            x-text="pageData[currentLang]?.hero_title || productName">
                            {{ $product->landing_page_hero_title ?? $product->name }}
                        </h1>
                        <p class="text-lg lg:text-xl mb-8 text-white/90 leading-relaxed" 
                           x-text="pageData[currentLang]?.hero_description || productDescription">
                            {{ $product->landing_page_hero_description ?? $product->description }}
                        </p>
                    </div>

                    <!-- Product Image -->
                    @if($product->first_image)
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                        <img src="{{ $product->first_image }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-[400px] lg:h-[500px] object-cover">
                    </div>
                    @endif

                    <!-- Price Display Below Image -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-6 py-3 border border-white/30">
                            <div class="text-4xl font-black text-white">{{ number_format($product->price, 2) }} <span class="text-xl">MAD</span></div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <div class="text-sm line-through text-white/70">{{ number_format($product->compare_at_price, 2) }} MAD</div>
                            @endif
                        </div>
                        @if($product->discount_percentage)
                        <div class="bg-yellow-400 text-blue-900 px-5 py-2 rounded-xl font-black text-xl shadow-lg">
                            -{{ $product->discount_percentage }}%
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Side: Contact Form -->
                <div class="lg:sticky lg:top-8">
                    <div class="bg-white rounded-2xl p-8 lg:p-10 shadow-2xl">
                        @if (session('success'))
                            <div class="mb-6 rounded-xl border-2 border-green-500 bg-green-50 px-4 py-3 text-green-800 text-center font-semibold">
                                {{ session('success') }}
                            </div>
                        @endif

                        <h2 class="text-2xl lg:text-3xl font-black mb-6 text-gray-900 text-center" x-text="pageData[currentLang]?.form_title || 'Contactez-nous'">
                            Contactez-nous
                        </h2>

                        <form method="POST" action="{{ route('product.submit-lead', $product->slug) }}" class="space-y-6">
                            @csrf
                            <input type="hidden" name="language" x-model="currentLang">

                            <div>
                                <label class="block text-gray-900 font-bold mb-2" x-text="pageData[currentLang]?.form_name_placeholder || 'Votre nom'">
                                    Votre nom
                                </label>
                                <input type="text" 
                                       name="name" 
                                       required
                                       :placeholder="pageData[currentLang]?.form_name_placeholder || 'Entrez votre nom'"
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 focus:outline-none transition-all">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-900 font-bold mb-2" x-text="pageData[currentLang]?.form_phone_placeholder || 'Numéro de téléphone'">
                                    Numéro de téléphone
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       required
                                       :placeholder="pageData[currentLang]?.form_phone_placeholder || 'Entrez votre numéro'"
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 focus:outline-none transition-all">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-900 font-bold mb-2" x-text="pageData[currentLang]?.form_note_placeholder || 'Note (optionnel)'">
                                    Note (optionnel)
                                </label>
                                <textarea name="note" 
                                          rows="3"
                                          :placeholder="pageData[currentLang]?.form_note_placeholder || 'Ajoutez vos questions ou commentaires'"
                                          class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 focus:outline-none transition-all resize-none"></textarea>
                                @error('note')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-black text-lg rounded-xl transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span x-text="pageData[currentLang]?.form_submit_button || 'Envoyer'">
                                    Envoyer
                                </span>
                            </button>
                        </form>

                        <!-- Alternative Contact Methods -->
                        <div class="mt-8 pt-8 border-t-2 border-gray-200">
                            <p class="text-gray-700 text-center mb-4 font-semibold">
                                <span x-show="currentLang === 'fr'">Ou contactez-nous directement :</span>
                                <span x-show="currentLang === 'en'">Or contact us directly:</span>
                                <span x-show="currentLang === 'ar'">أو اتصل بنا مباشرة:</span>
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->user->phone ?? '') }}?text={{ urlencode('Hello, I am interested in ' . $product->name) }}" 
                                   target="_blank" 
                                   class="flex items-center justify-center gap-2 px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span class="text-sm">WhatsApp</span>
                                </a>
                                
                                <a href="tel:{{ $product->user->phone ?? '' }}" 
                                   class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-sm" x-show="currentLang === 'fr'">Appeler</span>
                                    <span class="text-sm" x-show="currentLang === 'en'">Call</span>
                                    <span class="text-sm" x-show="currentLang === 'ar'">اتصل</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Divider -->
    <div class="section-divider"></div>

    <!-- Landing Page Sections (Image with Description) - BEFORE Features -->
    @if($product->landing_page_sections && count($product->landing_page_sections) > 0)
    <section class="py-16 lg:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto space-y-12">
                @foreach($product->landing_page_sections as $index => $section)
                <div class="grid md:grid-cols-2 gap-8 items-center {{ $index % 2 == 1 ? 'md:flex-row-reverse' : '' }}">
                    <!-- Image -->
                    <div class="relative {{ $index % 2 == 1 ? 'md:order-2' : '' }}">
                        @if(!empty($section['image']))
                        <div class="rounded-2xl overflow-hidden shadow-xl">
                            <img src="{{ \Storage::url($section['image']) }}" 
                                 alt="{{ $section['title_fr'] ?? 'Product feature' }}" 
                                 class="w-full h-[350px] object-cover">
                        </div>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="{{ $index % 2 == 1 ? 'md:order-1' : '' }}" x-cloak>
                        <h3 class="text-2xl lg:text-3xl font-black mb-4 text-gray-900">
                            <span x-show="currentLang === 'fr'">{{ $section['title_fr'] ?? '' }}</span>
                            <span x-show="currentLang === 'en'">{{ $section['title_en'] ?? $section['title_fr'] ?? '' }}</span>
                            <span x-show="currentLang === 'ar'">{{ $section['title_ar'] ?? $section['title_fr'] ?? '' }}</span>
                        </h3>
                        <p class="text-gray-600 text-lg leading-relaxed">
                            <span x-show="currentLang === 'fr'">{{ $section['description_fr'] ?? '' }}</span>
                            <span x-show="currentLang === 'en'">{{ $section['description_en'] ?? $section['description_fr'] ?? '' }}</span>
                            <span x-show="currentLang === 'ar'">{{ $section['description_ar'] ?? $section['description_fr'] ?? '' }}</span>
                        </p>
                    </div>
                </div>
                @if($index < count($product->landing_page_sections) - 1)
                <div class="border-t border-gray-200"></div>
                @endif
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Section Divider -->
    <div class="section-divider"></div>
    @endif

    <!-- Features Section -->
    <section class="py-16 lg:py-20 bg-gray-50" x-cloak>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">
                    <span x-show="currentLang === 'fr'">المميزات / Les Caractéristiques</span>
                    <span x-show="currentLang === 'en'">Features</span>
                    <span x-show="currentLang === 'ar'">المميزات</span>
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <template x-for="(feature, index) in (pageData[currentLang]?.features || [])" :key="index">
                    <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl" x-text="feature.icon">✓</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900" x-text="feature.title"></h3>
                        <p class="text-gray-600 leading-relaxed" x-text="feature.description"></p>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Steps Section (How It Works) -->
    <section class="py-16 lg:py-20 bg-white" x-cloak>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4" x-text="pageData[currentLang]?.steps_title || 'Comment ça marche'"></h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <template x-for="(step, index) in (pageData[currentLang]?.steps || [])" :key="index">
                    <div class="relative">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <span class="text-3xl font-black text-white" x-text="step.number"></span>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-gray-900" x-text="step.title"></h3>
                            <p class="text-gray-600 leading-relaxed" x-text="step.description"></p>
                        </div>
                        <!-- Arrow for desktop -->
                        <div class="hidden md:block absolute top-10 right-0 transform translate-x-1/2" x-show="index < 2">
                            <svg class="w-8 h-8 text-blue-300" :class="{'rotate-180': currentLang === 'ar'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Full Description Section -->
    <section class="py-16 lg:py-20 bg-gray-50" x-cloak>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl p-8 lg:p-12 shadow-lg border border-gray-200">
                <h2 class="text-3xl lg:text-4xl font-black mb-8 text-gray-900 text-center">
                    <span x-show="currentLang === 'fr'">À Propos de {{ $product->name }}</span>
                    <span x-show="currentLang === 'en'">About {{ $product->name }}</span>
                    <span x-show="currentLang === 'ar'">حول {{ $product->name }}</span>
                </h2>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed" 
                     x-html="(pageData[currentLang]?.full_description || '').replace(/\n/g, '\u003cbr\u003e')">
                    {!! nl2br(e($product->landing_page_content ?? $product->description)) !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 lg:py-20 bg-white" x-cloak>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4" x-text="pageData[currentLang]?.testimonials_title || 'Témoignages'"></h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <template x-for="(testimonial, index) in (pageData[currentLang]?.testimonials || [])" :key="index">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg border-2 border-green-200">
                        <!-- Stars -->
                        <div class="flex gap-1 mb-4">
                            <template x-for="i in (testimonial.rating || 5)" :key="i">
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            </template>
                        </div>
                        <p class="text-gray-700 mb-4 italic leading-relaxed" x-text="testimonial.text || ''"></p>
                        <p class="text-gray-900 font-bold" x-text="testimonial.name || ''"></p>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 lg:py-20 bg-gray-50" x-cloak x-data="{ openFaq: null }">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4" x-text="pageData[currentLang]?.faqs_title || 'Questions Fréquentes'"></h2>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <template x-for="(faq, index) in (pageData[currentLang]?.faqs || [])" :key="index">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                        <button @click="openFaq = openFaq === index ? null : index" 
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                            <span class="font-bold text-gray-900 text-lg" x-text="faq.question"></span>
                            <svg :class="{'rotate-180': openFaq === index}" class="w-6 h-6 text-blue-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === index" x-collapse class="px-6 pb-4">
                            <p class="text-gray-600 leading-relaxed" x-text="faq.answer"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Product Gallery -->
    @if(false)
    <section class="py-16 lg:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl lg:text-4xl font-black text-center mb-12 text-gray-900">
                <span x-show="currentLang === 'fr'">Galerie de Photos</span>
                <span x-show="currentLang === 'en'">Photo Gallery</span>
                <span x-show="currentLang === 'ar'">معرض الصور</span>
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($product->all_images as $image)
                <div class="relative group overflow-hidden rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300 cursor-pointer"
                     onclick="openImageModal('{{ $image }}')">
                    <img src="{{ $image }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        function openImageModal(imageSrc) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/90 z-[100] flex items-center justify-center p-4';
            modal.onclick = (e) => {
                if (e.target === modal || e.target.tagName === 'BUTTON') {
                    modal.remove();
                }
            };
            
            modal.innerHTML = `
                <div class="relative max-w-7xl w-full">
                    <button class="absolute top-4 right-4 text-white hover:text-gray-300 transition z-10">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <img src="${imageSrc}" alt="Product image" class="w-full h-auto max-h-[90vh] object-contain rounded-lg shadow-2xl" />
                </div>
            `;
            
            document.body.appendChild(modal);
        }
    </script>
    @endif


    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-lg">&copy; {{ date('Y') }} {{ config('app.name') }}. 
                <span x-show="currentLang === 'fr'">Tous droits réservés.</span>
                <span x-show="currentLang === 'en'">All rights reserved.</span>
                <span x-show="currentLang === 'ar'">جميع الحقوق محفوظة.</span>
            </p>
        </div>
    </footer>
</body>
</html>
