@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-box me-2"></i>Ürünler
                </h1>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Yeni Ürün
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <!-- Filtreleme ve Arama -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Arama</label>
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Ürün adı, barkod veya açıklama...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-select">
                                    <option value="">Tümü</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tedarikçi</label>
                                <select name="supplier" class="form-select">
                                    <option value="">Tümü</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                                {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Durum</label>
                                <select name="filter" class="form-select">
                                    <option value="">Tümü</option>
                                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                                    <option value="low_stock" {{ request('filter') == 'low_stock' ? 'selected' : '' }}>Düşük Stok</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-2"></i>Ara
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ürün Listesi -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Ürün Listesi 
                        <span class="badge bg-secondary">{{ $products->total() }} ürün</span>
                    </h5>
                    <div>
                        <div class="btn-group me-2">
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-sort-alpha-{{ request('sort') === 'name' && request('direction') === 'asc' ? 'down' : 'up' }}"></i>
                                Ad
                            </a>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'current_stock', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-sort-numeric-{{ request('sort') === 'current_stock' && request('direction') === 'asc' ? 'down' : 'up' }}"></i>
                                Stok
                            </a>
                            <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'unit_price', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-sort-numeric-{{ request('sort') === 'unit_price' && request('direction') === 'asc' ? 'down' : 'up' }}"></i>
                                Fiyat
                            </a>
                        </div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Yeni Ürün
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resim</th>
                                        <th>Ürün Bilgileri</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Fiyat</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <img src="{{ $product->image_url }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    @if($product->barcode)
                                                        <br><small class="text-muted">
                                                            <i class="bi bi-upc"></i> {{ $product->barcode }}
                                                        </small>
                                                    @endif
                                                    @if($product->description)
                                                        <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $product->category->name }}</span>
                                                @if($product->supplier)
                                                    <br><small class="text-muted">{{ $product->supplier->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->isLowStock() ? 'warning' : 'success' }} fs-6">
                                                    {{ $product->current_stock }}
                                                </span>
                                                @if($product->isLowStock())
                                                    <br><small class="text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> Düşük stok
                                                    </small>
                                                @endif
                                                <br><small class="text-muted">Min: {{ $product->min_stock }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($product->price_with_tax, 2) }} ₺</strong>
                                                <br><small class="text-muted">
                                                    + %{{ $product->tax_rate }} KDV
                                                </small>
                                            </td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Pasif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Görüntüle">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Düzenle">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($product->stockMovements->count() == 0)
                                                        <form action="{{ route('products.destroy', $product) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                disabled 
                                                                title="Bu ürüne ait stok hareketleri olduğu için silinemez">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    {{ $products->firstItem() }}-{{ $products->lastItem() }} arası, 
                                    toplam {{ $products->total() }} ürün
                                </div>
                                <div>
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box fs-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">
                                @if(request()->hasAny(['search', 'category', 'supplier', 'filter']))
                                    Arama kriterlerinize uygun ürün bulunamadı
                                @else
                                    Henüz ürün bulunmuyor
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'category', 'supplier', 'filter']))
                                    Farklı kriterlerle arama yapabilir veya yeni ürün ekleyebilirsiniz.
                                @else
                                    İlk ürününüzü oluşturmak için yukarıdaki "Yeni Ürün" butonuna tıklayın.
                                @endif
                            </p>
                            <div class="mt-3">
                                @if(request()->hasAny(['search', 'category', 'supplier', 'filter']))
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Filtreleri Temizle
                                    </a>
                                @endif
                                <a href="{{ route('products.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>İlk Ürünü Oluştur
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Barkod okuyucu (gelecekte mobil entegrasyon için)
        document.addEventListener('keydown', function(e) {
            // Ctrl+B ile barkod arama modalı açılabilir
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                // Barkod arama modalı burada açılacak
                console.log('Barkod arama özelliği gelecekte eklenecek');
            }
        });
    </script>
@endpush 