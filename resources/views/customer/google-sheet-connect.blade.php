<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">Google Sheet Connect</h2>
            <p class="text-sm text-gray-400 mt-1">Connect and manage multiple Google Sheets for lead exports</p>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @if (!$isConfigured)
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 space-y-3">
                    <p class="text-sm text-amber-200 font-medium">Google Sheets credentials are not set up yet.</p>
                    <p class="text-sm text-amber-200/90">Add one of these to your <code class="text-amber-100">.env</code> file, then restart the server:</p>
                    <pre class="text-xs text-amber-100 bg-[#0a1628] border border-amber-500/20 rounded-lg p-3 overflow-x-auto">GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-service-account.json</pre>
                    <p class="text-xs text-amber-200/80">Place your Google Cloud service account JSON file at that path. You can still add sheet connections below; export will work once credentials are configured.</p>
                </div>
            @elseif ($serviceAccountEmail)
                <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-4">
                    <p class="text-sm text-gray-300">
                        Share each Google Sheet with this service account email (Editor access):
                    </p>
                    <code class="mt-2 block text-emerald-400 text-sm break-all">{{ $serviceAccountEmail }}</code>
                </div>
            @endif

            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Add Google Sheet</h3>
                <form method="POST" action="{{ route('app.google-sheet-connect.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Connection name *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none"
                            placeholder="e.g. Morocco Leads, Summer Campaign">
                        <p class="mt-1 text-xs text-gray-500">A label to identify this sheet in your dashboard</p>
                        @error('name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="spreadsheet_id" class="block text-sm font-medium text-gray-300 mb-2">Spreadsheet ID or URL *</label>
                        <input type="text" name="spreadsheet_id" id="spreadsheet_id" required value="{{ old('spreadsheet_id') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none"
                            placeholder="https://docs.google.com/spreadsheets/d/... or spreadsheet ID">
                        @error('spreadsheet_id')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sheet_name" class="block text-sm font-medium text-gray-300 mb-2">Tab name *</label>
                        <input type="text" name="sheet_name" id="sheet_name" required value="{{ old('sheet_name', 'Sheet1') }}"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none"
                            placeholder="Sheet1">
                        <p class="mt-1 text-xs text-gray-500">The tab where leads will be appended (must match exactly)</p>
                        @error('sheet_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 bg-[#0a1628] text-emerald-500">
                        Active
                    </label>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition">
                        Add Connection
                    </button>
                </form>
            </div>

            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Connected Sheets ({{ $connections->count() }})</h3>
                @if ($connections->isEmpty())
                    <p class="text-gray-400 text-sm">No Google Sheets connected yet. Add one above.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($connections as $connection)
                            <div class="border border-white/10 rounded-lg p-4 bg-[#0a1628]" x-data="{ editing: false }">
                                <div x-show="!editing">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <h4 class="font-semibold text-white">{{ $connection->name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">Tab: {{ $connection->sheet_name }}</p>
                                            <p class="text-xs text-gray-500 font-mono mt-1 truncate max-w-md">{{ $connection->spreadsheet_id }}</p>
                                            <p class="text-xs text-gray-400 mt-2">{{ $connection->products_count }} product(s) assigned</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if ($connection->is_active)
                                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-300">Active</span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded-full bg-gray-500/20 text-gray-400">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-4">
                                        <button type="button" @click="editing = true" class="px-3 py-1.5 text-xs bg-white/5 hover:bg-white/10 text-white rounded-lg transition">Edit</button>
                                        @if ($isConfigured)
                                            <form method="POST" action="{{ route('app.google-sheet-connect.test', $connection) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 text-xs bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 rounded-lg transition">Test</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('app.google-sheet-connect.destroy', $connection) }}" class="inline" onsubmit="return confirm('Remove this connection? Products using it will be unassigned.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition">Remove</button>
                                        </form>
                                    </div>
                                </div>
                                <form x-show="editing" x-cloak method="POST" action="{{ route('app.google-sheet-connect.update', $connection) }}" class="space-y-3 mt-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $connection->name }}" required class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white text-sm">
                                    <input type="text" name="spreadsheet_id" value="{{ $connection->spreadsheet_id }}" required class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white text-sm">
                                    <input type="text" name="sheet_name" value="{{ $connection->sheet_name }}" required class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white text-sm">
                                    <label class="flex items-center gap-2 text-sm text-gray-300">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ $connection->is_active ? 'checked' : '' }} class="rounded border-white/20 text-emerald-500">
                                        Active
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-3 py-1.5 text-xs bg-emerald-500 text-white rounded-lg">Save</button>
                                        <button type="button" @click="editing = false" class="px-3 py-1.5 text-xs bg-white/10 text-white rounded-lg">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">How it works</h3>
                <ol class="space-y-3 text-sm text-gray-300">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center">1</span>
                        <span>Create a Google Cloud service account with Sheets API enabled</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center">2</span>
                        <span>Share your spreadsheet with the service account email</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center">3</span>
                        <span>Add each sheet here with a unique name</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center">4</span>
                        <span>Assign a sheet to each product in Products → Edit</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-center">5</span>
                        <span>Leads are exported automatically when customers submit forms</span>
                    </li>
                </ol>
            </div>
            <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Export columns</h3>
                <p class="text-xs text-gray-400 mb-2">Same format as CSV export:</p>
                <p class="text-xs text-gray-300">Horodateur, Nom, Address, Ville, Tel, Price, Produit Ref, qte, Note</p>
            </div>
        </div>
    </div>
</x-customer-layout>
