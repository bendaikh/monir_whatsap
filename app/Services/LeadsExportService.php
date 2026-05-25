<?php

namespace App\Services;

use App\Models\ProductLead;
use App\Models\UpsellOrder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadsExportService
{
    /**
     * Column order for Google Sheets and CSV export (A–J).
     */
    public function columnHeaders(): array
    {
        return [
            'Horodateur',
            'Produit Ref',
            'qte',
            'Nom',
            'Tel',
            'Address',
            'Price',
            'Ville',
            'Note',
            'Adresse e-mail',
        ];
    }

    public function exportCsv(Collection $leads): StreamedResponse
    {
        $filename = 'leads_export_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $this->columnHeaders());

            $leads->loadMissing(['product', 'selectedPromotion', 'upsellOrders.upsellProduct']);

            foreach ($leads as $lead) {
                fputcsv($handle, $this->rowForLead($lead));
                foreach ($lead->upsellOrders as $upsellOrder) {
                    fputcsv($handle, $this->rowForUpsellOrder($upsellOrder));
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function rowForLead(ProductLead $lead): array
    {
        $product = $lead->product;
        $promotion = $lead->selectedPromotion;

        $price = $promotion?->price ?? $product?->price ?? '';
        $quantity = $promotion?->min_quantity ?? 1;
        $productRef = $product?->sku ?: ($product?->name ?? '');

        return [
            $lead->created_at->format('d/m/Y H:i:s'),
            $productRef,
            (string) $quantity,
            $lead->name ?? '',
            $lead->phone ?? '',
            $lead->address ?? '',
            $this->formatPrice($price),
            $lead->city ?? '',
            $lead->note ?? '',
            $lead->email ?? '',
        ];
    }

    public function rowForUpsellOrder(UpsellOrder $order): array
    {
        $lead = $order->lead;
        $upsell = $order->upsellProduct;
        $price = $upsell?->price ?? '';

        return [
            $order->created_at->format('d/m/Y H:i:s'),
            $upsell?->title ?? '',
            '1',
            $lead?->name ?? '',
            $lead?->phone ?? '',
            $lead?->address ?? '',
            $this->formatPrice($price),
            $lead?->city ?? '',
            'Upsell',
            $lead?->email ?? '',
        ];
    }

    /**
     * Format monetary values for export: no decimals when .00, otherwise up to 2 decimals.
     */
    protected function formatPrice(mixed $price): string
    {
        if ($price === '' || $price === null) {
            return '';
        }

        $value = round((float) $price, 2);

        if ($value == floor($value)) {
            return (string) (int) $value;
        }

        return number_format($value, 2, '.', '');
    }
}
