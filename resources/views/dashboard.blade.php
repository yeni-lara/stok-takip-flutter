@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if(Auth::user()->role_id == 3)
        {{-- Teslimat Elemanı Dashboard --}}
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-truck me-2"></i>Teslimat Operasyonları
                    </h1>
                    <p class="text-muted">Barkod okutarak stok çıkışı veya iadesi yapabilirsiniz</p>
                </div>

                <div class="row g-4">
                    <!-- Stok Çıkışı -->
                    <div class="col-md-6">
                        <div class="card h-100 border-warning shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-arrow-down-circle text-warning" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title text-warning mb-3">Stok Çıkışı</h4>
                                <p class="card-text mb-4">
                                    Müşteriye teslimat yapmak için stok çıkışı yapın. 
                                    Barkod okutarak ürünü seçip, müşteriyi belirleyerek çıkış işlemini tamamlayın.
                                </p>
                                <a href="{{ route('stock.exit') }}" class="btn btn-warning btn-lg">
                                    <i class="bi bi-qr-code-scan me-2"></i>Barkod Okut & Çıkış Yap
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Stok İadesi -->
                    <div class="col-md-6">
                        <div class="card h-100 border-info shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="bi bi-arrow-repeat text-info" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="card-title text-info mb-3">Stok İadesi</h4>
                                <p class="card-text mb-4">
                                    Müşteriden gelen ürünleri stoğa iade edin. 
                                    Barkod okutarak ürünü seçip, müşteriyi belirleyerek iade işlemini tamamlayın.
                                </p>
                                <a href="{{ route('stock.return') }}" class="btn btn-info btn-lg">
                                    <i class="bi bi-qr-code-scan me-2"></i>Barkod Okut & İade Yap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kullanım Talimatları -->
                <div class="card mt-4 border-light">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Kullanım Talimatları
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Stok Çıkışı İçin:
                                </h6>
                                <ul class="list-unstyled ps-3">
                                    <li><i class="bi bi-check2 text-success me-2"></i>Çıkış butonuna tıklayın</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Barkodu kamera ile okutun</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Müşteriyi seçin</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Miktarı girin ve onaylayın</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info">
                                    <i class="bi bi-arrow-repeat me-1"></i>Stok İadesi İçin:
                                </h6>
                                <ul class="list-unstyled ps-3">
                                    <li><i class="bi bi-check2 text-success me-2"></i>İade butonuna tıklayın</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Barkodu kamera ile okutun</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Müşteriyi seçin</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Miktarı girin ve onaylayın</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Admin ve Yardımcı Dashboard --}}
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </h1>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- İstatistik Kartları -->
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Toplam Ürün</h5>
                                <h2 class="mb-0">{{ \App\Models\Product::active()->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-box fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Aktif Kategori</h5>
                                <h2 class="mb-0">{{ \App\Models\Category::active()->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-tags fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Düşük Stok</h5>
                                <h2 class="mb-0">{{ \App\Models\Product::whereRaw('current_stock <= min_stock')->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">Bu Ay Hareket</h5>
                                <h2 class="mb-0">{{ \App\Models\StockMovement::whereMonth('created_at', now()->month)->count() }}</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-left-right fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Hızlı İşlemler</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @if(Auth::user()->hasPermission('product_management'))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('products.create') }}" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="bi bi-plus-circle d-block fs-1 mb-2"></i>
                                    Yeni Ürün
                                </a>
                            </div>
                            @endif
                            
                            @if(Auth::user()->hasPermission('stock_entry'))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('stock.entry') }}" class="btn btn-outline-success btn-lg w-100">
                                    <i class="bi bi-arrow-up-circle d-block fs-1 mb-2"></i>
                                    Stok Girişi
                                </a>
                            </div>
                            @endif
                            
                            @if(Auth::user()->hasPermission('stock_exit'))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('stock.exit') }}" class="btn btn-outline-warning btn-lg w-100">
                                    <i class="bi bi-arrow-down-circle d-block fs-1 mb-2"></i>
                                    Stok Çıkışı
                                </a>
                            </div>
                            @endif
                            
                            @if(Auth::user()->hasPermission('view_reports'))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-info btn-lg w-100">
                                    <i class="bi bi-graph-up d-block fs-1 mb-2"></i>
                                    Raporlar
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Stok Hareketleri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Son Stok Hareketleri</h5>
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-primary btn-sm">
                            Tümünü Gör <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>İşlem</th>
                                        <th>Ürün</th>
                                        <th>Miktar</th>
                                        <th>Kullanıcı</th>
                                        <th>Müşteri</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\StockMovement::with(['product', 'user', 'customer'])->latest()->take(10)->get() as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type_color }}">
                                                <i class="{{ $movement->type_icon }} me-1"></i>{{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->product->name ?? 'Ürün Silinmiş' }}</td>
                                        <td><strong>{{ number_format($movement->quantity) }}</strong></td>
                                        <td>{{ $movement->user->name ?? 'Bilinmiyor' }}</td>
                                        <td>{{ $movement->customer->company_name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Düşük Stok Uyarıları -->
        @php
            $lowStockProducts = \App\Models\Product::with('category')
                ->whereRaw('current_stock <= min_stock')
                ->where('is_active', true)
                ->orderBy('current_stock', 'asc')
                ->take(10)
                ->get();
        @endphp

        @if($lowStockProducts->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Düşük Stok Uyarıları</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Kategori</th>
                                        <th>Mevcut Stok</th>
                                        <th>Min. Stok</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                    <tr class="{{ $product->current_stock == 0 ? 'table-danger' : 'table-warning' }}">
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $product->current_stock == 0 ? 'danger' : 'warning' }}">{{ $product->current_stock }}</span></td>
                                        <td>{{ $product->min_stock }}</td>
                                        <td>
                                            @if(Auth::user()->hasPermission('stock_entry'))
                                            <a href="{{ route('stock.entry', ['product_id' => $product->id]) }}" class="btn btn-success btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Stok Ekle
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
