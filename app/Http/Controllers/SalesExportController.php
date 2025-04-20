<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Sale::with('user')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Invoice Number',
            'Customer Name',
            'Date',
            'Total Amount',
            'Payment Amount',
            'Change Amount',
            'Cashier',
            'Products'
        ];
    }

    public function map($sale): array
    {
        $products = json_decode($sale->product_data, true);
        $productDetails = array_map(function($product) {
            return $product['name'] . ' (Qty: ' . $product['quantity'] . ', Price: Rp' . number_format($product['price']) . ')';
        }, $products);

        return [
            $sale->id,
            $sale->invoice_number,
            $sale->customer_name,
            $sale->created_at->format('d-m-Y H:i:s'),
            'Rp' . number_format($sale->total_amount, 0, ',', '.'),
            'Rp' . number_format($sale->payment_amount, 0, ',', '.'),
            'Rp' . number_format($sale->change_amount, 0, ',', '.'),
            $sale->user->name,
            implode("\n", $productDetails)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row style
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ],

            // All cells style
            'A:I' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ]
        ];
    }
}
