<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetActiveStore
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $activeStoreId = session('active_store_id');
            
            if (!$activeStoreId && auth()->user()->role === 'superadmin') {
                $firstStore = auth()->user()->stores()->first();
                if ($firstStore) {
                    session(['active_store_id' => $firstStore->id]);
                }
            }
            
            if ($activeStoreId) {
                view()->share('activeStore', \App\Models\Store::find($activeStoreId));
            }
        }
        
        return $next($request);
    }
}
