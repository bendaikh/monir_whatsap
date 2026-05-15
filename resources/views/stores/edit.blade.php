<x-stores-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Store') }}
            </h2>
            <a href="{{ route('stores.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stores
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('stores.update', $store) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Store Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $store->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdomain *</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain', $store->subdomain) }}" required
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="mystore">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        .yourdomain.com
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Only letters, numbers, dashes, and underscores allowed</p>
                                @error('subdomain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="domain" class="block text-sm font-medium text-gray-700">Custom Domain (Optional)</label>
                                    <button type="button" onclick="toggleDomainHelp()" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Setup Guide
                                    </button>
                                </div>
                                <input type="text" name="domain" id="domain" value="{{ old('domain', $store->domain) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="tanzaniaship.com">
                                <p class="mt-1 text-sm text-gray-500">Enter your custom domain without http:// or https://</p>
                                @error('domain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <!-- Domain Setup Guide -->
                                <div id="domainHelpBox" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Custom Domain Setup Guide</h3>
                                            
                                            <!-- Step 1 -->
                                            <div class="mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</span>
                                                    <h4 class="font-semibold text-gray-900">Step 1: Configure DNS</h4>
                                                </div>
                                                <div class="ml-8 text-sm text-gray-700 space-y-2">
                                                    <p>In your domain registrar (Hostinger, GoDaddy, etc.), add these DNS records:</p>
                                                    <div class="bg-white rounded border border-gray-300 p-3 font-mono text-xs">
                                                        <div class="mb-2">
                                                            <strong>Record 1:</strong><br>
                                                            Type: A | Name: @ | Points to: <span class="text-blue-600 font-bold">178.16.128.28</span> | TTL: 14400
                                                        </div>
                                                        <div>
                                                            <strong>Record 2:</strong><br>
                                                            Type: A | Name: www | Points to: <span class="text-blue-600 font-bold">178.16.128.28</span> | TTL: 14400
                                                        </div>
                                                    </div>
                                                    <p class="text-orange-600 font-medium">⏳ Wait 1-24 hours for DNS propagation (usually 1-6 hours)</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Step 2 -->
                                            <div class="mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">✓</span>
                                                    <h4 class="font-semibold text-gray-900">Step 2: Add Domain to Store</h4>
                                                </div>
                                                <div class="ml-8 text-sm text-gray-700">
                                                    <p>You're doing this now! Just enter your domain above and click "Update Store".</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Step 3 -->
                                            <div class="mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                                    <h4 class="font-semibold text-gray-900">Step 3: Configure Web Server</h4>
                                                </div>
                                                <div class="ml-8 text-sm text-gray-700 space-y-3">
                                                    <p class="font-medium">Choose one option:</p>
                                                    
                                                    <div class="bg-white border border-gray-300 rounded p-3">
                                                        <p class="font-semibold text-green-600 mb-1">✅ Option A: Contact Your Hosting Provider (Easiest)</p>
                                                        <ol class="list-decimal list-inside space-y-1 text-sm">
                                                            <li>Contact your hosting support team</li>
                                                            <li>Say: "Please add <strong>{{ old('domain', $store->domain) ?: 'mydomain.com' }}</strong> as an alias/addon domain to my hosting account"</li>
                                                            <li>They'll configure it for you (5-30 minutes)</li>
                                                        </ol>
                                                    </div>
                                                    
                                                    <div class="bg-white border border-gray-300 rounded p-3">
                                                        <p class="font-semibold text-blue-600 mb-1">⚙️ Option B: Configure Yourself (SSH Access Required)</p>
                                                        
                                                        <div class="mt-2">
                                                            <p class="font-medium mb-1">For Apache:</p>
                                                            <div class="bg-gray-900 text-green-400 rounded p-2 font-mono text-xs overflow-x-auto">
                                                                # Edit your VirtualHost config<br>
                                                                sudo nano /etc/apache2/sites-available/000-default.conf<br><br>
                                                                # Add to ServerAlias line:<br>
                                                                ServerAlias {{ old('domain', $store->domain) ?: 'mydomain.com' }} www.{{ old('domain', $store->domain) ?: 'mydomain.com' }}<br><br>
                                                                # Restart Apache<br>
                                                                sudo systemctl restart apache2
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <p class="font-medium mb-1">For Nginx:</p>
                                                            <div class="bg-gray-900 text-green-400 rounded p-2 font-mono text-xs overflow-x-auto">
                                                                # Edit your server block<br>
                                                                sudo nano /etc/nginx/sites-available/default<br><br>
                                                                # Add to server_name line:<br>
                                                                server_name existing-domain.com {{ old('domain', $store->domain) ?: 'mydomain.com' }} www.{{ old('domain', $store->domain) ?: 'mydomain.com' }};<br><br>
                                                                # Test config<br>
                                                                sudo nginx -t<br><br>
                                                                # Restart Nginx<br>
                                                                sudo systemctl restart nginx
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Step 4 -->
                                            <div class="mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                                    <h4 class="font-semibold text-gray-900">Step 4: Install SSL Certificate (Recommended)</h4>
                                                </div>
                                                <div class="ml-8 text-sm text-gray-700">
                                                    <div class="bg-white border border-gray-300 rounded p-3">
                                                        <p class="font-medium mb-2">Using Let's Encrypt (Free):</p>
                                                        <div class="bg-gray-900 text-green-400 rounded p-2 font-mono text-xs">
                                                            # For Apache<br>
                                                            sudo certbot --apache -d {{ old('domain', $store->domain) ?: 'mydomain.com' }} -d www.{{ old('domain', $store->domain) ?: 'mydomain.com' }}<br><br>
                                                            # For Nginx<br>
                                                            sudo certbot --nginx -d {{ old('domain', $store->domain) ?: 'mydomain.com' }} -d www.{{ old('domain', $store->domain) ?: 'mydomain.com' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Testing -->
                                            <div class="mb-4 bg-green-50 border border-green-200 rounded p-3">
                                                <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Testing Your Setup
                                                </h4>
                                                <div class="text-sm text-gray-700 space-y-2">
                                                    <div>
                                                        <p class="font-medium">Check DNS Propagation:</p>
                                                        <div class="bg-white border border-gray-300 rounded p-2 font-mono text-xs mt-1">
                                                            nslookup {{ old('domain', $store->domain) ?: 'mydomain.com' }}
                                                        </div>
                                                        <p class="text-xs mt-1">Should show: 178.16.128.28</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Test in Browser:</p>
                                                        <p class="text-xs">Visit: <a href="#" class="text-blue-600 hover:underline">https://{{ old('domain', $store->domain) ?: 'mydomain.com' }}</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Quick Help -->
                                            <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                                <h4 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    Need Help?
                                                </h4>
                                                <p class="text-sm text-gray-700">
                                                    <strong>Most Important:</strong> If you're not comfortable with server configuration, 
                                                    <span class="text-green-600 font-semibold">contact your hosting provider's support</span> - they can do Step 3 for you!
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                function toggleDomainHelp() {
                                    const helpBox = document.getElementById('domainHelpBox');
                                    if (helpBox.classList.contains('hidden')) {
                                        helpBox.classList.remove('hidden');
                                        helpBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                    } else {
                                        helpBox.classList.add('hidden');
                                    }
                                }
                            </script>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $store->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $store->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Store is active
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('stores.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                                    Update Store
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-stores-layout>
