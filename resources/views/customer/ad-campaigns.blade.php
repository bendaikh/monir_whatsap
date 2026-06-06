@extends('layouts.customer')

@section('title', 'Campaign Dashboard')

@php
    $kpiIcons = [
        'spend' => 'payments', 'leads' => 'groups', 'cpl' => 'price_change',
        'impressions' => 'visibility', 'clicks' => 'ads_click', 'link_clicks' => 'link',
        'ctr' => 'trending_up', 'cpc' => 'touch_app', 'cpm' => 'monetization_on', 'frequency' => 'repeat',
        'conversion_rate' => 'percent',
    ];
    $formatKpi = function ($kpi) {
        $v = $kpi['value'];
        return match ($kpi['format']) {
            'currency' => '$' . number_format($v, 2),
            'percent' => number_format($v, 2) . '%',
            'multiplier' => number_format($v, 2) . 'x',
            'decimal' => number_format($v, 2),
            default => number_format($v),
        };
    };
@endphp

@section('content')
<div class="max-w-[1600px] mx-auto space-y-6"
     x-data="campaignDashboard()"
     x-init="initCharts()">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-white">Campaign Dashboard</h1>
            <p class="text-gray-400 mt-1 text-sm">Monitor performance, profitability, and optimization opportunities</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('app.campaign-creator') }}" class="px-4 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-lg text-sm font-semibold inline-flex items-center gap-2 transition">
                <span class="material-icons text-base">auto_awesome</span> Create Campaign
            </a>
            <form action="{{ route('app.ad-campaigns.refresh') }}" method="POST" class="inline" onsubmit="window.showCampaignDashboardLoader()">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-lg text-sm font-medium inline-flex items-center gap-2 transition border border-white/10">
                    <span class="material-icons text-base">refresh</span> Refresh
                </button>
            </form>
            <button @click="showAnalysisModal = true" class="px-4 py-2.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition border border-emerald-500/30">
                <span class="material-icons text-base">psychology</span> AI Analysis
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
        <span class="material-icons text-base">check_circle</span>{{ session('success') }}
    </div>
    @endif

    @if(!empty($errors))
    <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 px-4 py-3 rounded-xl text-sm">
        <div class="flex items-start gap-2">
            <span class="material-icons text-base">warning</span>
            <ul class="space-y-1">
                @foreach($errors as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Date Range & Filters --}}
    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-4">
        <form method="GET" action="{{ route('app.ad-campaigns') }}" class="space-y-4" onsubmit="window.showCampaignDashboardLoader()">
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', '7d' => 'Last 7 Days', '30d' => 'Last 30 Days'] as $preset => $label)
                <button type="button" @click="applyPreset('{{ $preset }}'); window.showCampaignDashboardLoader()"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition"
                    :class="datePreset === '{{ $preset }}' ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400' : 'bg-white/5 border-white/10 text-gray-400 hover:text-white'">
                    {{ $label }}
                </button>
                @endforeach
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Platform</label>
                    <select name="platform" class="w-full bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="all" {{ $platform === 'all' ? 'selected' : '' }}>All Platforms</option>
                        <option value="facebook" {{ $platform === 'facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="tiktok" {{ $platform === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ $status === 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Account</label>
                    <select name="account_id" class="w-full bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                        <option value="all">All Accounts</option>
                        @foreach($facebookAccounts as $account)
                        <option value="fb_{{ $account->id }}" {{ $accountId === 'fb_'.$account->id ? 'selected' : '' }}>FB - {{ $account->ad_account_name ?? $account->ad_account_id }}</option>
                        @endforeach
                        @foreach($tiktokAccounts as $account)
                        <option value="tt_{{ $account->id }}" {{ $accountId === 'tt_'.$account->id ? 'selected' : '' }}>TT - {{ $account->advertiser_name ?? $account->advertiser_id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Date From</label>
                    <input type="date" name="date_from" x-ref="dateFrom" value="{{ $dateFrom ?? $dashboard['periodFrom'] }}" class="w-full bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Date To</label>
                    <input type="date" name="date_to" x-ref="dateTo" value="{{ $dateTo ?? $dashboard['periodTo'] }}" class="w-full bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition">Apply</button>
                    <a href="{{ route('app.ad-campaigns.export', request()->query()) }}" class="px-3 py-2 bg-white/10 hover:bg-white/15 text-gray-300 rounded-lg transition" title="Export to Excel">
                        <span class="material-icons text-base">download</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($dashboard['kpis'] as $kpi)
        <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-3 hover:border-white/20 transition">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] uppercase tracking-wide text-gray-500 font-medium leading-tight">{{ $kpi['label'] }}</span>
                <span class="material-icons text-sm text-gray-600">{{ $kpiIcons[$kpi['key']] ?? 'analytics' }}</span>
            </div>
            <div class="text-lg font-bold text-white truncate">{{ $formatKpi($kpi) }}</div>
            <div class="flex items-center gap-1 mt-1">
                @if($kpi['change'] != 0)
                <span class="material-icons text-xs {{ $kpi['positive'] ? 'text-emerald-400' : 'text-red-400' }}">{{ $kpi['change'] >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span>
                <span class="text-[10px] font-medium {{ $kpi['positive'] ? 'text-emerald-400' : 'text-red-400' }}">{{ $kpi['change'] >= 0 ? '+' : '' }}{{ $kpi['change'] }}%</span>
                @else
                <span class="text-[10px] text-gray-600">— vs prev period</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Grid: Tables + Sidebar --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Left Column (2/3) --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Campaign Performance Table --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h2 class="text-lg font-semibold text-white">Campaign Performance</h2>
                    <input type="text" x-model="searchQuery" placeholder="Search campaigns..."
                        class="bg-[#0a1628] border border-white/10 rounded-lg px-3 py-2 text-sm text-white w-full sm:w-64 focus:ring-emerald-500">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-white/5">
                                @foreach(['name' => 'Campaign', 'spend' => 'Spent', 'leads' => 'Leads', 'cpl' => 'CPL', 'ctr' => 'CTR', 'status' => 'Status', 'last_updated' => 'Updated'] as $col => $label)
                                <th class="px-4 py-3 font-medium cursor-pointer hover:text-white transition whitespace-nowrap" @click="sortBy('{{ $col }}')">
                                    {{ $label }}
                                    <span x-show="sortColumn === '{{ $col }}'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="text-emerald-400"></span>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($paginator as $campaign)
                            <tr class="hover:bg-white/[0.02] transition campaign-row"
                                data-name="{{ strtolower($campaign['name']) }}"
                                x-show="matchesSearch('{{ addslashes($campaign['name']) }}')">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white truncate max-w-[180px]">{{ $campaign['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $campaign['platform'] ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-white font-medium">${{ number_format($campaign['spend'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($campaign['leads']) }}</td>
                                <td class="px-4 py-3 text-gray-300">${{ number_format($campaign['cpl'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($campaign['ctr'], 2) }}%</td>
                                <td class="px-4 py-3">
                                    @php $st = strtolower($campaign['status']); @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ in_array($st, ['active','completed']) ? 'bg-emerald-500/20 text-emerald-400' : (in_array($st, ['paused','pending']) ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-500/20 text-gray-400') }}">
                                        {{ $campaign['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ isset($campaign['last_updated']) ? \Carbon\Carbon::parse($campaign['last_updated'])->diffForHumans() : '—' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">No campaigns found. Connect ad accounts or create a campaign.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($paginator->hasPages())
                <div class="px-5 py-3 border-t border-white/10 flex justify-between items-center text-sm text-gray-500">
                    <span>Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
                    <div>{{ $paginator->links() }}</div>
                </div>
                @endif
            </div>

            {{-- Top Performing Campaigns --}}
            @if(count($dashboard['topCampaigns']) > 0)
            <div class="bg-[#0f1c2e] border border-emerald-500/20 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex items-center gap-2">
                    <span class="material-icons text-emerald-400">emoji_events</span>
                    <h2 class="text-lg font-semibold text-white">Top Campaigns</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-white/5">
                                <th class="px-4 py-3">Campaign</th>
                                <th class="px-4 py-3">Spent</th>
                                <th class="px-4 py-3">Leads</th>
                                <th class="px-4 py-3">CPL</th>
                                <th class="px-4 py-3">ROAS</th>
                                <th class="px-4 py-3">CTR</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($dashboard['topCampaigns'] as $item)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $item['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['platform'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-300">${{ number_format($item['spend'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($item['leads']) }}</td>
                                <td class="px-4 py-3 text-gray-300">${{ number_format($item['cpl'], 2) }}</td>
                                <td class="px-4 py-3 text-emerald-400 font-medium">{{ number_format($item['roas'], 2) }}x</td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($item['ctr'], 2) }}%</td>
                                <td class="px-4 py-3">
                                    @php
                                        $topAction = $item['roas'] >= 3 ? 'Scale' : ($item['roas'] >= 1.5 ? 'Maintain' : 'Optimize');
                                        $topColor = match($topAction) {
                                            'Scale' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                            'Maintain' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                            default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-medium border {{ $topColor }}">{{ $topAction }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Underperforming Campaigns --}}
            @if(count($dashboard['underperforming']) > 0)
            <div class="bg-[#0f1c2e] border border-red-500/20 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex items-center gap-2">
                    <span class="material-icons text-red-400">warning</span>
                    <h2 class="text-lg font-semibold text-white">Needs Attention</h2>
                    <span class="ml-auto text-xs text-gray-500">{{ count($dashboard['underperforming']) }} campaigns</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-white/5">
                                <th class="px-4 py-3">Campaign</th>
                                <th class="px-4 py-3">Issue</th>
                                <th class="px-4 py-3">Spent</th>
                                <th class="px-4 py-3">CPL</th>
                                <th class="px-4 py-3">CTR</th>
                                <th class="px-4 py-3">ROAS</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($dashboard['underperforming'] as $item)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $item['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['platform'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @foreach($item['issues'] as $issue)
                                    <span class="inline-block px-2 py-0.5 rounded text-xs bg-red-500/20 text-red-400 mr-1">{{ $issue }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-gray-300">${{ number_format($item['spend'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-300">${{ number_format($item['cpl'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ number_format($item['ctr'], 2) }}%</td>
                                <td class="px-4 py-3 text-red-400">{{ number_format($item['roas'], 2) }}x</td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionColors = [
                                            'Pause Campaign' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                            'Scale Campaign' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                            'Improve Creatives' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                            'Optimize Audience' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                        ];
                                        $color = $actionColors[$item['recommendation']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-medium border {{ $color }}">{{ $item['recommendation'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Daily Performance Table --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h2 class="text-lg font-semibold text-white">Daily Performance Overview</h2>
                    <a href="{{ route('app.ad-campaigns.export', request()->query()) }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition">View Report</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-white/5">
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium text-right">Spent</th>
                                <th class="px-4 py-3 font-medium text-right">Leads</th>
                                <th class="px-4 py-3 font-medium text-right">Purchases</th>
                                <th class="px-4 py-3 font-medium text-right">CPP</th>
                                <th class="px-4 py-3 font-medium text-right">CPL</th>
                                <th class="px-4 py-3 font-medium text-right">Cost/Lead</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse(collect($dashboard['dailyPerformance'])->sortByDesc('full_date') as $day)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($day['full_date'])->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-right text-white font-medium">${{ number_format($day['spend'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">{{ number_format($day['leads']) }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">{{ number_format($day['purchases']) }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">${{ number_format($day['cpp'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">${{ number_format($day['cpl'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-300">${{ number_format($day['cost_per_lead'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">No daily performance data for the selected period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Funnel Analysis --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-white mb-4">Funnel Analysis</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-2">
                        @foreach($dashboard['funnel']['steps'] as $i => $step)
                        @php
                            $pct = $i === 0 ? 100 : ($dashboard['funnel']['steps'][0]['value'] > 0 ? ($step['value'] / $dashboard['funnel']['steps'][0]['value']) * 100 : 0);
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-36 text-xs text-gray-400 shrink-0">{{ $step['label'] }}</div>
                            <div class="flex-1 bg-white/5 rounded-full h-7 overflow-hidden relative">
                                <div class="h-full bg-gradient-to-r from-emerald-600 to-emerald-400 rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                                     style="width: {{ max(2, $pct) }}%">
                                    <span class="text-[10px] font-bold text-white">{{ number_format($step['value']) }}</span>
                                </div>
                            </div>
                            @if(isset($step['conversion_to_next']))
                            <div class="w-14 text-xs text-gray-500 text-right">{{ $step['conversion_to_next'] }}%</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="bg-[#0a1628] border border-white/10 rounded-xl p-4">
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Biggest Drop-off</div>
                        <div class="text-white font-semibold mb-1">{{ $dashboard['funnel']['biggest_drop']['from'] }} → {{ $dashboard['funnel']['biggest_drop']['to'] }}</div>
                        <div class="text-2xl font-bold text-red-400 mb-3">{{ $dashboard['funnel']['biggest_drop']['rate'] }}%</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">AI Recommendation</div>
                        <p class="text-sm text-gray-300 leading-relaxed">{{ $dashboard['funnel']['recommendation'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Audience & Placement --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10"><h2 class="text-lg font-semibold text-white">Audience Performance</h2></div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead><tr class="text-gray-500 uppercase border-b border-white/5">
                                <th class="px-3 py-2 text-left">Audience</th><th class="px-3 py-2 text-right">Spend</th><th class="px-3 py-2 text-right">Leads</th><th class="px-3 py-2 text-right">Cost of Lead</th>
                            </tr></thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($dashboard['audience'] as $row)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-3 py-2.5">
                                        <div class="text-white font-medium">{{ $row['audience'] }}</div>
                                        <div class="text-gray-600">{{ $row['age'] }} · {{ $row['gender'] }} · {{ $row['location'] }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">${{ number_format($row['spend'], 0) }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">{{ number_format($row['leads']) }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">${{ number_format($row['cpl'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10"><h2 class="text-lg font-semibold text-white">Placement Performance</h2></div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead><tr class="text-gray-500 uppercase border-b border-white/5">
                                <th class="px-3 py-2 text-left">Placement</th><th class="px-3 py-2 text-right">Spend</th><th class="px-3 py-2 text-right">Leads</th><th class="px-3 py-2 text-right">Cost per Lead</th>
                            </tr></thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($dashboard['placements'] as $row)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-3 py-2.5 text-white font-medium">{{ $row['placement'] }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">${{ number_format($row['spend'], 0) }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">{{ number_format($row['leads']) }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-300">${{ number_format($row['cpl'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['route' => 'app.campaign-creator', 'icon' => 'add_circle', 'label' => 'Create Campaign'],
                        ['route' => 'app.campaign-creator', 'icon' => 'content_copy', 'label' => 'Duplicate'],
                        ['route' => 'app.ad-campaigns', 'icon' => 'pause_circle', 'label' => 'Pause'],
                        ['route' => 'app.ad-campaigns', 'icon' => 'trending_up', 'label' => 'Scale'],
                        ['route' => 'app.ad-campaigns', 'icon' => 'edit', 'label' => 'Edit Budget'],
                        ['route' => 'app.ad-campaigns.export', 'icon' => 'file_download', 'label' => 'Export Report', 'params' => request()->query()],
                        ['route' => 'app.ad-campaigns', 'icon' => 'rule', 'label' => 'Automation'],
                    ] as $action)
                    <a href="{{ isset($action['params']) ? route($action['route'], $action['params']) : route($action['route']) }}"
                       class="flex flex-col items-center gap-1.5 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition border border-white/5 hover:border-white/15">
                        <span class="material-icons text-emerald-400">{{ $action['icon'] }}</span>
                        <span class="text-[10px] text-gray-400 text-center leading-tight">{{ $action['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Alerts Center --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-white mb-4">Alerts Center</h2>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($dashboard['alerts'] as $alert)
                    @php
                        $priorityColors = ['high' => 'border-red-500/40 bg-red-500/10', 'medium' => 'border-amber-500/40 bg-amber-500/10', 'low' => 'border-blue-500/40 bg-blue-500/10'];
                        $pc = $priorityColors[$alert['priority']] ?? 'border-white/10 bg-white/5';
                    @endphp
                    <div class="p-3 rounded-lg border {{ $pc }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="font-medium text-white text-sm">{{ $alert['title'] }}</div>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded
                                {{ $alert['priority'] === 'high' ? 'text-red-400' : ($alert['priority'] === 'medium' ? 'text-amber-400' : 'text-blue-400') }}">{{ $alert['priority'] }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $alert['message'] }}</p>
                        <div class="text-[10px] text-gray-600 mt-1">{{ $alert['time'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Creative Performance --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-white mb-4">Top Creatives</h2>
                <div class="space-y-3">
                    @forelse($dashboard['creatives'] as $creative)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/[0.03] transition">
                        <div class="w-12 h-12 rounded-lg shrink-0 flex items-center justify-center" style="background: {{ $creative['preview_color'] }}20; border: 1px solid {{ $creative['preview_color'] }}40">
                            <span class="material-icons text-lg" style="color: {{ $creative['preview_color'] }}">image</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-white truncate">{{ $creative['name'] }}</div>
                            <div class="text-xs text-gray-500">CTR {{ number_format($creative['ctr'], 2) }}% · {{ number_format($creative['purchases']) }} purchases</div>
                        </div>
                        @php
                            $statusColors = ['Winner' => 'text-emerald-400 bg-emerald-500/20', 'Strong' => 'text-blue-400 bg-blue-500/20', 'Fatigued' => 'text-amber-400 bg-amber-500/20', 'Weak' => 'text-red-400 bg-red-500/20'];
                            $sc = $statusColors[$creative['status']] ?? 'text-gray-400 bg-gray-500/20';
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sc }}">{{ $creative['status'] }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">No creative data available</p>
                    @endforelse
                </div>
                @if(count($dashboard['creatives']) > 0)
                @php $avgFatigue = collect($dashboard['creatives'])->avg('fatigue_score'); @endphp
                <div class="mt-4 pt-4 border-t border-white/10">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                        <span>Creative Fatigue</span>
                        <span class="{{ $avgFatigue >= 70 ? 'text-red-400' : 'text-emerald-400' }} font-bold">{{ round($avgFatigue) }}%</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $avgFatigue >= 70 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $avgFatigue }}%"></div>
                    </div>
                    @if($avgFatigue >= 70)
                    <p class="text-xs text-amber-400 mt-2">Replace creatives within 48 hours</p>
                    @endif
                </div>
                @endif
            </div>

            {{-- 7-Day Forecast --}}
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-white mb-1">7-Day Forecast</h2>
                <p class="text-xs text-gray-500 mb-4">Based on historical performance {{ $dashboard['forecast']['trend_percent'] >= 0 ? '+' : '' }}{{ $dashboard['forecast']['trend_percent'] }}% trend</p>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach([
                        ['label' => 'Leads', 'value' => number_format($dashboard['forecast']['projected_leads'])],
                        ['label' => 'Purchases', 'value' => number_format($dashboard['forecast']['projected_purchases'])],
                        ['label' => 'Revenue', 'value' => '$'.number_format($dashboard['forecast']['projected_revenue'], 0)],
                        ['label' => 'Profit', 'value' => '$'.number_format($dashboard['forecast']['projected_profit'], 0)],
                    ] as $f)
                    <div class="bg-[#0a1628] rounded-lg p-3 border border-white/5">
                        <div class="text-[10px] text-gray-500 uppercase">{{ $f['label'] }}</div>
                        <div class="text-lg font-bold text-white">{{ $f['value'] }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="h-40"><canvas id="forecastChart"></canvas></div>
            </div>
        </div>
    </div>

    {{-- AI Analysis Modal --}}
    <div x-show="showAnalysisModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showAnalysisModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/70" @click="showAnalysisModal = false"></div>
            <div class="relative bg-[#0f1c2e] border border-white/10 rounded-2xl shadow-2xl max-w-3xl w-full p-6 z-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-icons text-emerald-400">psychology</span>
                    <h3 class="text-xl font-bold text-white">AI Campaign Analysis</h3>
                </div>
                <div x-show="isAnalyzing" class="py-12 text-center">
                    <svg class="animate-spin h-8 w-8 text-emerald-400 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <p class="text-gray-400 mt-3 text-sm">Analyzing campaigns...</p>
                </div>
                <div x-show="analysisResult && !isAnalyzing" class="max-h-96 overflow-y-auto text-sm text-gray-300 prose-invert" x-html="analysisResult"></div>
                <div x-show="analysisError" class="text-red-400 text-sm" x-text="analysisError"></div>
                <div x-show="!isAnalyzing && !analysisResult && !analysisError" class="text-gray-500 text-sm py-8 text-center">Click Analyze to get AI-powered recommendations.</div>
                <div class="flex justify-end gap-3 mt-6">
                    <button @click="showAnalysisModal = false" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">Close</button>
                    <button @click="analyzeCampaigns()" :disabled="isAnalyzing" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition disabled:opacity-50">Analyze Now</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const forecastData = @json($dashboard['forecast']['chart']);

function campaignDashboard() {
    return {
        searchQuery: '',
        sortColumn: 'spend',
        sortDir: 'desc',
        datePreset: '{{ $dateFrom ? "custom" : "30d" }}',
        showAnalysisModal: false,
        isAnalyzing: false,
        analysisResult: null,
        analysisError: null,
        forecastChart: null,
        analyzeUrl: '{{ route('app.ad-campaigns.analyze') }}',
        csrfToken: '{{ csrf_token() }}',
        dateFrom: '{{ $dateFrom ?? $dashboard['periodFrom'] }}',
        dateTo: '{{ $dateTo ?? $dashboard['periodTo'] }}',

        matchesSearch(name) {
            if (!this.searchQuery) return true;
            return name.toLowerCase().includes(this.searchQuery.toLowerCase());
        },

        sortBy(col) {
            if (this.sortColumn === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            else { this.sortColumn = col; this.sortDir = 'desc'; }
        },

        applyPreset(preset) {
            const form = this.$refs.dateFrom?.closest('form');
            if (!form) return;
            const from = form.querySelector('[name=date_from]');
            const to = form.querySelector('[name=date_to]');
            const today = new Date();
            const fmt = d => d.toISOString().split('T')[0];
            this.datePreset = preset;
            if (preset === 'today') { from.value = fmt(today); to.value = fmt(today); }
            else if (preset === 'yesterday') { const y = new Date(today); y.setDate(y.getDate()-1); from.value = fmt(y); to.value = fmt(y); }
            else if (preset === '7d') { const s = new Date(today); s.setDate(s.getDate()-6); from.value = fmt(s); to.value = fmt(today); }
            else if (preset === '30d') { const s = new Date(today); s.setDate(s.getDate()-29); from.value = fmt(s); to.value = fmt(today); }
            form.submit();
        },

        initCharts() {
            this.$nextTick(() => {
                this.renderForecastChart();
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        if (typeof window.hideCampaignDashboardLoader === 'function') {
                            window.hideCampaignDashboardLoader();
                        }
                    }, 300);
                });
            });
        },

        chartColors() {
            return { grid: 'rgba(255,255,255,0.05)', text: '#9ca3af', emerald: '#10b981', blue: '#3b82f6', violet: '#8b5cf6' };
        },

        renderForecastChart() {
            const ctx = document.getElementById('forecastChart');
            if (!ctx) return;
            const c = this.chartColors();
            this.forecastChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: forecastData.map(d => d.day),
                    datasets: [
                        { label: 'Revenue', data: forecastData.map(d => d.revenue), borderColor: c.emerald, backgroundColor: c.emerald + '20', fill: true, tension: 0.4 },
                        { label: 'Profit', data: forecastData.map(d => d.profit), borderColor: c.violet, backgroundColor: c.violet + '20', fill: true, tension: 0.4 },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: c.text, boxWidth: 12, font: { size: 10 } } } },
                    scales: {
                        x: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 10 } } },
                        y: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 10 } }, beginAtZero: true }
                    }
                }
            });
        },

        analyzeCampaigns() {
            this.isAnalyzing = true;
            this.analysisResult = null;
            this.analysisError = null;
            fetch(this.analyzeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                body: JSON.stringify({ date_from: this.dateFrom || null, date_to: this.dateTo || null })
            })
            .then(r => r.json())
            .then(data => {
                this.isAnalyzing = false;
                if (data.error) this.analysisError = data.error;
                else if (data.analysis) this.analysisResult = this.formatAnalysis(data.analysis);
                else this.analysisError = 'No analysis received.';
            })
            .catch(e => { this.isAnalyzing = false; this.analysisError = e.message; });
        },

        formatAnalysis(text) {
            return text
                .replace(/\*\*(.+?)\*\*/g, '<strong class="text-white">$1</strong>')
                .replace(/##\s+(.+?)$/gm, '<h3 class="text-white font-bold mt-4 mb-2">$1</h3>')
                .replace(/^-\s+(.+?)$/gm, '<li class="ml-4 mb-1">$1</li>')
                .replace(/\n\n/g, '<br><br>')
                .replace(/\n/g, '<br>');
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
.pagination { display: flex; gap: 0.25rem; }
.pagination a, .pagination span { padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; }
.pagination a { background: rgba(255,255,255,0.05); color: #9ca3af; }
.pagination a:hover { background: rgba(255,255,255,0.1); color: white; }
.pagination span { background: rgba(16,185,129,0.2); color: #34d399; }
</style>
<script>
window.addEventListener('load', function () {
    setTimeout(function () {
        if (typeof window.hideCampaignDashboardLoader === 'function') {
            window.hideCampaignDashboardLoader();
        }
    }, 600);
});
</script>
@endsection
