<?php

namespace App\Http\Controllers;

use App\Models\GoogleSheetConnection;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class GoogleSheetConnectController extends Controller
{
    protected function getStoreId(): ?int
    {
        return session('active_store_id');
    }

    protected function connectionsQuery()
    {
        $storeId = $this->getStoreId();

        return GoogleSheetConnection::where('user_id', auth()->id())
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->withCount('products')
            ->orderBy('name');
    }

    public function index(GoogleSheetsService $sheetsService)
    {
        $connections = $this->connectionsQuery()->get();
        $serviceAccountEmail = $sheetsService->getServiceAccountEmail();
        $isConfigured = $sheetsService->isConfigured();

        return view('customer.google-sheet-connect', compact('connections', 'serviceAccountEmail', 'isConfigured'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'spreadsheet_id' => 'required|string|max:255',
            'sheet_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        GoogleSheetConnection::create([
            'user_id' => auth()->id(),
            'store_id' => $this->getStoreId(),
            'name' => $validated['name'],
            'spreadsheet_id' => $this->extractSpreadsheetId($validated['spreadsheet_id']),
            'sheet_name' => $validated['sheet_name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('app.google-sheet-connect')
            ->with('success', 'Google Sheet connection added successfully.');
    }

    public function update(Request $request, GoogleSheetConnection $connection)
    {
        $this->authorizeConnection($connection);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'spreadsheet_id' => 'required|string|max:255',
            'sheet_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $connection->update([
            'name' => $validated['name'],
            'spreadsheet_id' => $this->extractSpreadsheetId($validated['spreadsheet_id']),
            'sheet_name' => $validated['sheet_name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('app.google-sheet-connect')
            ->with('success', 'Google Sheet connection updated successfully.');
    }

    public function destroy(GoogleSheetConnection $connection)
    {
        $this->authorizeConnection($connection);

        $connection->products()->update(['google_sheet_connection_id' => null]);
        $connection->delete();

        return redirect()
            ->route('app.google-sheet-connect')
            ->with('success', 'Google Sheet connection removed.');
    }

    public function test(GoogleSheetConnection $connection, GoogleSheetsService $sheetsService)
    {
        $this->authorizeConnection($connection);

        $result = $sheetsService->testConnection($connection);

        return redirect()
            ->route('app.google-sheet-connect')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    protected function authorizeConnection(GoogleSheetConnection $connection): void
    {
        if ($connection->user_id !== auth()->id()) {
            abort(403);
        }

        $storeId = $this->getStoreId();
        if ($storeId && $connection->store_id !== $storeId) {
            abort(403);
        }
    }

    protected function extractSpreadsheetId(string $input): string
    {
        if (preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $input, $matches)) {
            return $matches[1];
        }

        return trim($input);
    }
}
