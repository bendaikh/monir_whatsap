<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WhatsappProfile;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('is_active', true)->count(),
            'total_stores' => Store::count(),
            'active_stores' => Store::where('is_active', true)->count(),
            'total_whatsapp_profiles' => WhatsappProfile::count(),
            'active_profiles' => WhatsappProfile::where('status', 'connected')->count(),
            'total_messages' => Message::count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount'),
        ];
        
        $recent_customers = User::where('role', 'customer')
            ->latest()
            ->take(10)
            ->get();
            
        $subscription_breakdown = Subscription::select('plan', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('plan')
            ->get();
        
        return view('superadmin.dashboard', compact('stats', 'recent_customers', 'subscription_breakdown'));
    }
    
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->withCount('whatsappProfiles')
            ->latest()
            ->paginate(20);
            
        return view('superadmin.customers', compact('customers'));
    }
    
    public function analytics()
    {
        return view('superadmin.analytics');
    }
    
    public function stores()
    {
        $stores = Store::with('user')
            ->withCount('products')
            ->latest()
            ->paginate(20);
        
        $currentStoreId = session('active_store_id');
            
        return view('superadmin.stores', compact('stores', 'currentStoreId'));
    }
    
    public function storeCreate()
    {
        return view('superadmin.stores-create');
    }
    
    public function storeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:stores,subdomain|alpha_dash',
            'domain' => 'nullable|string|max:255|unique:stores,domain',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        $store = Store::create($validated);
        
        return redirect()->route('superadmin.stores')->with('success', 'Store created successfully!');
    }
    
    public function storeEdit(Store $store)
    {
        return view('superadmin.stores-edit', compact('store'));
    }
    
    public function storeUpdate(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|alpha_dash|unique:stores,subdomain,' . $store->id,
            'domain' => 'nullable|string|max:255|unique:stores,domain,' . $store->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $store->update($validated);
        
        return redirect()->route('superadmin.stores')->with('success', 'Store updated successfully!');
    }
    
    public function storeDestroy(Store $store)
    {
        $store->delete();
        
        return redirect()->route('superadmin.stores')->with('success', 'Store deleted successfully!');
    }
    
    public function switchStore(Store $store)
    {
        session(['active_store_id' => $store->id]);
        
        return redirect()->route('app.dashboard')->with('success', 'Switched to store: ' . $store->name);
    }
}
