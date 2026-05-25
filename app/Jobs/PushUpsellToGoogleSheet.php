<?php

namespace App\Jobs;

use App\Models\UpsellOrder;
use App\Services\GoogleSheetsService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PushUpsellToGoogleSheet
{
    use Queueable;

    public function __construct(protected UpsellOrder $order) {}

    public function handle(GoogleSheetsService $sheetsService): void
    {
        $order = $this->order->fresh(['lead.product.googleSheetConnection', 'upsellProduct']);

        if (! $order || ! $order->lead || ! $order->lead->product) {
            Log::warning('Upsell order or lead not found for Google Sheets export', [
                'upsell_order_id' => $this->order->id,
            ]);

            return;
        }

        $connection = $order->lead->product->googleSheetConnection;

        if (! $connection) {
            Log::info('Google Sheets export skipped: no sheet assigned to product', [
                'upsell_order_id' => $order->id,
                'lead_id' => $order->lead_id,
                'product_id' => $order->lead->product_id,
            ]);

            return;
        }

        if (! $sheetsService->isConfigured()) {
            Log::error('Google Sheets export failed: credentials not configured in .env', [
                'upsell_order_id' => $order->id,
                'connection_id' => $connection->id,
            ]);

            return;
        }

        $result = $sheetsService->appendUpsellOrder($order, $connection);

        if ($result['success']) {
            Log::info('Upsell order exported to Google Sheet', [
                'upsell_order_id' => $order->id,
                'lead_id' => $order->lead_id,
                'connection_id' => $connection->id,
            ]);
        } else {
            Log::error('Failed to export upsell order to Google Sheet', [
                'upsell_order_id' => $order->id,
                'lead_id' => $order->lead_id,
                'connection_id' => $connection->id,
                'error' => $result['message'],
            ]);
        }
    }
}
