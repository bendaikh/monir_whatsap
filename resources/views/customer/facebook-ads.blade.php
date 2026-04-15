@extends('layouts.customer')

@section('title', 'Facebook Ads Connect')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <h1 class="text-3xl font-bold text-gray-900">Facebook Ads Connect</h1>
        </div>
        <p class="text-gray-600">Connect your Facebook Ads accounts to manage campaigns</p>
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
                    <li>{{ $account->ad_account_name ?? $account->ad_account_id }}</li>
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
                <span class="material-icons text-blue-600">account_circle</span>
                Connected Ad Accounts ({{ $adAccounts->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account ID</th>
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
                                {{ $account->ad_account_name ?? 'Unnamed Account' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-600">{{ $account->ad_account_id }}</div>
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
                                <form action="{{ route('app.facebook-ads.test') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $account->id }}">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900" title="Test Connection">
                                        <span class="material-icons text-sm">check_circle</span>
                                    </button>
                                </form>
                                <form action="{{ route('app.facebook-ads.disconnect') }}" method="POST" class="inline">
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

    <form action="{{ route('app.facebook-ads.save') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-blue-600">add_circle</span>
                Add New Ad Account
            </h2>

            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-900 mb-1">How to Connect Facebook Ads</p>
                        <ol class="text-xs text-blue-700 space-y-1 list-decimal list-inside">
                            <li>Go to <a href="https://developers.facebook.com/tools/accesstoken/" target="_blank" class="underline hover:text-blue-900">Facebook Access Token Tool</a></li>
                            <li>Select your app and generate a User Access Token</li>
                            <li><strong class="text-red-700">IMPORTANT:</strong> Make sure to include these permissions: <code class="bg-blue-100 px-1 rounded font-semibold">ads_read</code>, <code class="bg-blue-100 px-1 rounded font-semibold">ads_management</code>, <code class="bg-blue-100 px-1 rounded font-semibold">business_management</code></li>
                            <li>Copy the access token and paste it below</li>
                            <li>Get your Ad Account ID from <a href="https://business.facebook.com/settings/ad-accounts" target="_blank" class="underline hover:text-blue-900">Facebook Business Manager</a></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="mb-6 bg-red-50 border-2 border-red-300 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-900 mb-2">⚠️ Required Permissions</p>
                        <p class="text-xs text-red-800 mb-2">Your access token MUST include these permissions to fetch campaigns:</p>
                        <ul class="text-xs text-red-800 space-y-1 list-disc list-inside ml-2">
                            <li><code class="bg-red-100 px-2 py-0.5 rounded font-semibold">ads_read</code> - To read campaign data</li>
                            <li><code class="bg-red-100 px-2 py-0.5 rounded font-semibold">ads_management</code> - To manage ad accounts</li>
                        </ul>
                        <p class="text-xs text-red-700 mt-2 font-medium">Without these permissions, your campaigns won't appear in the dashboard!</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Facebook Access Token *
                    </label>
                    <input type="password" name="facebook_access_token" placeholder="Enter your Facebook Access Token" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Your access token is encrypted and stored securely</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ad Account ID *
                    </label>
                    <input type="text" name="facebook_ad_account_id" placeholder="act_123456789" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Format: act_XXXXXXXXXX</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Facebook Page ID (Optional)
                    </label>
                    <input type="text" name="facebook_page_id" placeholder="123456789012345" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Business Manager ID (Optional)
                    </label>
                    <input type="text" name="facebook_business_id" placeholder="123456789012345" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
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
                    <strong>Getting Started:</strong> Make sure you have a Facebook Business Manager account and an ad account set up.
                </div>
            </div>
            <div class="flex gap-3">
                <span class="material-icons text-sm text-gray-400 mt-0.5">arrow_right</span>
                <div>
                    <strong>Permissions:</strong> Your access token needs ads_read, ads_management, and business_management permissions.
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
                    <strong>Documentation:</strong> Visit <a href="https://developers.facebook.com/docs/marketing-api" target="_blank" class="text-blue-600 hover:underline">Facebook Marketing API Docs</a> for more information.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
