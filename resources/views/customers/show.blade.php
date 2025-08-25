@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-building me-2"></i>
                    {{ $customer->company_name }}
                    @if(!$customer->is_active)
                        <span class="badge bg-danger ms-2">Pasif</span>
                    @endif
                </h1>
                <div class="btn-group">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Sol Kolon: Müşteri Bilgileri & İstatistikler -->
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
                                <div class="col-7">{{ $customer->company_name }}</div>

                                <div class="col-5"><strong>Durum:</strong></div>
                                <div class="col-7">
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                                        {{ $customer->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>

                                <div class="col-5"><strong>Kayıt:</strong></div>
                                <div class="col-7">{{ $customer->created_at->format('d.m.Y H:i') }}</div>

                                <div class="col-5"><strong>Güncelleme:</strong></div>
                                <div class="col-7">{{ $customer->updated_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok İstatistikleri -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-bar-chart me-2"></i>Stok İstatistikleri
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-danger mb-1">{{ $customer->total_stock_out }}</div>
                                        <small class="text-muted">Toplam Çıkış</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h4 text-success mb-1">{{ $customer->total_stock_return }}</div>
                                        <small class="text-muted">Toplam İade</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 bg-light rounded">
                                        <div class="h5 text-primary mb-1">
                                            {{ $customer->total_stock_out - $customer->total_stock_return }}
                                        </div>
                                        <small class="text-muted">Net Çıkış</small>
                                    </div>
                                </div>
                            </div>

                            @if($customer->last_transaction_date)
                                <div class="border-top pt-3 mt-3 text-center">
                                    <strong class="text-muted d-block">Son İşlem</strong>
                                    <span class="small">{{ $customer->last_transaction_date->format('d.m.Y H:i') }}</span><br>
                                    <span class="badge bg-secondary">{{ $customer->last_transaction_date->diffForHumans() }}</span>
                                </div>
                            @endif
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
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil me-2"></i>Müşteriyi Düzenle
                                </a>
                                
                                <!-- Bu butonlar stok hareket sistemi tamamlandıktan sonra aktif olacak -->
                                <button class="btn btn-outline-success btn-sm" disabled>
                                    <i class="bi bi-box-arrow-in-down me-2"></i>Stok Çıkışı Yap
                                </button>
                                <button class="btn btn-outline-warning btn-sm" disabled>
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Stok İadesi Yap
                                </button>
                                
                                @if($customer->stockMovements()->count() === 0)
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteCustomer()">
                                        <i class="bi bi-trash me-2"></i>Müşteriyi Sil
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Stok Hareketleri -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-arrow-left-right me-2"></i>Stok Hareketleri
                            </h6>
                            <span class="badge bg-primary">{{ $recentMovements->count() }} hareket</span>
                        </div>
                        <div class="card-body">
                            <!-- Filtreler -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="typeFilter">
                                        <option value="">Tüm Hareketler</option>
                                        <option value="çıkış">Sadece Çıkış</option>
                                        <option value="iade">Sadece İade</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control form-control-sm" id="dateFilter" 
                                           placeholder="Tarih Filtresi">
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

                            @if($recentMovements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="movementsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Ürün</th>
                                                <th>Tip</th>
                                                <th>Miktar</th>
                                                <th>Yapan</th>
                                                <th>Not</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentMovements as $movement)
                                                <tr data-type="{{ $movement->type }}" 
                                                    data-date="{{ $movement->created_at->format('Y-m-d') }}"
                                                    data-product="{{ strtolower($movement->product->name) }}">
                                                    <td>
                                                        <small>{{ $movement->created_at->format('d.m.Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('products.show', $movement->product) }}" 
                                                           class="text-decoration-none small" target="_blank">
                                                            {{ $movement->product->name }}
                                                            <i class="bi bi-box-arrow-up-right text-muted"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $movement->type === 'çıkış' ? 'danger' : 'success' }} badge-sm">
                                                            <i class="bi bi-{{ $movement->type === 'çıkış' ? 'box-arrow-up' : 'arrow-counterclockwise' }} me-1"></i>
                                                            {{ ucfirst($movement->type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold {{ $movement->type === 'çıkış' ? 'text-danger' : 'text-success' }}">
                                                            {{ $movement->type === 'çıkış' ? '-' : '+' }}{{ $movement->quantity }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $movement->user->name }}</small>
                                                    </td>
                                                    <td>
                                                        @if($movement->note)
                                                            <small class="text-muted" title="{{ $movement->note }}">
                                                                {{ Str::limit($movement->note, 30) }}
                                                            </small>
                                                        @else
                                                            <small class="text-muted">-</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Özet -->
                                <div class="border-top pt-3 mt-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Toplam Hareket</small>
                                            <div class="fw-bold" id="totalCount">{{ $recentMovements->count() }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Çıkış</small>
                                            <div class="fw-bold text-danger" id="exitCount">
                                                {{ $recentMovements->where('type', 'çıkış')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">İade</small>
                                            <div class="fw-bold text-success" id="returnCount">
                                                {{ $recentMovements->where('type', 'iade')->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-arrow-left-right display-6 text-muted"></i>
                                    <h6 class="mt-2">Henüz stok hareketi yok</h6>
                                    <p class="text-muted small">Bu müşteri için henüz hiç stok çıkışı veya iadesi yapılmamış.</p>
                                    <button class="btn btn-outline-primary btn-sm" disabled>
                                        <i class="bi bi-plus-circle me-1"></i>İlk Hareketi Ekle
                                    </button>
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
@if($customer->stockMovements()->count() === 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Müşteriyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <strong>{{ $customer->company_name }}</strong> isimli müşteriyi silmek istediğinizden emin misiniz?
                <br><br>
                Bu işlem geri alınamaz ve müşteriye ait tüm veriler silinir.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
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
function deleteCustomer() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Filtreleme fonksiyonları
document.addEventListener('DOMContentLoaded', function() {
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const productFilter = document.getElementById('productFilter');
    const table = document.getElementById('movementsTable');
    
    if (!table) return; // Tablo yoksa fonksiyonu durdur

    function applyFilters() {
        const rows = table.querySelectorAll('tbody tr');
        const typeValue = typeFilter.value.toLowerCase();
        const dateValue = dateFilter.value;
        const productValue = productFilter.value.toLowerCase();
        
        let visibleCount = 0;
        let exitCount = 0;
        let returnCount = 0;
        
        rows.forEach(row => {
            const rowType = row.dataset.type;
            const rowDate = row.dataset.date;
            const rowProduct = row.dataset.product;
            
            let showRow = true;
            
            // Tip filtresi
            if (typeValue && rowType !== typeValue) {
                showRow = false;
            }
            
            // Tarih filtresi
            if (dateValue && rowDate !== dateValue) {
                showRow = false;
            }
            
            // Ürün filtresi
            if (productValue && !rowProduct.includes(productValue)) {
                showRow = false;
            }
            
            if (showRow) {
                row.style.display = '';
                visibleCount++;
                if (rowType === 'çıkış') exitCount++;
                if (rowType === 'iade') returnCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Sayaçları güncelle
        document.getElementById('totalCount').textContent = visibleCount;
        document.getElementById('exitCount').textContent = exitCount;
        document.getElementById('returnCount').textContent = returnCount;
    }
    
    // Event listener'ları ekle
    typeFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    productFilter.addEventListener('input', applyFilters);
});

function clearFilters() {
    document.getElementById('typeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('productFilter').value = '';
    
    // Filtreleri tekrar uygula (tümünü göster)
    const event = new Event('change');
    document.getElementById('typeFilter').dispatchEvent(event);
}
</script>
@endpush 