<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveStore
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $activeStoreId = session('active_store_id');
            
            if (!$activeStoreId) {
                return redirect()->route('stores.dashboard')
                    ->with('warning', 'Please select a store to manage first.');
            }
            
            $store = \App\Models\Store::find($activeStoreId);
            
            if (!$store || ($store->user_id !== auth()->id() && auth()->user()->role !== 'superadmin')) {
                session()->forget('active_store_id');
                return redirect()->route('stores.dashboard')
                    ->with('error', 'Invalid store selection. Please select a valid store.');
            }
            
            view()->share('activeStore', $store);
        }
        
        return $next($request);
    }
}
