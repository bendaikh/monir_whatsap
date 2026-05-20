<?php

namespace App\Services;

use App\Models\ProductLead;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadsExportService
{
    /**
     * Export leads as CSV matching the standard template:
     * Horodateur, Nom, Address, Ville, Tel, Price, Produit Ref, qte, Note
     */
    public function exportCsv(Collection $leads): StreamedResponse
    {
        $filename = 'leads_export_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Horodateur',
                'Nom',
                'Address',
                'Ville',
                'Tel',
                'Price',
                'Produit Ref',
                'qte',
                'Note',
            ]);

            foreach ($leads as $lead) {
                fputcsv($handle, $this->rowForLead($lead));
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
        $productRef = $product?->sku ?? '';

        return [
            $lead->created_at->format('d/m/Y H:i:s'),
            $lead->name ?? '',
            $lead->address ?? '',
            $lead->city ?? '',
            $lead->phone ?? '',
            $price !== '' ? (string) $price : '',
            $productRef,
            (string) $quantity,
            $lead->note ?? '',
        ];
    }
}
