<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Hareketleri Raporu - {{ date('d.m.Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 9px;
        }
        .info-table .label {
            font-weight: bold;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 8px;
        }
        td {
            font-size: 7px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6px;
            color: white;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; }
        .footer {
            position: fixed;
            bottom: 15px;
            left: 15px;
            right: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Stok Hareketleri Raporu</p>
        <p>{{ date('d.m.Y H:i') }} - Toplam {{ $movements->count() }} Hareket</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Rapor Tarihi:</td>
            <td>{{ date('d.m.Y H:i') }}</td>
            <td class="label">Toplam Hareket:</td>
            <td>{{ number_format($movements->count()) }}</td>
        </tr>
        <tr>
            <td class="label">Stok Girişi:</td>
            <td>{{ $movements->where('type', 'giriş')->count() }}</td>
            <td class="label">Stok Çıkışı:</td>
            <td>{{ $movements->where('type', 'çıkış')->count() }}</td>
        </tr>
        <tr>
            <td class="label">Stok İadesi:</td>
            <td>{{ $movements->where('type', 'iade')->count() }}</td>
            <td class="label">-</td>
            <td>-</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tarih</th>
                <th style="width: 10%;">Ref. No</th>
                <th style="width: 8%;">İşlem</th>
                <th style="width: 25%;">Ürün</th>
                <th style="width: 8%;">Miktar</th>
                <th style="width: 8%;">Ön. Stok</th>
                <th style="width: 8%;">Yeni Stok</th>
                <th style="width: 12%;">Kullanıcı</th>
                <th style="width: 9%;">Müşteri</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td class="text-center">
                        {{ $movement->created_at->format('d.m.Y') }}<br>
                        <small>{{ $movement->created_at->format('H:i') }}</small>
                    </td>
                    <td class="text-center">
                        @if($movement->reference_number)
                            {{ $movement->reference_number }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($movement->type == 'giriş')
                            <span class="badge badge-success">Giriş</span>
                        @elseif($movement->type == 'çıkış')
                            <span class="badge badge-warning">Çıkış</span>
                        @else
                            <span class="badge badge-info">İade</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $movement->product->name ?? 'Ürün Silinmiş' }}</strong>
                        @if($movement->product && $movement->product->barcode)
                            <br><small>{{ $movement->product->barcode }}</small>
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ number_format($movement->quantity) }}</strong></td>
                    <td class="text-center">{{ number_format($movement->previous_stock) }}</td>
                    <td class="text-center">{{ number_format($movement->new_stock) }}</td>
                    <td>{{ $movement->user->name ?? 'Bilinmiyor' }}</td>
                    <td>{{ $movement->customer->company_name ?? '-' }}</td>
                </tr>
                @if($movement->note)
                    <tr>
                        <td colspan="9" style="padding: 2px 4px; font-style: italic; color: #666; border-top: none;">
                            <strong>Not:</strong> {{ $movement->note }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('app.name') }} - Stok Takip Sistemi | Rapor Tarihi: {{ date('d.m.Y H:i') }}</p>
    </div>
</body>
</html> 