<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Sale::with('user')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor Invoice',
            'Nama Pelanggan',
            'Tanggal',
            'Total Harga (Rp)',
            'Kasir',
            'Produk'
        ];
    }

    public function map($sale): array
    {
        $products = [];
        $saleId = $sale->id ?? 'unknown';

        // Penanganan product_data
        if (empty($sale->product_data)) {
            Log::warning("Empty product_data for sale ID: {$saleId}");
        } else {
            try {
                $products = json_decode($sale->product_data, true);
                if (!is_array($products)) {
                    Log::warning("Invalid product_data format for sale ID: {$saleId}", [
                        'product_data' => $sale->product_data
                    ]);
                    $products = [];
                }
            } catch (\Exception $e) {
                Log::error("Error decoding product_data for sale ID: {$saleId}", [
                    'error' => $e->getMessage(),
                    'product_data' => $sale->product_data
                ]);
                $products = [];
            }
        }

        $productDetails = array_map(function ($product) {
            $name = $product['name'] ?? 'Unknown Product';
            $quantity = $product['quantity'] ?? 0;
            $price = $product['price'] ?? 0;
            return "{$name} (Qty: {$quantity}, Harga: Rp" . number_format($price, 0, ',', '.') . ")";
        }, $products);

        return [
            $sale->id ?? '-',
            $sale->invoice_number ?? '-',
            $sale->customer_name ?? '-',
            $sale->created_at ? $sale->created_at->format('d-m-Y H:i:s') : '-',
            $sale->total_amount ? number_format($sale->total_amount, 2, ',', '.') : '0',
            $sale->user?->name ?? '-',
            $productDetails ? implode("\n", $productDetails) : '-'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT, // Total Harga sudah diformat di map()
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
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
            'A:G' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]
        ];
    }
}