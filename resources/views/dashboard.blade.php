<x-app-layout>
    <x-slot name="header">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </x-slot>

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
                            <h5 class="card-title">Toplam Stok</h5>
                            <h2 class="mb-0">{{ \App\Models\Product::active()->sum('current_stock') }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-boxes fs-1"></i>
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
                            <h2 class="mb-0">{{ \App\Models\Product::active()->lowStock()->count() }}</h2>
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
                            <h5 class="card-title">Toplam Değer</h5>
                            <h2 class="mb-0">
                                @php
                                    $totalValue = \App\Models\Product::active()->get()->sum('total_value');
                                @endphp
                                {{ number_format($totalValue, 2) }} ₺
                            </h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-exchange fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Son Stok Hareketleri -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Son Stok Hareketleri</h5>
                    @if(Auth::user()->hasPermission('stock_entry') || Auth::user()->hasPermission('stock_exit') || Auth::user()->hasPermission('stock_return'))
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                    @endif
                </div>
                <div class="card-body">
                    @php
                        $recentMovements = \App\Models\StockMovement::with(['product', 'user'])
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp
                    
                    @if($recentMovements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Ürün</th>
                                        <th>Tip</th>
                                        <th>Miktar</th>
                                        <th>Kullanıcı</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMovements as $movement)
                                        <tr>
                                            <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                            <td>{{ $movement->product->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $movement->type_color }}">
                                                    <i class="{{ $movement->type_icon }}"></i> {{ ucfirst($movement->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $movement->quantity }}</td>
                                            <td>{{ $movement->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Henüz stok hareketi bulunmuyor.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Düşük Stoklu Ürünler -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Düşük Stok Uyarıları</h5>
                    @if(Auth::user()->hasPermission('product_management'))
                        <a href="{{ route('products.index') }}?filter=low_stock" class="btn btn-sm btn-outline-warning">Tümünü Gör</a>
                    @endif
                </div>
                <div class="card-body">
                    @php
                        $lowStockProducts = \App\Models\Product::active()
                            ->lowStock()
                            ->with('category')
                            ->take(10)
                            ->get();
                    @endphp
                    
                    @if($lowStockProducts->count() > 0)
                        @foreach($lowStockProducts as $product)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>{{ $product->name }}</strong><br>
                                    <small class="text-muted">{{ $product->category->name }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">{{ $product->current_stock }}</span><br>
                                    <small class="text-muted">Min: {{ $product->min_stock }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Tebrikler! Hiçbir ürününüz düşük stokta değil.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kategori Dağılımı -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Kategori Dağılımı</h5>
                </div>
                <div class="card-body">
                    @php
                        $categories = \App\Models\Category::active()
                            ->withCount('products')
                            ->having('products_count', '>', 0)
                            ->orderBy('products_count', 'desc')
                            ->get();
                    @endphp
                    
                    @if($categories->count() > 0)
                        @foreach($categories as $category)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $category->name }}</span>
                                <span class="badge bg-primary">{{ $category->products_count }} ürün</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Henüz kategoride ürün bulunmuyor.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->hasPermission('product_management'))
                            <a href="{{ route('products.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>Yeni Ürün Ekle
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_entry'))
                            <a href="{{ route('stock-movements.create') }}?type=giriş" class="btn btn-outline-success">
                                <i class="bi bi-arrow-up-circle me-2"></i>Stok Girişi
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_exit'))
                            <a href="{{ route('stock-movements.create') }}?type=çıkış" class="btn btn-outline-danger">
                                <i class="bi bi-arrow-down-circle me-2"></i>Stok Çıkışı
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_return'))
                            <a href="{{ route('stock-movements.create') }}?type=iade" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-repeat me-2"></i>Stok İadesi
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('reports_view'))
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-info">
                                <i class="bi bi-graph-up me-2"></i>Raporları Görüntüle
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
