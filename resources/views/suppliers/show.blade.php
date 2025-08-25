@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-truck me-2"></i>
                    {{ $supplier->name }}
                    @if(!$supplier->is_active)
                        <span class="badge bg-danger ms-2">Pasif</span>
                    @endif
                </h1>
                <div class="btn-group">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Sol Kolon: Tedarikçi Bilgileri -->
                <div class="col-lg-4">
                    <!-- Genel Bilgiler -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Genel Bilgiler
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-5"><strong>Firma:</strong></div>
                                <div class="col-7">{{ $supplier->name }}</div>

                                @if($supplier->contact_person)
                                <div class="col-5"><strong>Yetkili:</strong></div>
                                <div class="col-7">{{ $supplier->contact_person }}</div>
                                @endif

                                <div class="col-5"><strong>Durum:</strong></div>
                                <div class="col-7">
                                    <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}">
                                        {{ $supplier->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>

                                <div class="col-5"><strong>Kayıt:</strong></div>
                                <div class="col-7">{{ $supplier->created_at->format('d.m.Y H:i') }}</div>

                                <div class="col-5"><strong>Güncelleme:</strong></div>
                                <div class="col-7">{{ $supplier->updated_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- İletişim Bilgileri -->
                    @if($supplier->phone || $supplier->email || $supplier->address)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-telephone me-2"></i>İletişim
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @if($supplier->phone)
                                <div class="col-3"><strong>Telefon:</strong></div>
                                <div class="col-9">
                                    <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}
                                    </a>
                                </div>
                                @endif

                                @if($supplier->email)
                                <div class="col-3"><strong>E-posta:</strong></div>
                                <div class="col-9">
                                    <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                        <i class="bi bi-envelope me-1"></i>{{ $supplier->email }}
                                    </a>
                                </div>
                                @endif

                                @if($supplier->address)
                                <div class="col-3"><strong>Adres:</strong></div>
                                <div class="col-9">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $supplier->address }}
                                </div>
                                @endif

                                @if($supplier->tax_number)
                                <div class="col-3"><strong>Vergi No:</strong></div>
                                <div class="col-9">{{ $supplier->tax_number }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notlar -->
                    @if($supplier->notes)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-chat-text me-2"></i>Notlar
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $supplier->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Ürün İstatistikleri -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Ürün İstatistikleri
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-primary mb-1">{{ $products->count() }}</div>
                                        <small class="text-muted">Toplam Ürün</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-success mb-1">{{ $products->where('is_active', true)->count() }}</div>
                                        <small class="text-muted">Aktif Ürün</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-info mb-1">{{ $products->sum('current_stock') }}</div>
                                        <small class="text-muted">Toplam Stok</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-warning mb-1">{{ number_format($products->sum('total_value'), 2) }} TL</div>
                                        <small class="text-muted">Toplam Değer</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>Hızlı İşlemler
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-2"></i>Tedarikçiyi Düzenle
                                </a>
                                
                                <a href="{{ route('products.create') }}?supplier={{ $supplier->id }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Yeni Ürün Ekle
                                </a>
                                
                                @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-telephone me-2"></i>Ara
                                </a>
                                @endif
                                
                                @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-envelope me-2"></i>E-posta Gönder
                                </a>
                                @endif
                                
                                @if($products->count() === 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSupplier()">
                                    <i class="bi bi-trash me-2"></i>Tedarikçiyi Sil
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Ürünler -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-box me-2"></i>Tedarikçiye Ait Ürünler
                            </h6>
                            <span class="badge bg-primary">{{ $products->count() }} ürün</span>
                        </div>
                        <div class="card-body">
                            <!-- Filtreler -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="statusFilter">
                                        <option value="">Tüm Ürünler</option>
                                        <option value="active">Sadece Aktif</option>
                                        <option value="inactive">Sadece Pasif</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="stockFilter">
                                        <option value="">Tüm Stoklar</option>
                                        <option value="instock">Stokta Var</option>
                                        <option value="lowstock">Az Stok</option>
                                        <option value="outofstock">Stok Yok</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" id="productFilter" 
                                           placeholder="Ürün ara...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="clearFilters()">
                                        <i class="bi bi-x-circle"></i> Temizle
                                    </button>
                                </div>
                            </div>

                            @if($products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="productsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Kategori</th>
                                                <th>Fiyat</th>
                                                <th>Stok</th>
                                                <th>Durum</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                                <tr data-status="{{ $product->is_active ? 'active' : 'inactive' }}" 
                                                    data-stock="{{ $product->current_stock == 0 ? 'outofstock' : ($product->isLowStock() ? 'lowstock' : 'instock') }}"
                                                    data-product="{{ strtolower($product->name) }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($product->hasImage())
                                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                                     class="me-2 rounded" width="40" height="40" style="object-fit: cover;">
                                                            @else
                                                                <div class="me-2 bg-light rounded d-flex align-items-center justify-content-center" 
                                                                     style="width: 40px; height: 40px;">
                                                                    <i class="bi bi-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <a href="{{ route('products.show', $product) }}" 
                                                                   class="text-decoration-none fw-medium" target="_blank">
                                                                    {{ $product->name }}
                                                                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                                                </a>
                                                                @if($product->barcode)
                                                                    <br><small class="text-muted">{{ $product->barcode }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($product->category)
                                                            <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                                        @else
                                                            <small class="text-muted">-</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="small">
                                                            <div>{{ number_format($product->unit_price, 2) }} TL</div>
                                                            <div class="text-muted">+KDV: {{ number_format($product->price_with_tax, 2) }} TL</div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="fw-bold {{ $product->current_stock == 0 ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                                                {{ $product->current_stock }}
                                                            </span>
                                                            @if($product->isLowStock())
                                                                <i class="bi bi-exclamation-triangle text-warning ms-1" title="Az stok"></i>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">Min: {{ $product->min_stock }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                                            {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('products.show', $product) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="Görüntüle" target="_blank">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="{{ route('products.edit', $product) }}" 
                                                               class="btn btn-sm btn-outline-secondary" title="Düzenle" target="_blank">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Özet -->
                                <div class="border-top pt-3 mt-3">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <small class="text-muted">Toplam Ürün</small>
                                            <div class="fw-bold" id="totalCount">{{ $products->count() }}</div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">Aktif</small>
                                            <div class="fw-bold text-success" id="activeCount">
                                                {{ $products->where('is_active', true)->count() }}
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">Toplam Stok</small>
                                            <div class="fw-bold text-info" id="stockCount">
                                                {{ $products->sum('current_stock') }}
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">Toplam Değer</small>
                                            <div class="fw-bold text-warning" id="valueCount">
                                                {{ number_format($products->sum('total_value'), 0) }} TL
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-box display-6 text-muted"></i>
                                    <h6 class="mt-2">Henüz ürün yok</h6>
                                    <p class="text-muted small">Bu tedarikçiye ait henüz hiç ürün eklenmemiş.</p>
                                    <a href="{{ route('products.create') }}?supplier={{ $supplier->id }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>İlk Ürünü Ekle
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Silme Modal -->
@if($products->count() === 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tedarikçiyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <strong>{{ $supplier->name }}</strong> isimli tedarikçiyi silmek istediğinizden emin misiniz?
                <br><br>
                Bu işlem geri alınamaz ve tedarikçiye ait tüm veriler silinir.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Evet, Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function deleteSupplier() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Filtreleme fonksiyonları
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const stockFilter = document.getElementById('stockFilter');
    const productFilter = document.getElementById('productFilter');
    const table = document.getElementById('productsTable');
    
    if (!table) return; // Tablo yoksa fonksiyonu durdur

    function applyFilters() {
        const rows = table.querySelectorAll('tbody tr');
        const statusValue = statusFilter.value.toLowerCase();
        const stockValue = stockFilter.value.toLowerCase();
        const productValue = productFilter.value.toLowerCase();
        
        let visibleCount = 0;
        let activeCount = 0;
        let totalStock = 0;
        let totalValue = 0;
        
        rows.forEach(row => {
            const rowStatus = row.dataset.status;
            const rowStock = row.dataset.stock;
            const rowProduct = row.dataset.product;
            
            let showRow = true;
            
            // Durum filtresi
            if (statusValue && rowStatus !== statusValue) {
                showRow = false;
            }
            
            // Stok filtresi
            if (stockValue && rowStock !== stockValue) {
                showRow = false;
            }
            
            // Ürün filtresi
            if (productValue && !rowProduct.includes(productValue)) {
                showRow = false;
            }
            
            if (showRow) {
                row.style.display = '';
                visibleCount++;
                if (rowStatus === 'active') activeCount++;
                
                // Stok ve değer hesaplama (basit şekilde)
                const stockCell = row.querySelector('td:nth-child(4) .fw-bold');
                const stock = parseInt(stockCell.textContent) || 0;
                totalStock += stock;
                
            } else {
                row.style.display = 'none';
            }
        });
        
        // Sayaçları güncelle
        document.getElementById('totalCount').textContent = visibleCount;
        document.getElementById('activeCount').textContent = activeCount;
        document.getElementById('stockCount').textContent = totalStock;
    }
    
    // Event listener'ları ekle
    statusFilter.addEventListener('change', applyFilters);
    stockFilter.addEventListener('change', applyFilters);
    productFilter.addEventListener('input', applyFilters);
});

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('stockFilter').value = '';
    document.getElementById('productFilter').value = '';
    
    // Filtreleri tekrar uygula (tümünü göster)
    const event = new Event('change');
    document.getElementById('statusFilter').dispatchEvent(event);
}
</script>
@endpush 