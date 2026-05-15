<?php

if (!function_exists('store_url')) {
    /**
     * Generate a store URL that works with both subdomain and custom domain
     *
     * @param string $path
     * @param \App\Models\Store|null $store
     * @return string
     */
    function store_url($path = '', $store = null)
    {
        // If custom domain is being used
        if (request()->attributes->get('custom_domain_store') || request()->get('is_custom_domain')) {
            $baseUrl = request()->getScheme() . '://' . request()->getHost();
            $path = ltrim($path, '/');
            return $baseUrl . ($path ? '/' . $path : '');
        }
        
        // Otherwise use subdomain route
        if ($store) {
            return route('store.home', ['subdomain' => $store->subdomain]) . ($path ? '/' . ltrim($path, '/') : '');
        }
        
        return url($path);
    }
}

if (!function_exists('store_route')) {
    /**
     * Generate store routes that work with both subdomain and custom domain
     *
     * @param string $name Route name (product.show, product.submit-lead, etc.)
     * @param array $parameters
     * @param \App\Models\Store|null $store
     * @return string
     */
    function store_route($name, $parameters = [], $store = null)
    {
        // If custom domain is being used
        if (request()->attributes->get('custom_domain_store') || request()->get('is_custom_domain')) {
            $baseUrl = request()->getScheme() . '://' . request()->getHost();
            
            switch ($name) {
                case 'store.home':
                    return $baseUrl;
                    
                case 'store.product.show':
                    $slug = $parameters['slug'] ?? $parameters[1] ?? '';
                    return $baseUrl . '/product/' . $slug;
                    
                case 'store.product.submit-lead':
                    $slug = $parameters['slug'] ?? $parameters[1] ?? '';
                    return $baseUrl . '/product/' . $slug . '/submit-lead';
                    
                case 'store.product.thank-you':
                    $slug = $parameters['slug'] ?? $parameters[1] ?? '';
                    $lead = $parameters['lead'] ?? $parameters[2] ?? '';
                    return $baseUrl . '/product/' . $slug . '/thank-you/' . $lead;
                    
                default:
                    return $baseUrl;
            }
        }
        
        // Otherwise use standard routing
        return route($name, $parameters);
    }
}
