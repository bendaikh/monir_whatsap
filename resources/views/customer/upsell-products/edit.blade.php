@extends('layouts.customer')

@section('title', 'Edit Upsell Product')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('app.upsell-products.index') }}" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 mb-4">
            <span class="material-icons text-sm">arrow_back</span>
            Back to Upsell Products
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Upsell Product</h1>
        <p class="text-gray-600 mt-2">Update product details</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <span class="material-icons">error</span>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('app.upsell-products.update', $upsellProduct) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $upsellProduct->title) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">{{ old('description', $upsellProduct->description) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">A short description of this upsell product</p>
            </div>

            @if($upsellProduct->images && count($upsellProduct->images) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Current Images</label>
                <div class="grid grid-cols-4 gap-4">
                    @foreach($upsellProduct->images as $index => $image)
                    <div class="relative group">
                        <img src="{{ \App\Models\Product::resolvePublicImageUrl($image) }}" alt="Image {{ $index + 1 }}" class="w-full h-32 object-cover rounded-lg border border-gray-200">
                        <form action="{{ route('app.upsell-products.delete-image', [$upsellProduct, $index]) }}" method="POST" class="absolute inset-0 bg-black/50 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this image?')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add More Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                <p class="text-xs text-gray-500 mt-1">You can upload multiple new images</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ $upsellProduct->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <label class="text-sm font-medium text-gray-700">Active (show this product on thank you pages)</label>
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('app.upsell-products.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                <span class="material-icons text-sm">save</span>
                Update Upsell Product
            </button>
        </div>
    </form>
</div>
@endsection
