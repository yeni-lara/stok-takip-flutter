@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-{{ $stockMovement->type_icon }} me-2"></i>
                    Stok {{ ucfirst($stockMovement->type) }} Detayı
                </h1>
                <div class="btn-group">
                    @if(Auth::user()->hasPermission('admin'))
                        <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>Düzenle
                        </a>
                    @endif
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Sol Panel - Hareket Bilgileri -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Hareket Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Referans No:</strong></td>
                                            <td><code>{{ $stockMovement->reference_number }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Hareket Tipi:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $stockMovement->type_color }} text-white">
                                                    <i class="bi bi-{{ $stockMovement->type_icon }} me-1"></i>
                                                    {{ ucfirst($stockMovement->type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Miktar:</strong></td>
                                            <td>
                                                <span class="fw-bold text-{{ $stockMovement->type === 'çıkış' ? 'danger' : 'success' }} fs-5">
                                                    {{ $stockMovement->type === 'çıkış' ? '-' : '+' }}{{ $stockMovement->quantity }} Adet
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tarih:</strong></td>
                                            <td>{{ $stockMovement->created_at->format('d.m.Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Önceki Stok:</strong></td>
                                            <td><span class="badge bg-secondary">{{ $stockMovement->previous_stock }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Yeni Stok:</strong></td>
                                            <td><span class="badge bg-primary">{{ $stockMovement->new_stock }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Değişim:</strong></td>
                                            <td>
                                                <span class="text-muted">{{ $stockMovement->previous_stock }}</span>
                                                <i class="bi bi-arrow-right mx-2"></i>
                                                <span class="fw-bold">{{ $stockMovement->new_stock }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>İşlem Yapan:</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle me-2"></i>
                                                    {{ $stockMovement->user->name }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($stockMovement->note)
                                <div class="border-top pt-3 mt-3">
                                    <h6><i class="bi bi-chat-text me-2"></i>Not</h6>
                                    <div class="alert alert-light">
                                        {{ $stockMovement->note }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ürün Bilgileri -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-box me-2"></i>Ürün Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    @if($stockMovement->product->hasImage())
                                        <img src="{{ $stockMovement->product->image_url }}" 
                                             alt="{{ $stockMovement->product->name }}" 
                                             class="img-fluid rounded border">
                                    @else
                                        <div class="bg-light rounded border d-flex align-items-center justify-content-center" 
                                             style="height: 150px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <h4>
                                        <a href="{{ route('products.show', $stockMovement->product) }}" 
                                           class="text-decoration-none" target="_blank">
                                            {{ $stockMovement->product->name }}
                                            <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                        </a>
                                    </h4>
                                    
                                    @if($stockMovement->product->description)
                                        <p class="text-muted">{{ $stockMovement->product->description }}</p>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Barkod:</strong></td>
                                                    <td>
                                                        @if($stockMovement->product->barcode)
                                                            <code>{{ $stockMovement->product->barcode }}</code>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Kategori:</strong></td>
                                                    <td>
                                                        @if($stockMovement->product->category)
                                                            <span class="badge bg-light text-dark">
                                                                {{ $stockMovement->product->category->name }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Mevcut Stok:</strong></td>
                                                    <td>
                                                        <span class="fw-bold {{ $stockMovement->product->current_stock <= 0 ? 'text-danger' : ($stockMovement->product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                                            {{ $stockMovement->product->current_stock }}
                                                        </span>
                                                        @if($stockMovement->product->isLowStock())
                                                            <i class="bi bi-exclamation-triangle text-warning ms-1" title="Az stok"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Birim Fiyat:</strong></td>
                                                    <td>{{ number_format($stockMovement->product->unit_price, 2) }} TL</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>KDV Dahil:</strong></td>
                                                    <td>{{ number_format($stockMovement->product->price_with_tax, 2) }} TL</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Toplam Değer:</strong></td>
                                                    <td>
                                                        <strong>{{ number_format($stockMovement->product->unit_price * $stockMovement->quantity, 2) }} TL</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Müşteri Bilgileri (Eğer varsa) -->
                    @if($stockMovement->customer)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-person-badge me-2"></i>Müşteri Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>
                                <a href="{{ route('customers.show', $stockMovement->customer) }}" 
                                   class="text-decoration-none" target="_blank">
                                    {{ $stockMovement->customer->company_name }}
                                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                </a>
                            </h6>
                            <p class="text-muted mb-0">
                                {{ $stockMovement->type === 'çıkış' ? 'Ürün bu müşteriye çıkış yapıldı' : 'Ürün bu müşteriden iade alındı' }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sağ Panel - Özet ve İşlemler -->
                <div class="col-lg-4">
                    <!-- Özet Kart -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-clipboard-data me-2"></i>Özet
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h5 text-{{ $stockMovement->type === 'çıkış' ? 'danger' : 'success' }} mb-1">
                                            {{ $stockMovement->type === 'çıkış' ? '-' : '+' }}{{ $stockMovement->quantity }}
                                        </div>
                                        <small class="text-muted">Miktar</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded">
                                        <div class="h5 text-primary mb-1">{{ $stockMovement->new_stock }}</div>
                                        <small class="text-muted">Yeni Stok</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 border rounded">
                                        <div class="h5 text-warning mb-1">
                                            {{ number_format($stockMovement->product->unit_price * $stockMovement->quantity, 2) }} TL
                                        </div>
                                        <small class="text-muted">Hareket Değeri</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Hızlı İşlemler
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(Auth::user()->hasPermission('admin'))
                                    <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil me-2"></i>Düzenle
                                    </a>
                                @endif
                                
                                <a href="{{ route('products.show', $stockMovement->product) }}" class="btn btn-outline-info btn-sm" target="_blank">
                                    <i class="bi bi-box me-2"></i>Ürünü Görüntüle
                                </a>
                                
                                @if($stockMovement->customer)
                                    <a href="{{ route('customers.show', $stockMovement->customer) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="bi bi-person me-2"></i>Müşteriyi Görüntüle
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('stock_entry'))
                                    <a href="{{ route('stock-movements.create', ['type' => 'giriş']) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-plus-circle me-2"></i>Yeni Giriş
                                    </a>
                                @endif

                                @if(Auth::user()->hasPermission('stock_exit'))
                                    <a href="{{ route('stock-movements.create', ['type' => 'çıkış']) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-dash-circle me-2"></i>Yeni Çıkış
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Zaman Bilgileri -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-clock me-2"></i>Zaman Bilgileri
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Oluşturma:</span>
                                    <span>{{ $stockMovement->created_at->format('d.m.Y H:i:s') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Güncelleme:</span>
                                    <span>{{ $stockMovement->updated_at->format('d.m.Y H:i:s') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Geçen Süre:</span>
                                    <span>{{ $stockMovement->created_at->diffForHumans() }}</span>
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