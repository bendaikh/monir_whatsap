<x-stores-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stores Management') }}
            </h2>
            <a href="{{ route('stores.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Store
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($activeWorkspace))
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <span class="font-semibold">Current Workspace:</span> {{ $activeWorkspace->name }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('workspaces.dashboard') }}" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded hover:bg-blue-200 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Switch Workspace
                        </a>
                    </div>
                </div>
            @endif

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Stores</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_stores'] }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Stores</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_stores'] }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Products</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_products'] }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stores List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Stores</h3>
                    
                    @if($stores->count() > 0)
                        <div class="space-y-4">
                            @foreach($stores as $store)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition {{ $currentStoreId == $store->id ? 'bg-blue-50 border-blue-300' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $store->name }}</h4>
                                                @if($currentStoreId == $store->id)
                                                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">
                                                        Currently Managing
                                                    </span>
                                                @endif
                                                @if($store->is_active)
                                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">
                                                @if($store->domain)
                                                    <span class="font-semibold text-indigo-600">{{ $store->domain }}</span>
                                                    <span class="text-gray-400 mx-2">•</span>
                                                    <span class="text-gray-500">{{ $store->subdomain }}.yourdomain.com</span>
                                                @else
                                                    {{ $store->subdomain }}.yourdomain.com
                                                @endif
                                            </p>
                                            @if($store->description)
                                                <p class="text-sm text-gray-500 mt-2">{{ $store->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-600">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                    {{ $store->products_count }} Products
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                    </svg>
                                                    {{ $store->categories_count }} Categories
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button onclick="openDomainModal({{ $store->id }}, '{{ $store->domain }}', '{{ $store->subdomain }}')" class="px-4 py-2 bg-indigo-100 text-indigo-700 text-sm font-medium rounded hover:bg-indigo-200 transition flex items-center gap-2" title="Setup Custom Domain">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                                </svg>
                                                @if($store->domain)
                                                    Domain Setup
                                                @else
                                                    Add Domain
                                                @endif
                                            </button>
                                            <a href="{{ route('store.home', $store->subdomain) }}" target="_blank" class="px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-medium rounded hover:bg-emerald-200 transition flex items-center gap-2" title="Visit Store Website">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Visit Store
                                            </a>
                                            @if($currentStoreId != $store->id)
                                                <form action="{{ route('stores.switch', $store) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                                        Manage Store
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('app.dashboard') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                                    Go to Dashboard
                                                </a>
                                            @endif
                                            <a href="{{ route('stores.edit', $store) }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('stores.destroy', $store) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this store? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded hover:bg-red-200 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No stores yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first store.</p>
                            <div class="mt-6">
                                <a href="{{ route('stores.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create Store
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Onboarding Modal -->
    <div id="domainModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Custom Domain Setup</h3>
                <button onclick="closeDomainModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="domainForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <!-- Current Status -->
                    <div id="currentDomainStatus" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Default URL</p>
                                <p class="text-sm text-blue-700" id="defaultUrl"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Domain Input -->
                    <div>
                        <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">Custom Domain</label>
                        <input type="text" name="domain" id="domainInput" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="www.mystore.com or mystore.com">
                        <p class="mt-2 text-sm text-gray-500">Enter your custom domain without http:// or https://</p>
                    </div>

                    <!-- Complete Setup Guide -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-5 max-h-[500px] overflow-y-auto">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h4 class="text-lg font-bold text-gray-900">Complete Setup Guide</h4>
                        </div>
                        
                        <!-- Step 1: DNS Configuration -->
                        <div class="mb-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-7 h-7 bg-blue-600 text-white rounded-full text-sm font-bold">1</div>
                                <h5 class="font-bold text-gray-900">Configure DNS Records</h5>
                            </div>
                            <div class="ml-9 space-y-3">
                                <p class="text-sm text-gray-700">In your domain registrar (Hostinger, GoDaddy, etc.), add these DNS records:</p>
                                
                                <!-- Delete existing CNAME warning -->
                                <div class="bg-red-50 border border-red-300 rounded p-3">
                                    <p class="text-xs font-semibold text-red-800 mb-1">⚠️ Important: Delete Any Existing CNAME Records First!</p>
                                    <p class="text-xs text-red-700">If you have a CNAME record for "www", delete it before adding A records (you can't have both).</p>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="bg-white rounded border border-blue-300 p-3">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">Record 1:</p>
                                        <div class="grid grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <span class="font-semibold text-gray-600">Type:</span>
                                                <p class="text-gray-900 font-mono font-bold">A</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-600">Name:</span>
                                                <p class="text-gray-900 font-mono font-bold">@</p>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="font-semibold text-gray-600">Points to:</span>
                                                <p class="text-blue-600 font-mono font-bold">178.16.128.28</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded border border-blue-300 p-3">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">Record 2:</p>
                                        <div class="grid grid-cols-4 gap-2 text-xs">
                                            <div>
                                                <span class="font-semibold text-gray-600">Type:</span>
                                                <p class="text-gray-900 font-mono font-bold">A</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-600">Name:</span>
                                                <p class="text-gray-900 font-mono font-bold">www</p>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="font-semibold text-gray-600">Points to:</span>
                                                <p class="text-blue-600 font-mono font-bold">178.16.128.28</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 border border-yellow-300 rounded p-2">
                                    <p class="text-xs text-yellow-800">⏳ <strong>Wait 1-24 hours</strong> for DNS propagation (usually 1-6 hours)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Save Domain Here -->
                        <div class="mb-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-7 h-7 bg-blue-600 text-white rounded-full text-sm font-bold">2</div>
                                <h5 class="font-bold text-gray-900">Save Your Domain</h5>
                            </div>
                            <div class="ml-9">
                                <p class="text-sm text-gray-700">Enter your domain above and click "Save Domain" below. ✓</p>
                            </div>
                        </div>

                        <!-- Step 3: Configure Web Server -->
                        <div class="mb-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-7 h-7 bg-orange-600 text-white rounded-full text-sm font-bold">3</div>
                                <h5 class="font-bold text-gray-900">Configure Web Server</h5>
                            </div>
                            <div class="ml-9 space-y-3">
                                <p class="text-sm font-semibold text-gray-800">Choose ONE method:</p>
                                
                                <!-- Method A: Hostinger Parked Domains -->
                                <div class="bg-green-50 border-2 border-green-400 rounded-lg p-3">
                                    <div class="flex items-start gap-2 mb-2">
                                        <span class="flex-shrink-0 text-lg">✅</span>
                                        <div>
                                            <p class="text-sm font-bold text-green-900">Method A: Hostinger Parked Domains (EASIEST)</p>
                                            <p class="text-xs text-green-800 mt-1">If your domain is in Hostinger:</p>
                                            <ol class="text-xs text-green-900 mt-2 space-y-1 list-decimal list-inside">
                                                <li>Log into <a href="https://hpanel.hostinger.com" target="_blank" class="underline font-semibold">Hostinger hPanel</a></li>
                                                <li>Go to <strong>Websites</strong> → Your hosting account</li>
                                                <li>Click <strong>Add Domain</strong> or <strong>Point Domain</strong></li>
                                                <li>Select: <strong>Point existing domain</strong></li>
                                                <li>Enter your domain and point to same hosting</li>
                                                <li>Save and wait 5-30 minutes ✓</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Method B: Contact Hosting Provider -->
                                <div class="bg-blue-50 border border-blue-300 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <span class="flex-shrink-0 text-lg">💬</span>
                                        <div>
                                            <p class="text-sm font-bold text-blue-900">Method B: Contact Your Hosting Provider</p>
                                            <p class="text-xs text-blue-800 mt-1">Send them this message:</p>
                                            <div class="bg-white border border-blue-200 rounded p-2 mt-2">
                                                <p class="text-xs font-mono text-gray-800">
                                                    "Please add <span class="font-bold text-blue-600">[YOUR-DOMAIN]</span> as an alias/parked domain to my hosting account. Point it to the same location as my main domain."
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Method C: SSH (Advanced) -->
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <span class="flex-shrink-0 text-lg">⚙️</span>
                                        <div class="w-full">
                                            <p class="text-sm font-bold text-gray-900">Method C: SSH Configuration (Advanced)</p>
                                            <details class="mt-2">
                                                <summary class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer font-semibold">Show SSH Instructions</summary>
                                                <div class="mt-2 space-y-2">
                                                    <p class="text-xs font-semibold text-gray-700">For Apache:</p>
                                                    <div class="bg-gray-900 text-green-400 rounded p-2 text-xs font-mono overflow-x-auto">
sudo nano /etc/apache2/sites-available/000-default.conf<br>
# Add: ServerAlias yourdomain.com www.yourdomain.com<br>
sudo systemctl restart apache2
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-700 mt-2">For Nginx:</p>
                                                    <div class="bg-gray-900 text-green-400 rounded p-2 text-xs font-mono overflow-x-auto">
sudo nano /etc/nginx/sites-available/default<br>
# Add to server_name: yourdomain.com www.yourdomain.com<br>
sudo nginx -t && sudo systemctl restart nginx
                                                    </div>
                                                </div>
                                            </details>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Test -->
                        <div class="mb-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-7 h-7 bg-purple-600 text-white rounded-full text-sm font-bold">4</div>
                                <h5 class="font-bold text-gray-900">Test Your Setup</h5>
                            </div>
                            <div class="ml-9 space-y-2">
                                <div class="bg-white border border-purple-300 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Check DNS:</p>
                                    <div class="bg-gray-900 text-green-400 rounded p-2 text-xs font-mono">
nslookup yourdomain.com<br>
<span class="text-gray-500"># Should show: 178.16.128.28</span>
                                    </div>
                                </div>
                                <div class="bg-white border border-purple-300 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Visit Your Domain:</p>
                                    <p class="text-xs text-gray-600">After Steps 1-3 are complete, visit <span class="font-mono font-bold text-purple-600">http://yourdomain.com</span></p>
                                    <p class="text-xs text-green-700 mt-1">✓ Should show your store!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: SSL (Optional) -->
                        <div class="mb-3">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center justify-center w-7 h-7 bg-green-600 text-white rounded-full text-sm font-bold">5</div>
                                <h5 class="font-bold text-gray-900">Install SSL Certificate (Recommended)</h5>
                            </div>
                            <div class="ml-9 space-y-2">
                                <p class="text-sm text-gray-700">After HTTP works, add HTTPS:</p>
                                <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside">
                                    <li><strong>Hostinger:</strong> Go to SSL section in hPanel → Enable free SSL</li>
                                    <li><strong>SSH:</strong> Run <code class="bg-gray-100 px-1 rounded font-mono">sudo certbot --apache -d yourdomain.com -d www.yourdomain.com</code></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Quick Help Box -->
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-300 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-indigo-900">Need Help?</p>
                                    <p class="text-xs text-indigo-800 mt-1">
                                        Most users should use <strong>Method A (Hostinger Parked Domains)</strong> - it's the easiest! 
                                        If you're stuck, contact your hosting provider's support - they can do Step 3 for you in 5-30 minutes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <button type="button" onclick="removeDomain()" id="removeDomainBtn" class="text-sm text-red-600 hover:text-red-700 font-medium hidden">
                            Remove Custom Domain
                        </button>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeDomainModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                Save Domain
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentStoreId = null;
        
        function openDomainModal(storeId, currentDomain, subdomain) {
            currentStoreId = storeId;
            const modal = document.getElementById('domainModal');
            const form = document.getElementById('domainForm');
            const input = document.getElementById('domainInput');
            const removeBtn = document.getElementById('removeDomainBtn');
            const defaultUrl = document.getElementById('defaultUrl');
            
            // Set form action
            form.action = '/stores/' + storeId + '/domain';
            
            // Set current values
            input.value = currentDomain || '';
            defaultUrl.textContent = subdomain + '.yourdomain.com';
            
            // Show/hide remove button
            if (currentDomain) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeDomainModal() {
            document.getElementById('domainModal').classList.add('hidden');
        }
        
        function removeDomain() {
            if (confirm('Are you sure you want to remove the custom domain? Your store will only be accessible via the default subdomain.')) {
                const form = document.getElementById('domainForm');
                const input = document.getElementById('domainInput');
                input.value = '';
                form.submit();
            }
        }
        
        // Close modal when clicking outside
        document.getElementById('domainModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDomainModal();
            }
        });
    </script>
</x-stores-layout>
