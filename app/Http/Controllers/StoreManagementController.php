<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreManagementController extends Controller
{
    use AuthorizesRequests;
    public function dashboard()
    {
        $stores = auth()->user()->stores()
            ->withCount('products', 'categories')
            ->latest()
            ->get();
            
        $stats = [
            'total_stores' => $stores->count(),
            'active_stores' => $stores->where('is_active', true)->count(),
            'total_products' => $stores->sum('products_count'),
        ];
        
        $currentStoreId = session('active_store_id');
        
        return view('stores.dashboard', compact('stores', 'stats', 'currentStoreId'));
    }
    
    public function index()
    {
        $stores = auth()->user()->stores()
            ->withCount('products', 'categories')
            ->latest()
            ->paginate(20);
            
        $currentStoreId = session('active_store_id');
        
        return view('stores.index', compact('stores', 'currentStoreId'));
    }
    
    public function create()
    {
        return view('stores.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:stores,subdomain|alpha_dash',
            'domain' => 'nullable|string|max:255|unique:stores,domain',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }
        
        $store = Store::create($validated);
        
        session(['active_store_id' => $store->id]);
        
        return redirect()->route('stores.dashboard')->with('success', 'Store created successfully! You can now manage it.');
    }
    
    public function edit(Store $store)
    {
        $this->authorize('update', $store);
        
        return view('stores.edit', compact('store'));
    }
    
    public function update(Request $request, Store $store)
    {
        $this->authorize('update', $store);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|alpha_dash|unique:stores,subdomain,' . $store->id,
            'domain' => 'nullable|string|max:255|unique:stores,domain,' . $store->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $store->update($validated);
        
        return redirect()->route('stores.dashboard')->with('success', 'Store updated successfully!');
    }
    
    public function destroy(Store $store)
    {
        $this->authorize('delete', $store);
        
        if (session('active_store_id') == $store->id) {
            session()->forget('active_store_id');
        }
        
        $store->delete();
        
        return redirect()->route('stores.dashboard')->with('success', 'Store deleted successfully!');
    }
    
    public function switchStore(Store $store)
    {
        $this->authorize('view', $store);
        
        session(['active_store_id' => $store->id]);
        
        return redirect()->route('app.dashboard')->with('success', 'Now managing: ' . $store->name);
    }
}
