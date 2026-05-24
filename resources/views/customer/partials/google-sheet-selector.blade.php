@php
    $selectedId = old('google_sheet_connection_id', isset($product) ? $product->google_sheet_connection_id : null);
@endphp
<div>
    <label for="google_sheet_connection_id" class="block text-sm font-medium text-gray-300 mb-2">
        Google Sheet for leads
        <span class="text-xs text-gray-500 font-normal">(optional)</span>
    </label>
    <select
        id="google_sheet_connection_id"
        name="google_sheet_connection_id"
        class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
    >
        <option value="">— No Google Sheet —</option>
        @forelse ($googleSheetConnections ?? [] as $connection)
            <option value="{{ $connection->id }}" {{ (string) $selectedId === (string) $connection->id ? 'selected' : '' }}>
                {{ $connection->display_label }}
            </option>
        @empty
            <option value="" disabled>No sheets connected — add one in Connects → Google Sheet Connect</option>
        @endforelse
    </select>
    <p class="mt-1 text-xs text-gray-500">
        Leads from this product will be sent automatically to the selected sheet.
        @if (($googleSheetConnections ?? collect())->isEmpty())
            <a href="{{ route('app.google-sheet-connect') }}" class="text-emerald-400 hover:underline">Connect a Google Sheet</a>
        @endif
    </p>
    @error('google_sheet_connection_id')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
