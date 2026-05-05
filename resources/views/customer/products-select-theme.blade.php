@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Create New Product
                </h2>
                <p class="text-sm text-gray-400 mt-1">Select the E-Commerce theme for your product landing page</p>
            </div>
            <a href="{{ route('app.products') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- Theme 2 - E-commerce Style (Now the only theme) -->
        <div class="group cursor-pointer" onclick="selectTheme('theme2')">
            <div class="bg-[#0f1c2e] border-2 border-cyan-500 rounded-xl overflow-hidden shadow-lg shadow-cyan-500/20">
                <!-- Theme Preview Image -->
                <div class="relative aspect-[4/3] bg-gradient-to-br from-[#1a2942] to-[#0a1628] overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <!-- Theme 2 Preview Mockup (E-commerce style) -->
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
                                        <div class="text-[8px] text-blue-600 font-bold">325+</div>
                                    </div>
                                    <div class="flex-1 bg-green-50 rounded p-1 text-center">
                                        <div class="text-[8px] text-green-600 font-bold">4.8 ⭐</div>
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
                    <div class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        DEFAULT
                    </div>
                </div>
                
                <!-- Theme Info -->
                <div class="p-5">
                    <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">
                        E-Commerce Landing Page
                        <span class="text-xs bg-cyan-500/20 text-cyan-400 px-2 py-0.5 rounded">Recommended</span>
                    </h3>
                    <p class="text-gray-400 text-sm mb-4">
                        A high-converting sales page with trust badges, countdown timers, social proof, and multiple CTAs. Perfect for all products, promotions and dropshipping.
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
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Product variations support
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            AI-powered landing sections
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Quantity-based promotions
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Multi-language support (30+ languages)
                        </li>
                    </ul>
                </div>
                
                <!-- Select Button -->
                <div class="px-5 pb-5">
                    <button type="button" class="w-full py-4 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2 text-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Product with E-Commerce Theme
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
