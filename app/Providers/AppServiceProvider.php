<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::component('layouts.customer', 'customer-layout');
        \Illuminate\Support\Facades\Blade::component('layouts.stores', 'stores-layout');
        
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Store::class, \App\Policies\StorePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Workspace::class, \App\Policies\WorkspacePolicy::class);
    }
}
