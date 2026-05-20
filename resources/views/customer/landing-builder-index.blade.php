@extends('layouts.customer')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white flex items-center gap-3">
        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
        </svg>
        Landing Page Builder
    </h2>
    <p class="text-sm text-gray-400 mt-1">Select a product to customize its landing page — colors, texts, images, and sections</p>
</div>

@if($products->isEmpty())
<div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-16 text-center">
    <p class="text-gray-400 mb-6">No products yet. Create a product first to build its landing page.</p>
    <a href="{{ route('app.products.create') }}" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg inline-flex items-center gap-2">
        Add Product
    </a>
</div>
@else
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($products as $product)
    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden hover:border-purple-500/50 transition group">
        <div class="aspect-video bg-[#0a1628] relative">
            @if($product->first_image)
            <img src="{{ $product->first_image }}" alt="" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center text-gray-600">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            @endif
        </div>
        <div class="p-4">
            <h3 class="font-bold text-white truncate">{{ $product->name }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $product->sku ?? 'No SKU' }}</p>
            <div class="flex gap-2 mt-4">
                <a href="{{ route('app.products.landing-builder', $product->id) }}"
                   class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold rounded-lg text-center transition">
                    Open Builder
                </a>
                @if($store)
                <a href="{{ $store->domain ? 'https://' . $store->domain . '/product/' . $product->slug : route('store.product.show', [$store->subdomain, $product->slug]) }}"
                   target="_blank"
                   class="px-3 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg" title="Preview live">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
