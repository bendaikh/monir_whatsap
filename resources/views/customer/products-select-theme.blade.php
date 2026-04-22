@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Select Product Theme
                </h2>
                <p class="text-sm text-gray-400 mt-1">Choose a landing page theme for your new product</p>
            </div>
            <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl">
        <!-- Theme 1 - Classic -->
        <div class="group cursor-pointer" onclick="selectTheme('theme1')">
            <div class="bg-[#0f1c2e] border-2 border-white/10 rounded-xl overflow-hidden transition-all duration-300 hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/20 group-hover:scale-[1.02]">
                <!-- Theme Preview Image -->
                <div class="relative aspect-[4/3] bg-gradient-to-br from-[#1a2942] to-[#0a1628] overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <!-- Theme 1 Preview Mockup -->
                        <div class="w-[90%] h-[85%] bg-white rounded-lg shadow-2xl overflow-hidden">
                            <div class="h-8 bg-gray-100 flex items-center px-3 gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="p-3 space-y-2">
                                <div class="flex gap-3">
                                    <div class="w-1/2 aspect-square bg-gray-200 rounded"></div>
                                    <div class="w-1/2 space-y-2">
                                        <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-full"></div>
                                        <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                                        <div class="h-6 bg-emerald-500 rounded w-1/2 mt-3"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    <div class="aspect-square bg-gray-100 rounded"></div>
                                    <div class="aspect-square bg-gray-100 rounded"></div>
                                    <div class="aspect-square bg-gray-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-3 left-3 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        Classic
                    </div>
                </div>
                
                <!-- Theme Info -->
                <div class="p-5">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                        Theme 1 - Classic Layout
                        <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded">Default</span>
                    </h3>
                    <p class="text-gray-400 text-sm mb-4">
                        A clean, professional product page with image gallery, detailed descriptions, and AI-generated content sections. Perfect for most products.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Image gallery with multiple photos
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Product variations support
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            AI-powered landing sections
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Quantity-based promotions
                        </li>
                    </ul>
                </div>
                
                <!-- Select Button -->
                <div class="px-5 pb-5">
                    <button type="button" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        Select Theme 1
                    </button>
                </div>
            </div>
        </div>

        <!-- Theme 2 - E-commerce Style -->
        <div class="group cursor-pointer" onclick="selectTheme('theme2')">
            <div class="bg-[#0f1c2e] border-2 border-white/10 rounded-xl overflow-hidden transition-all duration-300 hover:border-cyan-500 hover:shadow-lg hover:shadow-cyan-500/20 group-hover:scale-[1.02]">
                <!-- Theme Preview Image -->
                <div class="relative aspect-[4/3] bg-gradient-to-br from-[#1a2942] to-[#0a1628] overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <!-- Theme 2 Preview Mockup (E-commerce style like the provided image) -->
                        <div class="w-[90%] h-[85%] bg-white rounded-lg shadow-2xl overflow-hidden">
                            <div class="h-8 bg-gray-100 flex items-center px-3 gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="p-2 space-y-2">
                                <!-- Hero section with product -->
                                <div class="bg-gradient-to-r from-orange-100 to-yellow-100 rounded p-2 flex items-center gap-2">
                                    <div class="w-12 h-12 bg-gray-300 rounded"></div>
                                    <div class="flex-1">
                                        <div class="h-2 bg-gray-400 rounded w-3/4 mb-1"></div>
                                        <div class="h-4 bg-red-500 text-white text-[6px] rounded px-1 inline-block">PROMO</div>
                                    </div>
                                </div>
                                <!-- Stats row -->
                                <div class="flex gap-1">
                                    <div class="flex-1 bg-blue-50 rounded p-1 text-center">
                                        <div class="text-[8px] text-blue-600 font-bold">325</div>
                                    </div>
                                    <div class="flex-1 bg-green-50 rounded p-1 text-center">
                                        <div class="text-[8px] text-green-600 font-bold">4.8</div>
                                    </div>
                                </div>
                                <!-- Features grid -->
                                <div class="grid grid-cols-2 gap-1">
                                    <div class="bg-gray-50 rounded p-1">
                                        <div class="w-full h-6 bg-gray-200 rounded"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded p-1">
                                        <div class="w-full h-6 bg-gray-200 rounded"></div>
                                    </div>
                                </div>
                                <!-- CTA -->
                                <div class="bg-orange-500 rounded p-1 text-center">
                                    <div class="text-[8px] text-white font-bold">ORDER NOW</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-3 left-3 bg-cyan-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        E-Commerce
                    </div>
                    <div class="absolute top-3 right-3 bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-full">
                        NEW
                    </div>
                </div>
                
                <!-- Theme Info -->
                <div class="p-5">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                        Theme 2 - E-Commerce Style
                        <span class="text-xs bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded">Popular</span>
                    </h3>
                    <p class="text-gray-400 text-sm mb-4">
                        A high-converting sales page with trust badges, countdown timers, social proof, and multiple CTAs. Ideal for promotions and dropshipping.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sales-focused layout with urgency
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Trust badges & social proof
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Multiple call-to-action buttons
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Feature highlights with icons
                        </li>
                    </ul>
                </div>
                
                <!-- Select Button -->
                <div class="px-5 pb-5">
                    <button type="button" class="w-full py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        Select Theme 2
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectTheme(theme) {
            window.location.href = "{{ route('app.products.create') }}?theme=" + theme;
        }
    </script>
@endsection
