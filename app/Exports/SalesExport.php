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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    public function collection()
    {
        return Sale::with('user')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor Invoice',
            'Nama Pelanggan',
            'Tanggal',
            'Total Harga (Rp)',
            'Jumlah Bayar (Rp)',
            'Kembalian (Rp)',
            'Kasir',
            'Produk'
        ];
    }

    public function map($sale): array
    {
        $products = json_decode($sale->product_data, true) ?? [];
        $productDetails = array_map(function($product) {
            return $product['name'] . ' (Qty: ' . ($product['quantity'] ?? 0) . ', Harga: Rp' . number_format($product['price'] ?? 0) . ')';
        }, $products);

        return [
            $sale->id,
            $sale->invoice_number ?? '-',
            $sale->customer_name ?? '-',
            $sale->created_at->format('d-m-Y H:i:s'),
            $sale->total_amount ?? 0,
            $sale->payment_amount ?? 0,
            $sale->change_amount ?? 0,
            $sale->user->name ?? '-',
            implode("\n", $productDetails)
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],

            // Style untuk semua sel
            'A:I' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ],

            // Style khusus untuk kolom tertentu
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'E:G' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => '#,##0']
            ]
        ];
    }
}
