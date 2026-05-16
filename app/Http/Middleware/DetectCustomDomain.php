<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;
use App\Http\Controllers\ProductController;

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
            $request->attributes->set('custom_domain_subdomain', $store->subdomain);
            
            // Share the store with views
            view()->share('customDomainStore', $store);
            
            // Handle custom domain routes without /store/{subdomain} prefix
            $path = $request->path();
            
            // Root path - show store home
            if ($path === '/') {
                $request->merge(['is_custom_domain' => true]);
                $response = app(ProductController::class)->index($store->subdomain, $request);
                return response($response);
            }
            
            // Product detail page: /product/{slug}
            if (preg_match('#^product/([^/]+)$#', $path, $matches)) {
                $request->merge(['is_custom_domain' => true]);
                $response = app(ProductController::class)->show($store->subdomain, $matches[1], $request);
                return response($response);
            }
            
            // Product submission: /product/{slug}/submit-lead
            if (preg_match('#^product/([^/]+)/submit-lead$#', $path, $matches)) {
                $request->merge(['is_custom_domain' => true]);
                return app(ProductController::class)->submitLead($request, $store->subdomain, $matches[1]);
            }
            
            // Thank you page: /product/{slug}/thank-you/{lead}
            if (preg_match('#^product/([^/]+)/thank-you/([0-9]+)$#', $path, $matches)) {
                $request->merge(['is_custom_domain' => true]);
                $response = app(ProductController::class)->thankYou($store->subdomain, $matches[1], $matches[2], $request);
                return response($response);
            }
            
            // Buy upsell: /product/{slug}/buy-upsell
            if (preg_match('#^product/([^/]+)/buy-upsell$#', $path, $matches)) {
                $request->merge(['is_custom_domain' => true]);
                return app(ProductController::class)->buyUpsell($store->subdomain, $matches[1], $request);
            }
        }
        
        return $next($request);
    }
}
