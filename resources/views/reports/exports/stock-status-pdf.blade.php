<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Durum Raporu - {{ date('d.m.Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            font-size: 8px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            color: white;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Stok Durum Raporu</p>
        <p>{{ date('d.m.Y H:i') }} - Toplam {{ $products->count() }} Ürün</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Rapor Tarihi:</td>
            <td>{{ date('d.m.Y H:i') }}</td>
            <td class="label">Toplam Ürün:</td>
            <td>{{ number_format($products->count()) }}</td>
        </tr>
        <tr>
            <td class="label">Normal Stok:</td>
            <td>{{ $products->filter(function($p) { return $p->current_stock > $p->min_stock; })->count() }}</td>
            <td class="label">Düşük Stok:</td>
            <td>{{ $products->filter(function($p) { return $p->current_stock <= $p->min_stock && $p->current_stock > 0; })->count() }}</td>
        </tr>
        <tr>
            <td class="label">Stok Yok:</td>
            <td>{{ $products->filter(function($p) { return $p->current_stock == 0; })->count() }}</td>
            <td class="label">Toplam Değer:</td>
            <td>{{ number_format($products->sum(function($p) { return $p->current_stock * $p->unit_price; }), 2) }} TL</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Ürün Adı</th>
                <th style="width: 12%;">Barkod</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 10%;">Tedarikçi</th>
                <th style="width: 8%;">Mevcut Stok</th>
                <th style="width: 8%;">Min. Stok</th>
                <th style="width: 10%;">Birim Fiyat</th>
                <th style="width: 12%;">Toplam Değer</th>
                <th style="width: 10%;">Durum</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                @php
                    $totalValue = $product->current_stock * $product->unit_price;
                    $stockStatus = $product->current_stock == 0 ? 'zero' : 
                                  ($product->current_stock <= $product->min_stock ? 'low' : 'normal');
                @endphp
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->barcode ?: '-' }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->supplier->name ?? '-' }}</td>
                    <td class="text-center">{{ number_format($product->current_stock) }}</td>
                    <td class="text-center">{{ number_format($product->min_stock) }}</td>
                    <td class="text-right">{{ number_format($product->unit_price, 2) }} TL</td>
                    <td class="text-right">{{ number_format($totalValue, 2) }} TL</td>
                    <td class="text-center">
                        @if($stockStatus == 'zero')
                            <span class="badge badge-danger">Stok Yok</span>
                        @elseif($stockStatus == 'low')
                            <span class="badge badge-warning">Düşük</span>
                        @else
                            <span class="badge badge-success">Normal</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right"><strong>TOPLAM DEĞER:</strong></td>
                <td class="text-right"><strong>{{ number_format($products->sum(function($p) { return $p->current_stock * $p->unit_price; }), 2) }} TL</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>{{ config('app.name') }} - Stok Takip Sistemi | Rapor Tarihi: {{ date('d.m.Y H:i') }}</p>
    </div>
</body>
</html> 