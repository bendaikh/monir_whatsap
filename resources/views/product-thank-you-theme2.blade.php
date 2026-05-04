<!DOCTYPE html>
<html lang="{{ $lead->language ?? 'fr' }}" class="scroll-smooth"
      x-data="{ currentLang: '{{ $lead->language ?? 'fr' }}', rtlLangs: ['ar', 'he', 'fa', 'ur'] }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Thank You') }} - {{ $product->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800;900&family=Bebas+Neue&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f0; }
        .rtl { direction: rtl; font-family: 'Cairo', sans-serif; }
        .font-display { font-family: 'Bebas Neue', 'Cairo', sans-serif; letter-spacing: 0.02em; }
        @keyframes bounce-in { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .animate-bounce-in { animation: bounce-in 0.6s ease-out; }
        @keyframes pulse-scale { 0%,100% { transform: scale(1); } 50% { transform: scale(1.04); } }
        .animate-pulse-scale { animation: pulse-scale 1.6s ease-in-out infinite; }
    </style>
</head>
@php
    $td = $product->theme_data ?? [];
    $ctaColor = $td['cta_color'] ?? 'orange';
    $images = $product->all_images ?? [];
    
    $ctaGradientMap = [
        'orange' => 'from-orange-500 to-red-600',
        'green' => 'from-green-500 to-emerald-600',
        'red' => 'from-red-500 to-rose-600',
        'blue' => 'from-blue-500 to-indigo-600',
    ];
    $ctaBg = $ctaGradientMap[$ctaColor] ?? $ctaGradientMap['orange'];
    
    $currencySymbol = match($product->landing_page_currency ?? 'MAD') {
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'SAR' => 'ر.س', 'AED' => 'د.إ',
        'JPY' => '¥', 'CNY' => '¥', 'INR' => '₹', 'KRW' => '₩', 'RUB' => '₽',
        'TRY' => '₺', 'TZS' => 'TSh', 'KES' => 'KSh', default => 'د.م.'
    };
    $currencyCode = $product->landing_page_currency ?? 'MAD';
    
    $i18n = [
        'fr' => [
            'thank_you' => 'Merci pour votre commande !',
            'order_received' => 'Nous avons bien reçu votre commande',
            'contact_soon' => 'Notre équipe vous contactera très bientôt pour confirmer votre commande.',
            'your_order' => 'Votre commande',
            'product' => 'Produit',
            'offer_selected' => 'Offre sélectionnée',
            'price' => 'Prix',
            'your_info' => 'Vos informations',
            'name' => 'Nom',
            'phone' => 'Téléphone',
            'note' => 'Note',
            'what_happens_next' => 'Que se passe-t-il ensuite ?',
            'step1' => 'Nous vous appelons pour confirmer',
            'step2' => 'Préparation de votre commande',
            'step3' => 'Livraison à votre domicile',
            'back_to_store' => 'Retour à la boutique',
        ],
        'en' => [
            'thank_you' => 'Thank you for your order!',
            'order_received' => 'We have received your order',
            'contact_soon' => 'Our team will contact you very soon to confirm your order.',
            'your_order' => 'Your Order',
            'product' => 'Product',
            'offer_selected' => 'Selected Offer',
            'price' => 'Price',
            'your_info' => 'Your Information',
            'name' => 'Name',
            'phone' => 'Phone',
            'note' => 'Note',
            'what_happens_next' => 'What happens next?',
            'step1' => 'We call you to confirm',
            'step2' => 'We prepare your order',
            'step3' => 'Delivery to your home',
            'back_to_store' => 'Back to Store',
        ],
        'ar' => [
            'thank_you' => 'شكرا لطلبك!',
            'order_received' => 'لقد استلمنا طلبك',
            'contact_soon' => 'سيتصل بك فريقنا قريبا جدا لتأكيد طلبك.',
            'your_order' => 'طلبك',
            'product' => 'المنتج',
            'offer_selected' => 'العرض المختار',
            'price' => 'السعر',
            'your_info' => 'معلوماتك',
            'name' => 'الاسم',
            'phone' => 'الهاتف',
            'note' => 'ملاحظة',
            'what_happens_next' => 'ماذا يحدث بعد ذلك؟',
            'step1' => 'نتصل بك للتأكيد',
            'step2' => 'نحضر طلبك',
            'step3' => 'التوصيل إلى منزلك',
            'back_to_store' => 'العودة إلى المتجر',
        ],
        'sw' => [
            'thank_you' => 'Asante kwa agizo lako!',
            'order_received' => 'Tumepokea agizo lako',
            'contact_soon' => 'Timu yetu itawasiliana nawe hivi karibuni kuthibitisha agizo lako.',
            'your_order' => 'Agizo Lako',
            'product' => 'Bidhaa',
            'offer_selected' => 'Ofa Iliyochaguliwa',
            'price' => 'Bei',
            'your_info' => 'Taarifa Zako',
            'name' => 'Jina',
            'phone' => 'Simu',
            'note' => 'Maelezo',
            'what_happens_next' => 'Kinachofuata ni nini?',
            'step1' => 'Tutakupigia simu kuthibitisha',
            'step2' => 'Tutaandaa agizo lako',
            'step3' => 'Utoaji nyumbani kwako',
            'back_to_store' => 'Rudi Dukani',
        ],
    ];
    
    $lang = $lead->language ?? 'fr';
    $t = $i18n[$lang] ?? $i18n['en'] ?? $i18n['fr'];
@endphp
<body class="antialiased bg-[#f5f5f0] min-h-screen" :class="{'rtl': rtlLangs.includes(currentLang)}">

    <!-- Success Header -->
    <div class="bg-gradient-to-r {{ $ctaBg }} text-white py-12 md:py-16">
        <div class="container mx-auto px-4 text-center max-w-3xl">
            <div class="animate-bounce-in mb-6">
                <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center shadow-2xl">
                    <svg class="w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <h1 class="font-display text-4xl md:text-5xl font-black uppercase mb-4">
                {{ $t['thank_you'] }}
            </h1>
            <p class="text-xl md:text-2xl font-semibold opacity-95 mb-2">
                {{ $t['order_received'] }}
            </p>
            <p class="text-lg opacity-80">
                {{ $t['contact_soon'] }}
            </p>
        </div>
    </div>

    <!-- Order Details -->
    <div class="container mx-auto px-4 py-10 md:py-14 max-w-4xl">
        <div class="grid md:grid-cols-2 gap-6">
            
            <!-- Product Info Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                <h2 class="font-display text-2xl font-black text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">📦</span>
                    {{ $t['your_order'] }}
                </h2>
                
                <div class="flex gap-4 mb-4">
                    @if(!empty($images))
                    <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden border-2 border-gray-200">
                        <img src="{{ $images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $t['product'] }}</p>
                    </div>
                </div>
                
                @if($selectedPromotion)
                <div class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-600 mb-1">{{ $t['offer_selected'] }}</p>
                    <p class="font-bold text-lg text-gray-900">
                        @if($selectedPromotion->label)
                            {{ $selectedPromotion->label }}
                        @else
                            {{ $selectedPromotion->quantity_range }} {{ __('units') }}
                        @endif
                    </p>
                </div>
                @endif
                
                <div class="border-t-2 border-gray-100 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-semibold">{{ $t['price'] }}</span>
                        <span class="font-display text-3xl font-black text-gray-900">
                            @if($selectedPromotion)
                                {{ number_format($selectedPromotion->price, 0) }}
                            @else
                                {{ number_format($product->price, 0) }}
                            @endif
                            {{ $currencyCode }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Customer Info Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                <h2 class="font-display text-2xl font-black text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">👤</span>
                    {{ $t['your_info'] }}
                </h2>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">{{ $t['name'] }}</p>
                            <p class="font-semibold text-gray-900">{{ $lead->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">{{ $t['phone'] }}</p>
                            <p class="font-semibold text-gray-900">{{ $lead->phone }}</p>
                        </div>
                    </div>
                    
                    @if($lead->note)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">{{ $t['note'] }}</p>
                            <p class="font-semibold text-gray-900">{{ $lead->note }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- What Happens Next -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 md:p-8 border-2 border-gray-100">
            <h2 class="font-display text-2xl font-black text-gray-900 mb-6 text-center">
                {{ $t['what_happens_next'] }}
            </h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 shadow-lg">
                        📞
                    </div>
                    <div class="w-8 h-8 mx-auto -mt-5 bg-gray-900 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                    <p class="font-semibold text-gray-900 mt-2">{{ $t['step1'] }}</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-yellow-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 shadow-lg">
                        📦
                    </div>
                    <div class="w-8 h-8 mx-auto -mt-5 bg-gray-900 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                    <p class="font-semibold text-gray-900 mt-2">{{ $t['step2'] }}</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-green-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 shadow-lg">
                        🚚
                    </div>
                    <div class="w-8 h-8 mx-auto -mt-5 bg-gray-900 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                    <p class="font-semibold text-gray-900 mt-2">{{ $t['step3'] }}</p>
                </div>
            </div>
        </div>
        
        <!-- Back to Store Button -->
        <div class="mt-8 text-center">
            <a href="{{ route('store.home', $store->subdomain) }}" 
               class="inline-block bg-gradient-to-r {{ $ctaBg }} text-white font-display text-xl uppercase px-8 py-4 rounded-2xl shadow-xl hover:scale-105 transition-transform animate-pulse-scale">
                ← {{ $t['back_to_store'] }}
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6 text-center text-xs">
        <div class="container mx-auto px-4">
            <div class="font-bold text-white mb-1">{{ $store->name ?? 'Store' }}</div>
            © {{ date('Y') }} — All rights reserved.
        </div>
    </footer>

</body>
</html>
