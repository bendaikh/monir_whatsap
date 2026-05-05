<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">Leads</h2>
            <p class="text-sm text-gray-400 mt-1">Gérez les demandes de contact de vos produits</p>
        </div>
    </x-slot>

    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
        @if($leads->isEmpty())
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">Aucun lead pour le moment</h3>
                <p class="text-gray-500">Les demandes de contact des visiteurs apparaîtront ici</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#0a1628] border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Téléphone</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Langue</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($leads as $lead)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $lead->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        @if($lead->product && $lead->product->first_image)
                                            <img src="{{ $lead->product->first_image }}" alt="{{ $lead->product->name }}" class="w-10 h-10 rounded object-cover">
                                        @endif
                                        <span class="text-white font-medium">{{ $lead->product->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-white font-medium">{{ $lead->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="tel:{{ $lead->phone }}" class="flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $lead->phone }}
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="text-xs text-green-400 hover:text-green-300 transition mt-1 block">
                                        WhatsApp
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($lead->language === 'fr') bg-blue-500/20 text-blue-300
                                        @elseif($lead->language === 'en') bg-purple-500/20 text-purple-300
                                        @else bg-green-500/20 text-green-300
                                        @endif">
                                        {{ strtoupper($lead->language) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300 max-w-xs">
                                    @if($lead->note)
                                        <div class="truncate" title="{{ $lead->note }}">
                                            {{ $lead->note }}
                                        </div>
                                    @else
                                        <span class="text-gray-500 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button 
                                        type="button"
                                        onclick="showLeadDetails({{ $lead->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-400 hover:text-cyan-300 rounded-lg transition text-sm font-medium"
                                        title="Voir les détails de la commande"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="px-6 py-4 bg-[#0a1628] border-t border-white/10">
                    {{ $leads->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Lead Details Modal -->
    <div id="leadDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 transition-opacity" aria-hidden="true" onclick="closeLeadDetails()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-[#0f1c2e] rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/10">
                <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Détails de la commande
                    </h3>
                    <button type="button" onclick="closeLeadDetails()" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="leadDetailsContent" class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <div class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const leadsData = @json($leads->load(['product.activeVariations', 'product.activePromotions', 'selectedPromotion'])->keyBy('id'));
        
        function showLeadDetails(leadId) {
            const modal = document.getElementById('leadDetailsModal');
            const content = document.getElementById('leadDetailsContent');
            modal.classList.remove('hidden');
            
            const lead = leadsData[leadId];
            if (!lead) {
                content.innerHTML = '<div class="text-center py-8 text-red-400">Lead non trouvé</div>';
                return;
            }
            
            const product = lead.product;
            const promotions = product?.active_promotions || [];
            const variations = product?.active_variations || [];
            const selectedPromotion = lead.selected_promotion;
            
            let html = `
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-[#0a1628] rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Informations client
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-gray-500">Nom</span>
                                <p class="text-white font-medium">${lead.name || '-'}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Téléphone</span>
                                <p class="text-emerald-400 font-medium">${lead.phone || '-'}</p>
                            </div>
                            ${lead.email ? \`
                            <div>
                                <span class="text-xs text-gray-500">Email</span>
                                <p class="text-cyan-400 font-medium">${lead.email}</p>
                            </div>
                            \` : ''}
                            ${lead.city ? \`
                            <div>
                                <span class="text-xs text-gray-500">Ville</span>
                                <p class="text-white">${lead.city}</p>
                            </div>
                            \` : ''}
                            <div>
                                <span class="text-xs text-gray-500">Langue</span>
                                <p class="text-white">${(lead.language || 'N/A').toUpperCase()}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Date</span>
                                <p class="text-white">${new Date(lead.created_at).toLocaleString('fr-FR')}</p>
                            </div>
                        </div>
                        ${lead.address ? \`
                            <div class="mt-4 pt-4 border-t border-white/10">
                                <span class="text-xs text-gray-500">Adresse</span>
                                <p class="text-gray-300 mt-1">${lead.address}</p>
                            </div>
                        \` : ''}
                        ${lead.note ? \`
                            <div class="mt-4 pt-4 border-t border-white/10">
                                <span class="text-xs text-gray-500">Note</span>
                                <p class="text-gray-300 mt-1">${lead.note}</p>
                            </div>
                        \` : ''}
                    </div>
                    
                    <!-- Product Info -->
                    <div class="bg-[#0a1628] rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Produit
                        </h4>
                        <div class="flex items-start gap-4">
                            ${product?.first_image ? `<img src="${product.first_image}" alt="${product?.name || ''}" class="w-20 h-20 rounded-lg object-cover">` : ''}
                            <div class="flex-1">
                                <p class="text-white font-medium text-lg">${product?.name || 'N/A'}</p>
                                ${product?.nickname ? `<p class="text-gray-400 text-sm">${product.nickname}</p>` : ''}
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-emerald-400 font-bold">${product?.price || '0'} ${product?.landing_page_currency || 'MAD'}</span>
                                    ${product?.compare_at_price ? `<span class="text-gray-500 line-through text-sm">${product.compare_at_price} ${product?.landing_page_currency || 'MAD'}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
            `;
            
            // Selected Promotion
            if (selectedPromotion) {
                html += `
                    <div class="bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-lg p-4 border border-purple-500/30">
                        <h4 class="text-sm font-semibold text-purple-300 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            Promotion sélectionnée
                        </h4>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white font-medium">${selectedPromotion.label || 'Pack ' + selectedPromotion.min_quantity + ' unités'}</p>
                                <p class="text-sm text-gray-400">Quantité: ${selectedPromotion.min_quantity}${selectedPromotion.max_quantity ? ' - ' + selectedPromotion.max_quantity : '+'}</p>
                            </div>
                            <span class="text-2xl font-bold text-purple-300">${selectedPromotion.price} ${product?.landing_page_currency || 'MAD'}</span>
                        </div>
                    </div>
                `;
            }
            
            // All Promotions
            if (promotions.length > 0) {
                html += `
                    <div class="bg-[#0a1628] rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Toutes les promotions disponibles
                        </h4>
                        <div class="space-y-2">
                            ${promotions.map(promo => `
                                <div class="flex items-center justify-between py-2 px-3 rounded ${selectedPromotion?.id === promo.id ? 'bg-purple-500/20 border border-purple-500/30' : 'bg-[#0f1c2e]'}">
                                    <div>
                                        <span class="text-white font-medium">${promo.label || 'Pack ' + promo.min_quantity + ' unités'}</span>
                                        <span class="text-xs text-gray-500 ml-2">Qté: ${promo.min_quantity}${promo.max_quantity ? '-' + promo.max_quantity : '+'}</span>
                                        ${selectedPromotion?.id === promo.id ? '<span class="ml-2 px-2 py-0.5 text-xs bg-purple-500 text-white rounded">Sélectionné</span>' : ''}
                                    </div>
                                    <span class="text-emerald-400 font-bold">${promo.price} ${product?.landing_page_currency || 'MAD'}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Variations
            if (variations.length > 0) {
                html += `
                    <div class="bg-[#0a1628] rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Variantes du produit
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${variations.map(variation => {
                                const attrs = variation.attributes || {};
                                const attrString = Object.entries(attrs).map(([k, v]) => `${k}: ${v}`).join(', ');
                                return `
                                    <div class="flex items-center justify-between py-2 px-3 rounded bg-[#0f1c2e] ${variation.is_default ? 'ring-1 ring-cyan-500/50' : ''}">
                                        <div>
                                            <span class="text-white font-medium">${attrString || 'Variante ' + variation.id}</span>
                                            ${variation.sku ? `<span class="text-xs text-gray-500 ml-2">SKU: ${variation.sku}</span>` : ''}
                                            ${variation.is_default ? '<span class="ml-2 px-2 py-0.5 text-xs bg-cyan-500 text-white rounded">Par défaut</span>' : ''}
                                        </div>
                                        <div class="text-right">
                                            <span class="text-emerald-400 font-bold">${variation.price} MAD</span>
                                            <span class="text-xs text-gray-500 block">Stock: ${variation.stock}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Technical Info
            html += `
                    <div class="bg-[#0a1628] rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informations techniques
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">ID Lead</span>
                                <p class="text-white font-mono">#${lead.id}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">ID Produit</span>
                                <p class="text-white font-mono">#${product?.id || 'N/A'}</p>
                            </div>
                            ${lead.ip_address ? `
                                <div>
                                    <span class="text-gray-500">IP Address</span>
                                    <p class="text-white font-mono">${lead.ip_address}</p>
                                </div>
                            ` : ''}
                            ${lead.user_agent ? `
                                <div class="col-span-2">
                                    <span class="text-gray-500">User Agent</span>
                                    <p class="text-gray-300 text-xs truncate" title="${lead.user_agent}">${lead.user_agent}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            content.innerHTML = html;
        }
        
        function closeLeadDetails() {
            document.getElementById('leadDetailsModal').classList.add('hidden');
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLeadDetails();
        });
    </script>
    @endpush
</x-customer-layout>
