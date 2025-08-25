@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i>Stok Hareketleri
                </h1>
                <div class="btn-group">
                    @if(Auth::user()->hasPermission('stock_entry'))
                        <a href="{{ route('stock-movements.create', ['type' => 'giriş']) }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Stok Girişi
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('stock_exit'))
                        <a href="{{ route('stock-movements.create', ['type' => 'çıkış']) }}" class="btn btn-warning">
                            <i class="bi bi-dash-circle me-1"></i>Stok Çıkışı
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('stock_return'))
                        <a href="{{ route('stock-movements.create', ['type' => 'iade']) }}" class="btn btn-info">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Stok İadesi
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filtreleme -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('stock-movements.index') }}" class="row g-3">
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Hareket Tipi</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tümü</option>
                                <option value="giriş" {{ request('type') === 'giriş' ? 'selected' : '' }}>
                                    Giriş
                                </option>
                                <option value="çıkış" {{ request('type') === 'çıkış' ? 'selected' : '' }}>
                                    Çıkış
                                </option>
                                <option value="iade" {{ request('type') === 'iade' ? 'selected' : '' }}>
                                    İade
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="product_id" class="form-label">Ürün</label>
                            <select class="form-select" id="product_id" name="product_id">
                                <option value="">Tüm Ürünler</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="customer_id" class="form-label">Müşteri</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                <option value="">Tüm Müşteriler</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Kullanıcı</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Tüm Kullanıcılar</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Ürün, barkod, referans, not...">
                        </div>
                        <div class="col-md-2">
                            <label for="sort_by" class="form-label">Sıralama</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>
                                    Tarih
                                </option>
                                <option value="type" {{ request('sort_by') === 'type' ? 'selected' : '' }}>
                                    Tip
                                </option>
                                <option value="quantity" {{ request('sort_by') === 'quantity' ? 'selected' : '' }}>
                                    Miktar
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="sort_dir" class="form-label">Yön</label>
                            <select class="form-select" id="sort_dir" name="sort_dir">
                                <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>↓</option>
                                <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>↑</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Filtrele
                            </button>
                            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Özet Kartlar -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $movements->where('type', 'giriş')->count() }}</h4>
                                    <p class="mb-0">Giriş</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-plus-circle fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $movements->where('type', 'çıkış')->count() }}</h4>
                                    <p class="mb-0">Çıkış</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-dash-circle fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $movements->where('type', 'iade')->count() }}</h4>
                                    <p class="mb-0">İade</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-arrow-counterclockwise fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $movements->total() }}</h4>
                                    <p class="mb-0">Toplam</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-arrow-left-right fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Hareketleri Listesi -->
            <div class="card">
                <div class="card-body">
                    @if($movements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Tip</th>
                                        <th>Ürün</th>
                                        <th>Miktar</th>
                                        <th>Önceki/Yeni Stok</th>
                                        <th>Müşteri</th>
                                        <th>Kullanıcı</th>
                                        <th>Referans</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                        <tr>
                                            <td>
                                                <div class="small">
                                                    <div>{{ $movement->created_at->format('d.m.Y') }}</div>
                                                    <div class="text-muted">{{ $movement->created_at->format('H:i') }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $movement->type_color }} text-white">
                                                    <i class="bi bi-{{ $movement->type_icon }} me-1"></i>
                                                    {{ ucfirst($movement->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($movement->product->hasImage())
                                                        <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" 
                                                             class="me-2 rounded" width="30" height="30" style="object-fit: cover;">
                                                    @else
                                                        <div class="me-2 bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 30px; height: 30px;">
                                                            <i class="bi bi-image text-muted small"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('products.show', $movement->product) }}" 
                                                           class="text-decoration-none fw-medium" target="_blank">
                                                            {{ $movement->product->name }}
                                                        </a>
                                                        @if($movement->product->barcode)
                                                            <br><small class="text-muted">{{ $movement->product->barcode }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-{{ $movement->type === 'çıkış' ? 'danger' : 'success' }}">
                                                    {{ $movement->type === 'çıkış' ? '-' : '+' }}{{ $movement->quantity }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <span class="text-muted">{{ $movement->previous_stock }}</span>
                                                    <i class="bi bi-arrow-right mx-1"></i>
                                                    <span class="fw-bold">{{ $movement->new_stock }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($movement->customer)
                                                    <a href="{{ route('customers.show', $movement->customer) }}" 
                                                       class="text-decoration-none" target="_blank">
                                                        {{ $movement->customer->company_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div>{{ $movement->user->name }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="small">{{ $movement->reference_number }}</code>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('stock-movements.show', $movement) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Görüntüle">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->hasPermission('admin'))
                                                        <a href="{{ route('stock-movements.edit', $movement) }}" 
                                                           class="btn btn-sm btn-outline-secondary" title="Düzenle">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Toplam {{ $movements->total() }} hareket ({{ $movements->firstItem() }}-{{ $movements->lastItem() }})
                            </div>
                            {{ $movements->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-arrow-left-right display-1 text-muted"></i>
                            <h4 class="mt-3">Stok hareketi bulunamadı</h4>
                            <p class="text-muted">Filtreleri değiştirin veya yeni stok hareketi oluşturun.</p>
                            @if(Auth::user()->hasPermission('stock_entry'))
                                <a href="{{ route('stock-movements.create', ['type' => 'giriş']) }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>İlk Stok Girişini Yap
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 