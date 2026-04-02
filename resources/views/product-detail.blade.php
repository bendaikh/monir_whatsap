<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ $product->landing_page_hero_description ?? $product->description }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="antialiased bg-gradient-to-br from-slate-50 to-blue-50 text-gray-900">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-3xl font-bold bg-gradient-to-r from-emerald-500 to-blue-600 bg-clip-text text-transparent">
                        {{ config('app.name') }}
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">
                        <span class="material-icons align-middle mr-1">arrow_back</span>
                        Back to Store
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @if($product->landing_page_hero_title)
    <!-- AI-Generated Landing Page -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <section class="mb-16">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-3xl p-12 text-white shadow-2xl">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-5xl font-bold mb-6 leading-tight">{{ $product->landing_page_hero_title }}</h1>
                        <p class="text-xl mb-8 text-white/90">{{ $product->landing_page_hero_description }}</p>
                        
                        <div class="flex items-center gap-6 mb-8">
                            <div>
                                <div class="text-5xl font-bold">{{ number_format($product->price, 2) }} MAD</div>
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <div class="text-lg line-through text-white/70">{{ number_format($product->compare_at_price, 2) }} MAD</div>
                                @endif
                            </div>
                            @if($product->discount_percentage)
                            <div class="bg-yellow-400 text-purple-900 px-4 py-2 rounded-full font-bold text-xl">
                                -{{ $product->discount_percentage }}%
                            </div>
                            @endif
                        </div>

                        <a href="#order" class="inline-block px-8 py-4 bg-white text-purple-600 font-bold text-lg rounded-full hover:bg-yellow-400 hover:text-purple-900 transition shadow-lg hover:shadow-xl hover:scale-105 transform">
                            <span class="material-icons align-middle mr-2">shopping_cart</span>
                            {{ $product->landing_page_cta ?? 'Order Now' }}
                        </a>
                    </div>
                    
                    <div class="relative">
                        @if($product->first_image)
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-2xl shadow-2xl transform hover:scale-105 transition">
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if($product->landing_page_features && count($product->landing_page_features) > 0)
        <!-- Features Section -->
        <section class="mb-16">
            <h2 class="text-4xl font-bold text-center mb-12 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                Why Choose {{ $product->name }}?
            </h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($product->landing_page_features as $feature)
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2">
                    <div class="text-5xl mb-4">{{ $feature['icon'] ?? '✓' }}</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600">{{ $feature['description'] }}</p>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Full Description Section -->
        <section class="mb-16">
            <div class="bg-white rounded-3xl p-12 shadow-lg">
                <h2 class="text-4xl font-bold mb-8 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                    About {{ $product->name }}
                </h2>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($product->landing_page_content ?? $product->description)) !!}
                </div>
            </div>
        </section>

        <!-- Product Gallery -->
        @if($product->images && count($product->images) > 1)
        <section class="mb-16">
            <h2 class="text-4xl font-bold text-center mb-12 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                Product Gallery
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($product->images as $image)
                <div class="relative group overflow-hidden rounded-2xl shadow-lg">
                    <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover transform group-hover:scale-110 transition">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

    @else
    <!-- Standard Product Page (No AI Landing Page) -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="grid md:grid-cols-2 gap-12 p-12">
                <div>
                    @if($product->first_image)
                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-2xl shadow-lg">
                    @endif
                    
                    @if($product->images && count($product->images) > 1)
                    <div class="grid grid-cols-4 gap-4 mt-6">
                        @foreach(array_slice($product->images, 1, 4) as $image)
                        <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-24 object-cover rounded-lg">
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <div>
                    @if($product->category)
                    <div class="text-purple-600 font-semibold mb-3">{{ $product->category->name }}</div>
                    @endif
                    
                    <h1 class="text-4xl font-bold mb-6">{{ $product->name }}</h1>
                    
                    <div class="flex items-center gap-6 mb-8">
                        <div>
                            <div class="text-4xl font-bold text-purple-600">{{ number_format($product->price, 2) }} MAD</div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <div class="text-lg line-through text-gray-500">{{ number_format($product->compare_at_price, 2) }} MAD</div>
                            @endif
                        </div>
                        @if($product->discount_percentage)
                        <div class="bg-red-500 text-white px-4 py-2 rounded-full font-bold">
                            -{{ $product->discount_percentage }}%
                        </div>
                        @endif
                    </div>
                    
                    <div class="prose prose-lg max-w-none mb-8 text-gray-700">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                    
                    @if($product->stock !== null)
                    <div class="mb-6 flex items-center gap-2 text-gray-600">
                        <span class="material-icons">inventory_2</span>
                        <span>Stock: {{ $product->stock }}</span>
                    </div>
                    @endif
                    
                    <a href="#order" class="inline-block px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold text-lg rounded-full hover:shadow-xl transition transform hover:scale-105">
                        <span class="material-icons align-middle mr-2">shopping_cart</span>
                        Order Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Order Section -->
    <section id="order" class="bg-gradient-to-r from-purple-900 to-blue-900 py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-4xl font-bold text-white mb-6">Ready to Order?</h2>
                <p class="text-xl text-white/90 mb-8">Contact us now to place your order</p>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->user->phone ?? '') }}?text=Hello, I'm interested in {{ urlencode($product->name) }}" 
                       target="_blank" 
                       class="flex items-center justify-center gap-3 px-8 py-4 bg-green-500 hover:bg-green-600 text-white font-bold text-lg rounded-full transition shadow-lg hover:shadow-xl">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>Order via WhatsApp</span>
                    </a>
                    
                    <a href="tel:{{ $product->user->phone ?? '' }}" 
                       class="flex items-center justify-center gap-3 px-8 py-4 bg-blue-500 hover:bg-blue-600 text-white font-bold text-lg rounded-full transition shadow-lg hover:shadow-xl">
                        <span class="material-icons text-3xl">phone</span>
                        <span>Call Us</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if($relatedProducts && $relatedProducts->count() > 0)
    <!-- Related Products -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-12 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                Related Products
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($relatedProducts as $related)
                <a href="{{ route('product.show', $related->slug) }}" class="group">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                        <div class="relative overflow-hidden">
                            <img src="{{ $related->first_image }}" alt="{{ $related->name }}" class="w-full h-64 object-cover group-hover:scale-110 transition">
                            @if($related->discount_percentage)
                            <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full font-bold text-sm">
                                -{{ $related->discount_percentage }}%
                            </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-2 text-gray-900 group-hover:text-purple-600 transition">{{ $related->name }}</h3>
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-bold text-purple-600">{{ number_format($related->price, 2) }} MAD</span>
                                @if($related->compare_at_price)
                                <span class="text-sm line-through text-gray-500">{{ number_format($related->compare_at_price, 2) }} MAD</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-lg">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
