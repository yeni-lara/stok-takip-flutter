@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Stok Hareketi Düzenle
                </h1>
                <div class="btn-group">
                    <a href="{{ route('stock-movements.show', $stockMovement) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Görüntüle
                    </a>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Hareket Bilgileri (Sınırlı Düzenleme)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Dikkat:</strong> Stok hareketlerinde sadece not ve referans numarası düzenlenebilir. 
                                Miktar ve ürün bilgileri sistem bütünlüğü için değiştirilemez.
                            </div>

                            <form action="{{ route('stock-movements.update', $stockMovement) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Sabit Bilgiler (Düzenlenemez) -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Hareket Detayları</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Hareket Tipi:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="badge bg-{{ $stockMovement->type_color }} text-white">
                                                        <i class="bi bi-{{ $stockMovement->type_icon }} me-1"></i>
                                                        {{ ucfirst($stockMovement->type) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <strong>Miktar:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="fw-bold">{{ $stockMovement->quantity }} Adet</span>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <strong>Stok Değişimi:</strong>
                                                </div>
                                                <div class="col-6">
                                                    {{ $stockMovement->previous_stock }} → {{ $stockMovement->new_stock }}
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <strong>Tarih:</strong>
                                                </div>
                                                <div class="col-6">
                                                    {{ $stockMovement->created_at->format('d.m.Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Ürün & Müşteri</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="row">
                                                <div class="col-4">
                                                    <strong>Ürün:</strong>
                                                </div>
                                                <div class="col-8">
                                                    <a href="{{ route('products.show', $stockMovement->product) }}" 
                                                       class="text-decoration-none" target="_blank">
                                                        {{ $stockMovement->product->name }}
                                                        <i class="bi bi-box-arrow-up-right small"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @if($stockMovement->customer)
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <strong>Müşteri:</strong>
                                                </div>
                                                <div class="col-8">
                                                    <a href="{{ route('customers.show', $stockMovement->customer) }}" 
                                                       class="text-decoration-none" target="_blank">
                                                        {{ $stockMovement->customer->company_name }}
                                                        <i class="bi bi-box-arrow-up-right small"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row mt-2">
                                                <div class="col-4">
                                                    <strong>İşlem Yapan:</strong>
                                                </div>
                                                <div class="col-8">
                                                    {{ $stockMovement->user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Düzenlenebilir Alanlar -->
                                <h6 class="border-bottom pb-2 mb-3">Düzenlenebilir Alanlar</h6>
                                
                                <!-- Referans Numarası -->
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Referans Numarası</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                           id="reference_number" name="reference_number" 
                                           value="{{ old('reference_number', $stockMovement->reference_number) }}"
                                           placeholder="Referans numarası">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Hareket için özel referans numarası (isteğe bağlı)
                                    </div>
                                </div>

                                <!-- Not -->
                                <div class="mb-4">
                                    <label for="note" class="form-label">Not</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" 
                                              id="note" name="note" rows="4" 
                                              placeholder="Hareket ile ilgili notlar...">{{ old('note', $stockMovement->note) }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Stok hareketi ile ilgili açıklama veya notlar
                                    </div>
                                </div>

                                <!-- Butonlar -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Değişiklikleri Kaydet
                                    </button>
                                    <a href="{{ route('stock-movements.show', $stockMovement) }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i>İptal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sağ Panel -->
                <div class="col-lg-4">
                    <!-- Uyarı Kartı -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-shield-exclamation me-2"></i>Önemli Bilgiler
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Düzenleme Sınırları</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Düzenlenebilir:</strong> Not, Referans numarası</li>
                                    <li><strong>Değiştirilemez:</strong> Ürün, Miktar, Müşteri, Tarih</li>
                                    <li><strong>Neden:</strong> Sistem bütünlüğü ve stok doğruluğu</li>
                                    <li><strong>Alternatif:</strong> Yeni hareket oluşturun</li>
                                </ul>
                            </div>

                            <div class="border-top pt-3">
                                <h6>Hareket Özeti</h6>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between">
                                        <span>Tip:</span>
                                        <span class="badge bg-{{ $stockMovement->type_color }}">{{ $stockMovement->type }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Miktar:</span>
                                        <span>{{ $stockMovement->quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Değer:</span>
                                        <span>{{ number_format($stockMovement->product->unit_price * $stockMovement->quantity, 2) }} TL</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Tarih:</span>
                                        <span>{{ $stockMovement->created_at->format('d.m.Y') }}</span>
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
                                <a href="{{ route('products.show', $stockMovement->product) }}" class="btn btn-outline-info btn-sm" target="_blank">
                                    <i class="bi bi-box me-2"></i>Ürünü Görüntüle
                                </a>
                                
                                @if($stockMovement->customer)
                                    <a href="{{ route('customers.show', $stockMovement->customer) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="bi bi-person me-2"></i>Müşteriyi Görüntüle
                                    </a>
                                @endif

                                <hr class="my-2">

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

                                @if(Auth::user()->hasPermission('stock_return'))
                                    <a href="{{ route('stock-movements.create', ['type' => 'iade']) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Yeni İade
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Son Güncelleme -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>Zaman Bilgileri
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Oluşturma:</span>
                                    <span>{{ $stockMovement->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Son Güncelleme:</span>
                                    <span>{{ $stockMovement->updated_at->format('d.m.Y H:i') }}</span>
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