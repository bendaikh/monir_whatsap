<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class DetectCustomDomain
{
    /**
     * Handle an incoming request.
     * Detects if a custom domain is being used and loads the appropriate store.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Skip if it's the main domain or localhost
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        if ($host === $mainDomain || $host === 'localhost' || str_ends_with($host, '.localhost')) {
            return $next($request);
        }
        
        // Check if this host matches a custom domain in the database
        $store = Store::where('domain', $host)
            ->orWhere('domain', 'www.' . $host)
            ->orWhere('domain', str_replace('www.', '', $host))
            ->where('is_active', true)
            ->first();
        
        if ($store) {
            // Store the custom domain store in the request
            $request->attributes->set('custom_domain_store', $store);
            
            // Share the store with views
            view()->share('customDomainStore', $store);
            
            // If accessing root, redirect to store home
            if ($request->path() === '/') {
                return redirect()->route('store.home', ['subdomain' => $store->subdomain]);
            }
        }
        
        return $next($request);
    }
}
