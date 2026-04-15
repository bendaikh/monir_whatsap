@extends('layouts.customer')

@section('title', 'TikTok Ads Connect')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-8 h-8 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.53.02C1.84-.117.02 1.79.02 11.82V23.8h11.96V.02h.55zm5.66 0c-.28 0-.53.02-.79.07v12.03H23.98v-.28c0-9.65-1.54-11.65-5.79-11.82zM12.53 23.98V24h11.45v-3.08H12.53v3.06z"/>
            </svg>
            <h1 class="text-3xl font-bold text-gray-900">TikTok Ads Connect</h1>
        </div>
        <p class="text-gray-600">Connect your TikTok Ads accounts to manage campaigns</p>
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

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <span class="material-icons">error</span>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $expiredAccounts = $adAccounts->filter(fn($acc) => !$acc->isTokenValid());
    @endphp

    @if($expiredAccounts->count() > 0)
    <div class="mb-6 bg-red-50 border-2 border-red-300 text-red-800 px-4 py-3 rounded-lg">
        <div class="flex items-start gap-2">
            <span class="material-icons">error</span>
            <div class="flex-1">
                <p class="font-semibold mb-1">{{ $expiredAccounts->count() }} Account(s) Need Reconnection</p>
                <p class="text-sm">
                    Your access token has expired for the following account(s). Please add a new access token below to continue managing your campaigns.
                </p>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach($expiredAccounts as $account)
                    <li>{{ $account->advertiser_name ?? $account->advertiser_id }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if($adAccounts->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span class="material-icons text-gray-900">account_circle</span>
                Connected Ad Accounts ({{ $adAccounts->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advertiser Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advertiser ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($adAccounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $account->advertiser_name ?? 'Unnamed Advertiser' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $account->advertiser_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($account->isTokenValid())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Expired
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $account->token_expires_at ? $account->token_expires_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('app.tiktok-ads.test') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $account->id }}">
                                    <button type="submit" class="text-gray-900 hover:text-gray-700" title="Test Connection">
                                        <span class="material-icons text-sm">check_circle</span>
                                    </button>
                                </form>
                                <form action="{{ route('app.tiktok-ads.disconnect') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $account->id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to disconnect this ad account?')" title="Disconnect">
                                        <span class="material-icons text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <form action="{{ route('app.tiktok-ads.save') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-gray-900">add_circle</span>
                Add New Ad Account
            </h2>

            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 mb-1">How to Connect TikTok Ads</p>
                        <ol class="text-xs text-blue-700 space-y-1 list-decimal list-inside">
                            <li>Go to <a href="https://ads.tiktok.com/marketing_api/auth" target="_blank" class="underline hover:text-blue-900">TikTok Ads Manager</a></li>
                            <li>Create or select your app in the developer portal</li>
                            <li>Generate an access token with Marketing API permissions</li>
                            <li>Copy the access token and paste it below</li>
                            <li>Get your Advertiser ID from <a href="https://ads.tiktok.com/i18n/dashboard" target="_blank" class="underline hover:text-blue-900">TikTok Ads Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        TikTok Access Token *
                    </label>
                    <input type="password" name="tiktok_access_token" placeholder="Enter your TikTok Access Token" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Your access token is encrypted and stored securely</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Advertiser ID *
                    </label>
                    <input type="text" name="tiktok_advertiser_id" placeholder="123456789012345" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Find this in your TikTok Ads Manager dashboard</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        App ID (Optional)
                    </label>
                    <input type="text" name="tiktok_app_id" placeholder="1234567890" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Your TikTok for Business app ID from the developer portal</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between gap-4">
            <a href="{{ route('app.dashboard') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                Back
            </a>
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                <span class="material-icons text-sm">add</span>
                Add Ad Account
            </button>
        </div>
    </form>

    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="material-icons text-purple-600">help_outline</span>
            Need Help?
        </h2>
        <div class="space-y-3 text-sm text-gray-600">
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>Getting Started:</strong> You need a TikTok for Business account and an approved developer app to access the Marketing API.
                </div>
            </div>
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>Authentication:</strong> TikTok uses OAuth 2.0 for authentication. Generate your access token from the developer portal.
                </div>
            </div>
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>Token Expiration:</strong> Access tokens typically expire after 60 days. You'll need to refresh them periodically.
                </div>
            </div>
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>API Limits:</strong> TikTok has rate limits on API calls. Check the <a href="https://ads.tiktok.com/marketing_api/docs" target="_blank" class="text-blue-600 hover:underline">TikTok Marketing API Docs</a> for details.
                </div>
            </div>
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>Documentation:</strong> Visit <a href="https://business-api.tiktok.com/portal/docs" target="_blank" class="text-blue-600 hover:underline">TikTok Business API Docs</a> for more information.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
