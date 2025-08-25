@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-{{ $type === 'giriş' ? 'plus-circle' : ($type === 'çıkış' ? 'dash-circle' : 'arrow-counterclockwise') }} me-2"></i>
                    Stok {{ ucfirst($type) }}
                </h1>
                <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Geri Dön
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-{{ $type === 'giriş' ? 'box-arrow-in-down' : ($type === 'çıkış' ? 'box-arrow-up' : 'arrow-clockwise') }} me-2"></i>
                                {{ $type === 'giriş' ? 'Stok Giriş' : ($type === 'çıkış' ? 'Stok Çıkış' : 'Stok İade') }} Formu
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('stock-movements.store') }}" method="POST" id="stockMovementForm">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">

                                <!-- Ürün Seçimi -->
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">
                                        Ürün <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" 
                                            id="product_id" name="product_id" onchange="updateProductInfo()">
                                        <option value="">Ürün seçin...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-name="{{ $product->name }}"
                                                    data-barcode="{{ $product->barcode }}"
                                                    data-category="{{ $product->category ? $product->category->name : '' }}"
                                                    data-current-stock="{{ $product->current_stock }}"
                                                    data-min-stock="{{ $product->min_stock }}"
                                                    data-unit-price="{{ $product->unit_price }}"
                                                    data-image="{{ $product->image_url }}"
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} 
                                                @if($product->barcode)
                                                    ({{ $product->barcode }})
                                                @endif
                                                - Stok: {{ $product->current_stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ürün Bilgi Kartı -->
                                <div id="product_info" class="card mb-3" style="display: none;">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <img id="product_image" src="" alt="Ürün Resmi" 
                                                     class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 id="product_name" class="mb-1"></h6>
                                                        <small class="text-muted" id="product_category"></small>
                                                        <div class="small mt-1">
                                                            <strong>Barkod:</strong> <span id="product_barcode"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="small">
                                                            <strong>Mevcut Stok:</strong> 
                                                            <span id="current_stock" class="fw-bold"></span>
                                                        </div>
                                                        <div class="small">
                                                            <strong>Minimum Stok:</strong> 
                                                            <span id="min_stock"></span>
                                                        </div>
                                                        <div class="small">
                                                            <strong>Birim Fiyat:</strong> 
                                                            <span id="unit_price"></span> TL
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Miktar -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">
                                                Miktar <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}"
                                                       min="1" placeholder="Miktar girin" onchange="calculateTotal()">
                                                <span class="input-group-text">Adet</span>
                                            </div>
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($type === 'çıkış')
                                                <div class="form-text text-warning">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Çıkış yapılacak miktar mevcut stoktan fazla olamaz.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="total_value" class="form-label">Toplam Değer</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="total_value" readonly>
                                                <span class="input-group-text">TL</span>
                                            </div>
                                            <div class="form-text">Birim fiyat × Miktar</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Müşteri (Çıkış ve İade için) -->
                                @if(in_array($type, ['çıkış', 'iade']))
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">
                                        Müşteri <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" name="customer_id">
                                        <option value="">Müşteri seçin...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <!-- Referans Numarası -->
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Referans Numarası</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                           id="reference_number" name="reference_number" value="{{ old('reference_number') }}"
                                           placeholder="Otomatik oluşturulacak (isteğe bağlı)">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Boş bırakılırsa otomatik oluşturulur 
                                        ({{ $type === 'giriş' ? 'SG' : ($type === 'çıkış' ? 'SC' : 'SI') }}{{ date('Ymd') }}XXXX)
                                    </div>
                                </div>

                                <!-- Not -->
                                <div class="mb-3">
                                    <label for="note" class="form-label">Not</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" 
                                              id="note" name="note" rows="3" 
                                              placeholder="İsteğe bağlı açıklama...">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Butonlar -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-{{ $type === 'giriş' ? 'success' : ($type === 'çıkış' ? 'warning' : 'info') }}">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $type === 'giriş' ? 'Stok Girişi Yap' : ($type === 'çıkış' ? 'Stok Çıkışı Yap' : 'Stok İadesi Yap') }}
                                    </button>
                                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>İptal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sağ Panel -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Bilgilendirme
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($type === 'giriş')
                                <div class="alert alert-success">
                                    <h6><i class="bi bi-plus-circle me-2"></i>Stok Girişi</h6>
                                    <ul class="mb-0 small">
                                        <li>Ürün stoku artırılacak</li>
                                        <li>Müşteri seçimi opsiyonel</li>
                                        <li>Tedarikçiden gelen ürünler için</li>
                                        <li>İade edilen ürünler için</li>
                                    </ul>
                                </div>
                            @elseif($type === 'çıkış')
                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-dash-circle me-2"></i>Stok Çıkışı</h6>
                                    <ul class="mb-0 small">
                                        <li>Ürün stoku azaltılacak</li>
                                        <li>Müşteri seçimi zorunlu</li>
                                        <li>Satış işlemleri için</li>
                                        <li>Stok kontrolü yapılır</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-arrow-counterclockwise me-2"></i>Stok İadesi</h6>
                                    <ul class="mb-0 small">
                                        <li>Ürün stoku artırılacak</li>
                                        <li>Müşteri seçimi zorunlu</li>
                                        <li>Müşteriden iade alınan ürünler</li>
                                        <li>Pozitif stok hareketi</li>
                                    </ul>
                                </div>
                            @endif

                            <div class="border-top pt-3">
                                <h6>Son Hareketler</h6>
                                <div class="small text-muted">
                                    @php
                                        $recentMovements = \App\Models\StockMovement::with('product')
                                            ->where('type', $type)
                                            ->latest()
                                            ->take(3)
                                            ->get();
                                    @endphp
                                    
                                    @if($recentMovements->count() > 0)
                                        @foreach($recentMovements as $movement)
                                            <div class="d-flex justify-content-between py-1">
                                                <span>{{ Str::limit($movement->product->name, 20) }}</span>
                                                <span>{{ $movement->quantity }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted">Henüz {{ $type }} hareketi yok</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateProductInfo() {
    const select = document.getElementById('product_id');
    const option = select.options[select.selectedIndex];
    const infoCard = document.getElementById('product_info');
    
    if (option.value) {
        // Ürün bilgilerini göster
        document.getElementById('product_image').src = option.dataset.image;
        document.getElementById('product_name').textContent = option.dataset.name;
        document.getElementById('product_category').textContent = option.dataset.category;
        document.getElementById('product_barcode').textContent = option.dataset.barcode || '-';
        document.getElementById('current_stock').textContent = option.dataset.currentStock;
        document.getElementById('min_stock').textContent = option.dataset.minStock;
        document.getElementById('unit_price').textContent = parseFloat(option.dataset.unitPrice).toFixed(2);
        
        // Stok uyarısı
        const currentStock = parseInt(option.dataset.currentStock);
        const minStock = parseInt(option.dataset.minStock);
        const stockElement = document.getElementById('current_stock');
        
        if (currentStock <= 0) {
            stockElement.className = 'fw-bold text-danger';
        } else if (currentStock <= minStock) {
            stockElement.className = 'fw-bold text-warning';
        } else {
            stockElement.className = 'fw-bold text-success';
        }
        
        infoCard.style.display = 'block';
        calculateTotal();
    } else {
        infoCard.style.display = 'none';
    }
}

function calculateTotal() {
    const select = document.getElementById('product_id');
    const quantity = document.getElementById('quantity').value;
    const totalElement = document.getElementById('total_value');
    
    if (select.value && quantity) {
        const option = select.options[select.selectedIndex];
        const unitPrice = parseFloat(option.dataset.unitPrice);
        const total = unitPrice * parseInt(quantity);
        totalElement.value = total.toFixed(2);
    } else {
        totalElement.value = '';
    }
}

// Form validasyonu
document.getElementById('stockMovementForm').addEventListener('submit', function(e) {
    const productSelect = document.getElementById('product_id');
    const quantity = parseInt(document.getElementById('quantity').value);
    
    if (productSelect.value) {
        const option = productSelect.options[productSelect.selectedIndex];
        const currentStock = parseInt(option.dataset.currentStock);
        const type = '{{ $type }}';
        
        if (type === 'çıkış' && quantity > currentStock) {
            e.preventDefault();
            alert('Çıkış miktarı mevcut stoktan (' + currentStock + ') fazla olamaz!');
            return false;
        }
    }
});

// Sayfa yüklendiğinde seçili ürün varsa bilgileri göster
document.addEventListener('DOMContentLoaded', function() {
    updateProductInfo();
});
</script>
@endpush 