<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreManagementController extends Controller
{
    use AuthorizesRequests;
    public function dashboard(Request $request)
    {
        $activeWorkspaceId = session('active_workspace_id');
        $activeWorkspace = $activeWorkspaceId ? \App\Models\Workspace::find($activeWorkspaceId) : null;
        $view = $request->get('view', 'overview');
        
        $stores = auth()->user()->stores()
            ->when($activeWorkspaceId, function ($query, $workspaceId) {
                return $query->where('workspace_id', $workspaceId);
            })
            ->withCount('products', 'categories')
            ->latest()
            ->get();
        
        // Workspace-specific statistics
        $storeIds = $stores->pluck('id');
        $totalProducts = \App\Models\Product::whereIn('store_id', $storeIds)->count();
        $totalOrders = \App\Models\Order::whereIn('store_id', $storeIds)->count();
        $newOrders = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $totalRevenue = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('status', 'completed')
            ->sum('total');
        $pendingOrders = \App\Models\Order::whereIn('store_id', $storeIds)
            ->where('status', 'pending')
            ->count();
            
        $stats = [
            'total_stores' => $stores->count(),
            'active_stores' => $stores->where('is_active', true)->count(),
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'new_orders' => $newOrders,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
        ];
        
        $currentStoreId = session('active_store_id');
        
        if ($view === 'list') {
            return view('stores.list', compact('stores', 'stats', 'currentStoreId', 'activeWorkspace'));
        }
        
        return view('stores.overview', compact('stores', 'stats', 'currentStoreId', 'activeWorkspace'));
    }
    
    public function index()
    {
        $activeWorkspaceId = session('active_workspace_id');
        
        $stores = auth()->user()->stores()
            ->when($activeWorkspaceId, function ($query, $workspaceId) {
                return $query->where('workspace_id', $workspaceId);
            })
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
        $validated['workspace_id'] = session('active_workspace_id');
        
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
    
    public function updateDomain(Request $request, Store $store)
    {
        $this->authorize('update', $store);
        
        $validated = $request->validate([
            'domain' => 'nullable|string|max:255|unique:stores,domain,' . $store->id,
        ]);
        
        // Clean up the domain (remove http://, https://, www. if user included them)
        $domain = $validated['domain'] ?? null;
        if ($domain) {
            $domain = preg_replace('#^https?://#', '', $domain);
            $domain = trim($domain);
        }
        
        $store->update(['domain' => $domain]);
        
        if ($domain) {
            return redirect()->route('stores.dashboard')->with('success', 'Custom domain added successfully! Please configure your DNS records.');
        } else {
            return redirect()->route('stores.dashboard')->with('success', 'Custom domain removed. Store is accessible via subdomain.');
        }
    }
}
