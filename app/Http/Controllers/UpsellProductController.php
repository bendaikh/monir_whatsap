<?php

namespace App\Http\Controllers;

use App\Models\UpsellProduct;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpsellProductController extends Controller
{
    private function getActiveStore()
    {
        $storeId = session('active_store_id');
        if (!$storeId) {
            return null;
        }
        return Store::find($storeId);
    }

    public function index()
    {
        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        $upsellProducts = UpsellProduct::where('store_id', $store->id)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.upsell-products.index', compact('upsellProducts'));
    }

    public function create()
    {
        return view('customer.upsell-products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        $images = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('upsell-products', 'public');
                $images[] = $path;
            }
        }

        UpsellProduct::create([
            'store_id' => $store->id,
            'title' => $request->title,
            'description' => $request->description,
            'images' => $images,
            'is_active' => $request->has('is_active'),
            'order' => UpsellProduct::where('store_id', $store->id)->max('order') + 1,
        ]);

        return redirect()->route('app.upsell-products.index')
            ->with('success', 'Upsell product created successfully!');
    }

    public function edit(UpsellProduct $upsellProduct)
    {
        // Make sure the upsell product belongs to the current store
        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        if ($upsellProduct->store_id !== $store->id) {
            abort(403);
        }

        return view('customer.upsell-products.edit', compact('upsellProduct'));
    }

    public function update(Request $request, UpsellProduct $upsellProduct)
    {
        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        if ($upsellProduct->store_id !== $store->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $images = $upsellProduct->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('upsell-products', 'public');
                $images[] = $path;
            }
        }

        $upsellProduct->update([
            'title' => $request->title,
            'description' => $request->description,
            'images' => $images,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('app.upsell-products.index')
            ->with('success', 'Upsell product updated successfully!');
    }

    public function destroy(UpsellProduct $upsellProduct)
    {
        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        if ($upsellProduct->store_id !== $store->id) {
            abort(403);
        }

        // Delete images from storage
        if (!empty($upsellProduct->images)) {
            foreach ($upsellProduct->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $upsellProduct->delete();

        return redirect()->route('app.upsell-products.index')
            ->with('success', 'Upsell product deleted successfully!');
    }

    public function deleteImage(UpsellProduct $upsellProduct, $index)
    {
        $store = $this->getActiveStore();
        
        if (!$store) {
            return redirect()->route('stores.dashboard')
                ->with('error', 'Please select a store first.');
        }
        
        if ($upsellProduct->store_id !== $store->id) {
            abort(403);
        }

        $images = $upsellProduct->images ?? [];
        
        if (isset($images[$index])) {
            // Delete from storage
            Storage::disk('public')->delete($images[$index]);
            
            // Remove from array
            array_splice($images, $index, 1);
            
            // Update upsell product
            $upsellProduct->update(['images' => $images]);
        }

        return back()->with('success', 'Image deleted successfully!');
    }
}
