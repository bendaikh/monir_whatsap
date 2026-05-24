<?php

namespace App\Jobs;

use App\Models\ProductLead;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PushLeadToGoogleSheet
{
    use Queueable;

    public function __construct(protected ProductLead $lead) {}

    public function handle(GoogleSheetsService $sheetsService): void
    {
        $lead = $this->lead->fresh(['product.googleSheetConnection']);

        if (! $lead || ! $lead->product) {
            Log::warning('Lead or product not found for Google Sheets export', ['lead_id' => $this->lead->id]);

            return;
        }

        $connection = $lead->product->googleSheetConnection;

        if (! $connection) {
            Log::info('Google Sheets export skipped: no sheet assigned to product', [
                'lead_id' => $lead->id,
                'product_id' => $lead->product_id,
            ]);

            return;
        }

        if (! $sheetsService->isConfigured()) {
            Log::error('Google Sheets export failed: credentials not configured in .env', [
                'lead_id' => $lead->id,
                'connection_id' => $connection->id,
            ]);

            return;
        }

        $result = $sheetsService->appendLead($lead, $connection);

        if ($result['success']) {
            Log::info('Lead exported to Google Sheet', [
                'lead_id' => $lead->id,
                'connection_id' => $connection->id,
            ]);
        } else {
            Log::error('Failed to export lead to Google Sheet', [
                'lead_id' => $lead->id,
                'connection_id' => $connection->id,
                'error' => $result['message'],
            ]);
        }
    }
}
