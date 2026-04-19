@extends('layouts.customer')

@section('title', 'Ad Campaigns')

@section('content')
<div class="max-w-7xl mx-auto" 
     x-data="campaignAnalysis()" 
     x-init="init()">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Ad Campaigns</h1>
                <p class="text-gray-600 mt-1">Monitor and manage your advertising campaigns</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('app.campaign-creator') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg font-bold transition inline-flex items-center gap-2 shadow-lg">
                    <span class="material-icons text-sm">auto_awesome</span>
                    Create with AI
                </a>
                <form action="{{ route('app.ad-campaigns.refresh') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                        <span class="material-icons text-sm">refresh</span>
                        Refresh Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    
    @if($paginator->filter(fn($c) => in_array(strtolower($c['status']), ['pending', 'processing']))->isNotEmpty())
    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="animate-spin h-5 w-5 text-blue-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Campaigns are being generated. This page will auto-refresh every 10 seconds. You can continue creating more campaigns!</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">error</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(!empty($errors) && count($errors) > 0)
    <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <span class="material-icons">warning</span>
            <div class="flex-1">
                <p class="font-semibold mb-2">Attention Required:</p>
                <ul class="list-disc list-inside space-y-2 text-sm">
                    @foreach($errors as $error)
                    <li>
                        {{ $error }}
                        @if(str_contains($error, 'expired') || str_contains($error, 'invalid'))
                            @if(str_contains($error, 'Facebook'))
                            <a href="{{ route('app.facebook-ads') }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium ml-2">
                                <span>Reconnect Facebook</span>
                                <span class="material-icons text-sm">arrow_forward</span>
                            </a>
                            @elseif(str_contains($error, 'TikTok'))
                            <a href="{{ route('app.tiktok-ads') }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium ml-2">
                                <span>Reconnect TikTok</span>
                                <span class="material-icons text-sm">arrow_forward</span>
                            </a>
                            @endif
                        @endif
                    </li>
                    @endforeach
                </ul>
                <p class="text-xs mt-3 pt-2 border-t border-yellow-300">Check <code class="bg-yellow-100 px-2 py-1 rounded">storage/logs/laravel.log</code> for more details.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Spend</span>
                <span class="material-icons text-green-600">payments</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($totalSpend, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Last 30 days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Impressions</span>
                <span class="material-icons text-blue-600">visibility</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalImpressions) }}</div>
            <p class="text-xs text-gray-500 mt-1">Last 30 days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Clicks</span>
                <span class="material-icons text-purple-600">ads_click</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalClicks) }}</div>
            <p class="text-xs text-gray-500 mt-1">Last 30 days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Conversions</span>
                <span class="material-icons text-orange-600">shopping_cart</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalConversions) }}</div>
            <p class="text-xs text-gray-500 mt-1">Last 30 days</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('app.ad-campaigns') }}" class="space-y-4">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <select name="platform" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <option value="all" {{ $platform === 'all' ? 'selected' : '' }}>All Platforms</option>
                        <option value="facebook" {{ $platform === 'facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="tiktok" {{ $platform === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="generating" {{ $status === 'generating' ? 'selected' : '' }}>Generating</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ $status === 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
                    <select name="account_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <option value="all" {{ $accountId === 'all' ? 'selected' : '' }}>All Accounts</option>
                        @foreach($facebookAccounts as $account)
                            <option value="fb_{{ $account->id }}" {{ $accountId === 'fb_' . $account->id ? 'selected' : '' }}>
                                Facebook - {{ $account->ad_account_name ?? $account->ad_account_id }}
                            </option>
                        @endforeach
                        @foreach($tiktokAccounts as $account)
                            <option value="tt_{{ $account->id }}" {{ $accountId === 'tt_' . $account->id ? 'selected' : '' }}>
                                TikTok - {{ $account->advertiser_name ?? $account->advertiser_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                    <select name="per_page" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    Apply Filters
                </button>
                
                @if($platform !== 'all' || $status !== 'all' || $accountId !== 'all' || $dateFrom || $dateTo)
                <a href="{{ route('app.ad-campaigns') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                    Clear
                </a>
                @endif
                
                <button type="button" @click="showAnalysisModal = true" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                    <span class="material-icons text-sm">psychology</span>
                    Analyze Campaigns
                </button>
            </div>
        </form>
    </div>

    <!-- Campaigns List -->
    @if($paginator->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <span class="material-icons text-gray-400 text-6xl mb-4">campaign</span>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Campaigns Found</h3>
        <p class="text-gray-600 mb-6">
            @if($facebookAccounts->isEmpty() && $tiktokAccounts->isEmpty())
                Connect your ad accounts to see your campaigns here.
            @else
                Your connected accounts don't have any campaigns, or there was an error fetching them.
            @endif
        </p>
        
        @if($facebookAccounts->isEmpty() || $tiktokAccounts->isEmpty())
        <div class="flex gap-4 justify-center mb-8">
            @if($facebookAccounts->isEmpty())
            <a href="{{ route('app.facebook-ads') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                Connect Facebook Ads
            </a>
            @endif
            @if($tiktokAccounts->isEmpty())
            <a href="{{ route('app.tiktok-ads') }}" class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-medium transition">
                Connect TikTok Ads
            </a>
            @endif
        </div>
        @endif
        
        @if($facebookAccounts->isNotEmpty() || $tiktokAccounts->isNotEmpty())
        <div class="mt-8 p-6 bg-gray-50 rounded-lg text-left max-w-2xl mx-auto">
            <h4 class="font-semibold text-gray-900 mb-4">Connected Accounts:</h4>
            
            @if($facebookAccounts->isNotEmpty())
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Facebook Accounts ({{ $facebookAccounts->count() }}):</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    @foreach($facebookAccounts as $account)
                    <li>• {{ $account->ad_account_name ?? $account->ad_account_id }} (ID: {{ $account->ad_account_id }})</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            @if($tiktokAccounts->isNotEmpty())
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">TikTok Accounts ({{ $tiktokAccounts->count() }}):</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    @foreach($tiktokAccounts as $account)
                    <li>• {{ $account->advertiser_name ?? $account->advertiser_id }} (ID: {{ $account->advertiser_id }})</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <form action="{{ route('app.ad-campaigns.refresh') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Try refreshing the data →
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Spend</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Impressions</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Clicks</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">CTR</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">CPC</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Conversions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($paginator as $campaign)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    @if($campaign['platform'] === 'Facebook')
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    @else
                                    <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.53.02C1.84-.117.02 1.79.02 11.82V23.8h11.96V.02h.55zm5.66 0c-.28 0-.53.02-.79.07v12.03H23.98v-.28c0-9.65-1.54-11.65-5.79-11.82zM12.53 23.98V24h11.45v-3.08H12.53v3.06z"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $campaign['name'] }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $campaign['account_name'] }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $campaign['objective'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusLower = strtolower($campaign['status']);
                            @endphp
                            
                            @if($statusLower === 'active')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $campaign['status'] }}
                                </span>
                            @elseif($statusLower === 'paused')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $campaign['status'] }}
                                </span>
                            @elseif(in_array($statusLower, ['pending', 'processing']))
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 animate-pulse">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ $statusLower === 'pending' ? 'Generating' : 'Creating' }}
                                </span>
                            @elseif($statusLower === 'completed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <span class="material-icons text-xs mr-1">check_circle</span>
                                    Completed
                                </span>
                            @elseif($statusLower === 'failed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <span class="material-icons text-xs mr-1">error</span>
                                    Failed
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $campaign['status'] }}
                                </span>
                            @endif
                            
                            @if(!empty($campaign['error_message']))
                                <div class="text-xs text-red-600 mt-1" title="{{ $campaign['error_message'] }}">
                                    View error
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($campaign['daily_budget'] > 0)
                                    ${{ number_format($campaign['daily_budget'], 2) }}/day
                                @elseif($campaign['lifetime_budget'] > 0)
                                    ${{ number_format($campaign['lifetime_budget'], 2) }} lifetime
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-gray-900">${{ number_format($campaign['spend'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($campaign['impressions']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($campaign['clicks']) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($campaign['ctr'], 2) }}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">${{ number_format($campaign['cpc'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-green-600">{{ number_format($campaign['conversions']) }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} campaign(s) • Data cached for 5 minutes
                    @if($dateFrom && $dateTo)
                        • {{ $dateFrom }} to {{ $dateTo }}
                    @else
                        • Last 30 days
                    @endif
                </div>
                
                @if($paginator->hasPages())
                <div class="flex gap-2">
                    {{ $paginator->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    <!-- AI Analysis Modal -->
    <div x-show="showAnalysisModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="showAnalysisModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showAnalysisModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="showAnalysisModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showAnalysisModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons text-purple-600">psychology</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                AI Campaign Analysis
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Our AI media buyer will analyze your campaigns and provide recommendations on scaling, optimization, and budget allocation.
                                </p>
                            </div>
                            
                            <!-- Analysis Result -->
                            <div x-show="!isAnalyzing && analysisResult && !analysisError" class="mt-4 bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                                <div class="prose prose-sm max-w-none text-gray-800">
                                    <div x-html="analysisResult"></div>
                                </div>
                            </div>
                            
                            <!-- Loading State -->
                            <div x-show="isAnalyzing" class="mt-4 text-center py-8">
                                <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm text-gray-600 mt-3">Analyzing campaigns... This may take up to a minute.</p>
                            </div>
                            
                            <!-- Error State -->
                            <div x-show="analysisError && !isAnalyzing" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm text-red-800" x-text="analysisError"></p>
                            </div>
                            
                            <!-- Initial State -->
                            <div x-show="!isAnalyzing && !analysisResult && !analysisError" class="mt-4 text-center py-8">
                                <p class="text-sm text-gray-600">Click "Analyze Now" to start the AI analysis of your campaigns.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button x-show="!isAnalyzing" 
                            @click="analyzeCampaigns()" 
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Analyze Now
                    </button>
                    <button @click="showAnalysisModal = false; analysisResult = null; analysisError = null;" 
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function campaignAnalysis() {
    return {
        hasGeneratingCampaigns: {{ $paginator->filter(fn($c) => in_array(strtolower($c['status']), ['pending', 'processing']))->isNotEmpty() ? 'true' : 'false' }},
        showAnalysisModal: false,
        isAnalyzing: false,
        analysisResult: null,
        analysisError: null,
        dateFrom: '{{ $dateFrom ?? '' }}',
        dateTo: '{{ $dateTo ?? '' }}',
        analyzeUrl: '{{ route('app.ad-campaigns.analyze') }}',
        csrfToken: '{{ csrf_token() }}',
        
        init() {
            if (this.hasGeneratingCampaigns) {
                setTimeout(() => {
                    window.location.reload();
                }, 10000);
            }
        },
        
        analyzeCampaigns() {
            console.log('Starting campaign analysis...', { 
                dateFrom: this.dateFrom, 
                dateTo: this.dateTo 
            });
            
            this.isAnalyzing = true;
            this.analysisResult = null;
            this.analysisError = null;
            
            fetch(this.analyzeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({
                    date_from: this.dateFrom || null,
                    date_to: this.dateTo || null
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                console.log('Analysis text length:', data.analysis ? data.analysis.length : 0);
                
                this.isAnalyzing = false;
                
                if (data.error) {
                    console.error('Analysis error:', data.error);
                    this.analysisError = data.error;
                } else if (data.analysis) {
                    console.log('Analysis successful, formatting...');
                    const formatted = this.formatAnalysis(data.analysis);
                    console.log('Formatted HTML length:', formatted.length);
                    console.log('Formatted HTML preview:', formatted.substring(0, 200));
                    this.analysisResult = formatted;
                    console.log('analysisResult set:', this.analysisResult ? 'YES' : 'NO');
                } else {
                    console.error('No analysis in response');
                    this.analysisError = 'No analysis data received from the server.';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                this.isAnalyzing = false;
                this.analysisError = 'An error occurred while analyzing campaigns: ' + error.message;
            });
        },
        
        formatAnalysis(text) {
            if (!text) {
                console.error('formatAnalysis: No text provided');
                return '<p class="text-red-600">Error: No analysis text to format</p>';
            }
            
            console.log('formatAnalysis: Input text length:', text.length);
            
            try {
                // Convert markdown-style formatting to HTML
                let html = text
                    .replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>') // Bold+Italic
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>') // Bold
                    .replace(/\*(.+?)\*/g, '<em>$1</em>') // Italic
                    .replace(/###\s+(.+?)$/gm, '<h3 class="text-lg font-bold mt-4 mb-2">$1</h3>') // H3
                    .replace(/##\s+(.+?)$/gm, '<h2 class="text-xl font-bold mt-4 mb-2">$1</h2>') // H2
                    .replace(/^(\d+)\.\s+(.+?)$/gm, '<div class="ml-4 mb-2"><strong>$1.</strong> $2</div>') // Numbered lists
                    .replace(/^-\s+(.+?)$/gm, '<li class="ml-6 mb-1">$1</li>') // Bullet lists
                    .replace(/\n\n/g, '</p><p class="mb-2">') // Paragraphs
                    .replace(/\n/g, '<br>'); // Line breaks
                
                // Wrap in paragraph tags
                html = '<div class="text-gray-800">' + html + '</div>';
                
                // Fix list formatting
                html = html.replace(/<li/g, '<ul class="list-disc ml-4 mb-2"><li').replace(/<\/li>(?!<li)/g, '</li></ul>');
                
                console.log('formatAnalysis: Output HTML length:', html.length);
                
                return html;
            } catch (error) {
                console.error('formatAnalysis error:', error);
                return '<p class="text-red-600">Error formatting analysis: ' + error.message + '</p>';
            }
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
