@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-box me-2"></i>{{ $product->name }}
                    @if(!$product->is_active)
                        <span class="badge bg-danger ms-2">Pasif</span>
                    @endif
                    @if($product->isLowStock())
                        <span class="badge bg-warning ms-2">Az Stok</span>
                    @endif
                </h1>
                <div class="btn-group">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Ana Ürün Bilgileri -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ürün Detayları</h5>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning me-2">
                            <i class="bi bi-pencil me-2"></i>Düzenle
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid rounded shadow"
                                 style="max-height: 250px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%" class="text-muted">Ürün Adı:</td>
                                    <td><strong class="h5">{{ $product->name }}</strong></td>
                                </tr>
                                @if($product->barcode)
                                <tr>
                                    <td class="text-muted">Barkod:</td>
                                    <td>
                                        <span class="font-monospace fw-bold">{{ $product->barcode }}</span>
                                        <i class="bi bi-upc ms-2"></i>
                                    </td>
                                </tr>
                                @endif
                                @if($product->description)
                                <tr>
                                    <td class="text-muted">Açıklama:</td>
                                    <td>{{ $product->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Kategori:</td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $product->category->name }}</span>
                                    </td>
                                </tr>
                                @if($product->supplier)
                                <tr>
                                    <td class="text-muted">Tedarikçi:</td>
                                    <td>{{ $product->supplier->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Durum:</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success fs-6">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fiyat ve Stok Bilgileri -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-currency-exchange me-2"></i>Fiyat ve Stok Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-primary">{{ number_format($product->unit_price, 2) }} ₺</h4>
                                <small class="text-muted">Birim Fiyat (KDV Hariç)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-success">{{ number_format($product->price_with_tax, 2) }} ₺</h4>
                                <small class="text-muted">KDV Dahil Fiyat</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="text-warning">%{{ $product->tax_rate }}</h4>
                                <small class="text-muted">KDV Oranı</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info">{{ number_format($product->total_value, 2) }} ₺</h4>
                            <small class="text-muted">Toplam Stok Değeri</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Hareketleri -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Son Stok Hareketleri</h5>
                    @if(Auth::user()->hasPermission('stock_entry') || Auth::user()->hasPermission('stock_exit') || Auth::user()->hasPermission('stock_return'))
                        <a href="{{ route('stock-movements.create') }}?product={{ $product->id }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Yeni Hareket
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($product->stockMovements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Tip</th>
                                        <th>Miktar</th>
                                        <th>Önceki Stok</th>
                                        <th>Yeni Stok</th>
                                        <th>Kullanıcı</th>
                                        <th>Not</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->stockMovements as $movement)
                                        <tr>
                                            <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $movement->type_color }}">
                                                    <i class="{{ $movement->type_icon }}"></i> {{ ucfirst($movement->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-{{ $movement->type === 'çıkış' ? 'danger' : 'success' }}">
                                                    {{ $movement->type === 'çıkış' ? '-' : '+' }}{{ $movement->quantity }}
                                                </strong>
                                            </td>
                                            <td>{{ $movement->previous_stock }}</td>
                                            <td>{{ $movement->new_stock }}</td>
                                            <td>{{ $movement->user->name }}</td>
                                            <td>{{ Str::limit($movement->note, 30) ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($product->stockMovements()->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('stock-movements.index') }}?product={{ $product->id }}" class="btn btn-outline-primary">
                                    Tüm Hareketleri Gör ({{ $product->stockMovements()->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                            <h6 class="mt-3 text-muted">Henüz stok hareketi bulunmuyor</h6>
                            <p class="text-muted">İlk stok hareketini oluşturmak için yukarıdaki butonu kullanın.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-md-4">
            <!-- Stok Durumu -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-boxes me-2"></i>Stok Durumu</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h2 class="text-{{ $product->isLowStock() ? 'warning' : 'success' }}">
                            {{ $product->current_stock }}
                        </h2>
                        <p class="text-muted mb-0">Mevcut Stok</p>
                    </div>
                    
                    @if($product->isLowStock())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Düşük Stok Uyarısı!</strong><br>
                            Minimum stok: {{ $product->min_stock }}
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            Stok durumu normal<br>
                            Minimum stok: {{ $product->min_stock }}
                        </div>
                    @endif
                    
                    <!-- Stok Seviyesi Göstergesi -->
                    <div class="progress mb-3" style="height: 20px;">
                        @php
                            $percentage = $product->min_stock > 0 ? min(100, ($product->current_stock / ($product->min_stock * 2)) * 100) : 100;
                        @endphp
                        <div class="progress-bar bg-{{ $percentage < 50 ? 'danger' : ($percentage < 80 ? 'warning' : 'success') }}" 
                             style="width: {{ $percentage }}%">
                            {{ round($percentage) }}%
                        </div>
                    </div>
                    <small class="text-muted">Stok seviyesi göstergesi</small>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->hasPermission('stock_entry'))
                            <a href="{{ route('stock-movements.create') }}?product={{ $product->id }}&type=giriş" 
                               class="btn btn-outline-success">
                                <i class="bi bi-arrow-up-circle me-2"></i>Stok Girişi
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_exit'))
                            <a href="{{ route('stock-movements.create') }}?product={{ $product->id }}&type=çıkış" 
                               class="btn btn-outline-danger">
                                <i class="bi bi-arrow-down-circle me-2"></i>Stok Çıkışı
                            </a>
                        @endif
                        
                        @if(Auth::user()->hasPermission('stock_return'))
                            <a href="{{ route('stock-movements.create') }}?product={{ $product->id }}&type=iade" 
                               class="btn btn-outline-warning">
                                <i class="bi bi-arrow-repeat me-2"></i>Stok İadesi
                            </a>
                        @endif
                        
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-info">
                            <i class="bi bi-pencil me-2"></i>Ürünü Düzenle
                        </a>
                        
                        @if($product->stockMovements->count() == 0)
                            <form action="{{ route('products.destroy', $product) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-2"></i>Ürünü Sil
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ürün İstatistikleri -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>İstatistikler</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Hareket:</span>
                        <strong>{{ $product->stockMovements()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Giriş:</span>
                        <strong class="text-success">{{ $product->stockMovements()->where('type', 'giriş')->sum('quantity') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Çıkış:</span>
                        <strong class="text-danger">{{ $product->stockMovements()->where('type', 'çıkış')->sum('quantity') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam İade:</span>
                        <strong class="text-warning">{{ $product->stockMovements()->where('type', 'iade')->sum('quantity') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Oluşturma:</span>
                        <strong>{{ $product->created_at->format('d.m.Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Son Güncelleme:</span>
                        <strong>{{ $product->updated_at->format('d.m.Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Barkod kopyalama
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeElement = document.querySelector('.font-monospace.fw-bold');
            if (barcodeElement) {
                barcodeElement.style.cursor = 'pointer';
                barcodeElement.title = 'Kopyalamak için tıklayın';
                barcodeElement.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent).then(function() {
                        // Geçici olarak "Kopyalandı!" göster
                        const originalText = barcodeElement.textContent;
                        barcodeElement.textContent = 'Kopyalandı!';
                        setTimeout(function() {
                            barcodeElement.textContent = originalText;
                        }, 1000);
                    });
                });
            }
        });
    </script>
@endpush 