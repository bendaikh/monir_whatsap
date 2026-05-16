@extends('layouts.customer')

@section('title', 'Upsell Products')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Upsell Products</h1>
            <p class="text-gray-600 mt-2">Manage products to show on thank you pages</p>
        </div>
        <a href="{{ route('app.upsell-products.create') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
            <span class="material-icons text-sm">add</span>
            Add Upsell Product
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($upsellProducts->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($upsellProducts as $upsell)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
            <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ $upsell->first_image }}" alt="{{ $upsell->title }}" class="w-full h-full object-cover">
            </div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-bold text-gray-900 text-lg">{{ $upsell->title }}</h3>
                    @if($upsell->is_active)
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Active</span>
                    @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Inactive</span>
                    @endif
                </div>
                @if($upsell->description)
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $upsell->description }}</p>
                @endif
                @if($upsell->images && count($upsell->images) > 0)
                <p class="text-xs text-gray-500 mb-4">{{ count($upsell->images) }} image(s)</p>
                @endif
                <div class="flex gap-2">
                    <a href="{{ route('app.upsell-products.edit', $upsell) }}" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition text-center text-sm">
                        Edit
                    </a>
                    <form action="{{ route('app.upsell-products.destroy', $upsell) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this upsell product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-icons text-gray-400 text-3xl">shopping_bag</span>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No upsell products yet</h3>
        <p class="text-gray-600 mb-6">Create your first upsell product to show on thank you pages</p>
        <a href="{{ route('app.upsell-products.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
            <span class="material-icons text-sm">add</span>
            Add Upsell Product
        </a>
    </div>
    @endif
</div>
@endsection
