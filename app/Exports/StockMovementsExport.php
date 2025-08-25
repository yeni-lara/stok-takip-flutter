<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = StockMovement::with(['product', 'user', 'customer']);

        // Tarih filtresi
        if (isset($this->filters['date_from']) && isset($this->filters['date_to']) && 
            $this->filters['date_from'] && $this->filters['date_to']) {
            $query->whereBetween('created_at', [
                $this->filters['date_from'] . ' 00:00:00',
                $this->filters['date_to'] . ' 23:59:59'
            ]);
        } elseif (isset($this->filters['date_range']) && $this->filters['date_range']) {
            switch ($this->filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
            }
        }

        // Diğer filtreler
        if (isset($this->filters['type']) && $this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if (isset($this->filters['user_id']) && $this->filters['user_id']) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['customer_id']) && $this->filters['customer_id']) {
            $query->where('customer_id', $this->filters['customer_id']);
        }

        if (isset($this->filters['product_search']) && $this->filters['product_search']) {
            $search = $this->filters['product_search'];
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tarih',
            'Referans No',
            'İşlem Tipi',
            'Ürün Adı',
            'Ürün Barkodu',
            'Miktar',
            'Önceki Stok',
            'Yeni Stok',
            'Kullanıcı',
            'Müşteri',
            'Not',
            'Birim Fiyat (TL)',
            'Toplam Değer (TL)'
        ];
    }

    public function map($movement): array
    {
        $totalValue = $movement->quantity * ($movement->product->unit_price ?? 0);

        return [
            $movement->created_at->format('d.m.Y H:i'),
            $movement->reference_number ?: '-',
            ucfirst($movement->type),
            $movement->product->name ?? '-',
            $movement->product->barcode ?? '-',
            number_format($movement->quantity, 0, ',', '.'),
            number_format($movement->previous_stock, 0, ',', '.'),
            number_format($movement->new_stock, 0, ',', '.'),
            $movement->user->name ?? '-',
            $movement->customer->company_name ?? '-',
            $movement->note ?: '-',
            number_format($movement->product->unit_price ?? 0, 2, ',', '.'),
            number_format($totalValue, 2, ',', '.')
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
        return 'Stok Hareketleri Raporu';
    }
}
