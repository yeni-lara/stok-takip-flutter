<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockStatusExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::with(['category', 'supplier']);

        // Filtreleri uygula
        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['supplier_id']) && $this->filters['supplier_id']) {
            $query->where('supplier_id', $this->filters['supplier_id']);
        }

        if (isset($this->filters['stock_status']) && $this->filters['stock_status']) {
            switch ($this->filters['stock_status']) {
                case 'low':
                    $query->whereRaw('current_stock <= min_stock');
                    break;
                case 'zero':
                    $query->where('current_stock', 0);
                    break;
                case 'normal':
                    $query->whereRaw('current_stock > min_stock');
                    break;
            }
        }

        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'Ürün Adı',
            'Barkod',
            'Kategori',
            'Tedarikçi',
            'Mevcut Stok',
            'Min. Stok',
            'Birim Fiyat (TL)',
            'KDV Oranı (%)',
            'Toplam Değer (TL)',
            'KDV Dahil Değer (TL)',
            'Stok Durumu',
            'Oluşturma Tarihi'
        ];
    }

    public function map($product): array
    {
        $totalValue = $product->current_stock * $product->unit_price;
        $totalValueWithTax = $totalValue * (1 + $product->tax_rate / 100);
        
        $stockStatus = 'Normal';
        if ($product->current_stock == 0) {
            $stockStatus = 'Stok Yok';
        } elseif ($product->current_stock <= $product->min_stock) {
            $stockStatus = 'Düşük Stok';
        }

        return [
            $product->name,
            $product->barcode ?: '-',
            $product->category->name ?? '-',
            $product->supplier->name ?? '-',
            number_format($product->current_stock, 0, ',', '.'),
            number_format($product->min_stock, 0, ',', '.'),
            number_format($product->unit_price, 2, ',', '.'),
            number_format($product->tax_rate, 1, ',', '.'),
            number_format($totalValue, 2, ',', '.'),
            number_format($totalValueWithTax, 2, ',', '.'),
            $stockStatus,
            $product->created_at->format('d.m.Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Stok Durumu Raporu';
    }
}
