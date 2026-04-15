@extends('layouts.customer')

@section('title', 'Ad Campaigns')

@section('content')
<div class="max-w-7xl mx-auto">
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
        <form method="GET" action="{{ route('app.ad-campaigns') }}" class="flex flex-wrap gap-4 items-end">
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

            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                Apply Filters
            </button>
            
            @if($platform !== 'all' || $status !== 'all' || $accountId !== 'all')
            <a href="{{ route('app.ad-campaigns') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Campaigns List -->
    @if($campaigns->isEmpty())
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
                    @foreach($campaigns as $campaign)
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
                            @if(strtolower($campaign['status']) === 'active')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $campaign['status'] }}
                            </span>
                            @elseif(strtolower($campaign['status']) === 'paused')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $campaign['status'] }}
                            </span>
                            @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $campaign['status'] }}
                            </span>
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
            <div class="text-sm text-gray-600">
                Showing {{ $campaigns->count() }} campaign(s) • Data cached for 5 minutes • Last 30 days
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
