@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Stok Durum Raporu
                </h2>
                <div class="btn-group">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Raporlara Dön
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports.export.stock-status.excel', request()->all()) }}">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel İndir
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.export.stock-status.pdf', request()->all()) }}">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF İndir
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filtreler -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock-status') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Ürün Ara</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Ürün adı veya barkod...">
                        </div>
                        <div class="col-md-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Tüm Kategoriler</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="supplier_id" class="form-label">Tedarikçi</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">Tüm Tedarikçiler</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_status" class="form-label">Stok Durumu</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">Tüm Durumlar</option>
                                <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Normal Stok</option>
                                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Düşük Stok</option>
                                <option value="zero" {{ request('stock_status') === 'zero' ? 'selected' : '' }}>Stok Yok</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrele
                            </button>
                            <a href="{{ route('reports.stock-status') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sonuç Özeti -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Toplam Ürün</h5>
                            <h3 class="text-primary">{{ $products->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h5 class="card-title">Normal Stok</h5>
                            <h3 class="text-success">{{ $products->filter(function($p) { return $p->current_stock > $p->min_stock; })->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Düşük Stok</h5>
                            <h3 class="text-warning">{{ $products->filter(function($p) { return $p->current_stock <= $p->min_stock && $p->current_stock > 0; })->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <h5 class="card-title">Stok Yok</h5>
                            <h3 class="text-danger">{{ $products->filter(function($p) { return $p->current_stock == 0; })->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Tablosu -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Resim</th>
                                    <th>Ürün Adı</th>
                                    <th>Barkod</th>
                                    <th>Kategori</th>
                                    <th>Tedarikçi</th>
                                    <th>Mevcut Stok</th>
                                    <th>Min. Stok</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam Değer</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    @php
                                        $totalValue = $product->current_stock * $product->unit_price;
                                        $stockStatus = $product->current_stock == 0 ? 'zero' : 
                                                      ($product->current_stock <= $product->min_stock ? 'low' : 'normal');
                                    @endphp
                                    <tr class="{{ $stockStatus == 'zero' ? 'table-danger' : ($stockStatus == 'low' ? 'table-warning' : '') }}">
                                        <td>
                                            @if($product->hasImage())
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; border-radius: 0.375rem;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->description)
                                                <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $product->barcode ?: '-' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $product->category->name ?? '-' }}</span>
                                        </td>
                                        <td>{{ $product->supplier->name ?? '-' }}</td>
                                        <td>
                                            <span class="fw-bold {{ $stockStatus == 'zero' ? 'text-danger' : ($stockStatus == 'low' ? 'text-warning' : 'text-success') }}">
                                                {{ number_format($product->current_stock) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($product->min_stock) }}</td>
                                        <td>{{ number_format($product->unit_price, 2) }} TL</td>
                                        <td>
                                            <strong>{{ number_format($totalValue, 2) }} TL</strong>
                                            @if($product->tax_rate > 0)
                                                <br><small class="text-muted">
                                                    KDV Dahil: {{ number_format($totalValue * (1 + $product->tax_rate/100), 2) }} TL
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($stockStatus == 'zero')
                                                <span class="badge bg-danger">Stok Yok</span>
                                            @elseif($stockStatus == 'low')
                                                <span class="badge bg-warning">Düşük Stok</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" title="Görüntüle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasPermission('stock_entry'))
                                                    <a href="{{ route('stock.entry', ['product_id' => $product->id]) }}" class="btn btn-outline-success" title="Stok Girişi">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('stock_exit') && $product->current_stock > 0)
                                                    <a href="{{ route('stock.exit', ['product_id' => $product->id]) }}" class="btn btn-outline-warning" title="Stok Çıkışı">
                                                        <i class="bi bi-dash-circle"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Belirtilen kriterlere uygun ürün bulunamadı</p>
                                            <a href="{{ route('reports.stock-status') }}" class="btn btn-primary">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Filtreleri Temizle
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($products->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="8" class="text-end">Toplam Değer:</th>
                                        <th>
                                            {{ number_format($products->sum(function($p) { return $p->current_stock * $p->unit_price; }), 2) }} TL
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 