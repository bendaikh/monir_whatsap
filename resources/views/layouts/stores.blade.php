<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            <div class="flex h-screen overflow-hidden">
                <!-- Sidebar -->
                <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
                    <!-- Logo -->
                    <div class="h-16 flex items-center px-6 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">C</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">ChatEasy</span>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 overflow-y-auto py-4 px-3">
                        <div class="space-y-1">
                            @if(auth()->user()->role === 'superadmin')
                                <!-- Super Admin Menu -->
                                <div class="mb-4">
                                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Super Admin</p>
                                    
                                    <a href="{{ route('superadmin.dashboard') }}" class="{{ request()->routeIs('superadmin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        Dashboard
                                    </a>

                                    {{-- <a href="{{ route('superadmin.customers') }}" class="{{ request()->routeIs('superadmin.customers') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        Customers
                                    </a>

                                    <a href="{{ route('superadmin.analytics') }}" class="{{ request()->routeIs('superadmin.analytics') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Analytics
                                    </a> --}}

                                    <a href="{{ route('superadmin.stores') }}" class="{{ request()->routeIs('superadmin.stores*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        Stores
                                    </a>
                                </div>

                                <div class="border-t border-gray-200 pt-4 mb-4"></div>
                            @endif

                            <!-- User Menu -->
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">My Account</p>
                            
                            <a href="{{ route('stores.dashboard') }}" class="{{ request()->routeIs('stores.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Stores Management
                            </a>

                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                        </div>
                    </nav>

                    <!-- User Section -->
                    <div class="border-t border-gray-200 p-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-50 transition">
                                <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 text-left min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg py-1 border border-gray-200">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Top Header -->
                    <header class="bg-white border-b border-gray-200 h-16">
                        <div class="h-full px-6 flex items-center justify-between">
                            <div>
                                @isset($header)
                                    {{ $header }}
                                @endisset
                            </div>
                            
                            <div class="flex items-center gap-4">
                                @if(auth()->user()->role === 'superadmin')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                                        Super Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto bg-gray-50">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>
