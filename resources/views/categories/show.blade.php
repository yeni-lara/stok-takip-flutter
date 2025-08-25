@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-tags me-2"></i>{{ $category->name }}
                    @if(!$category->is_active)
                        <span class="badge bg-danger ms-2">Pasif</span>
                    @endif
                </h1>
                <div class="btn-group">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Düzenle
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Kategori Bilgileri -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kategori Detayları</h5>
                    <div>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning me-2">
                            <i class="bi bi-pencil me-2"></i>Düzenle
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Kategori Adı</h6>
                            <p class="h5">{{ $category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Durum</h6>
                            <p>
                                @if($category->is_active)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Pasif</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($category->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-muted">Açıklama</h6>
                                <p>{{ $category->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Oluşturma Tarihi</h6>
                            <p>{{ $category->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Son Güncelleme</h6>
                            <p>{{ $category->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Ürünleri -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Son Eklenen Ürünler</h5>
                    @if($category->products()->count() > 10)
                        <a href="{{ route('products.index') }}?category={{ $category->id }}" class="btn btn-sm btn-outline-primary">
                            Tümünü Gör ({{ $productCount }})
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($category->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün Adı</th>
                                        <th>Stok</th>
                                        <th>Fiyat</th>
                                        <th>Durum</th>
                                        <th>Eklenme</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->barcode)
                                                    <br><small class="text-muted">{{ $product->barcode }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $product->isLowStock() ? 'warning' : 'info' }}">
                                                    {{ $product->current_stock }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($product->price_with_tax, 2) }} ₺</td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Pasif</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->created_at->format('d.m.Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-box fs-1 text-muted"></i>
                            <h6 class="mt-3 text-muted">Bu kategoride henüz ürün bulunmuyor</h6>
                            <p class="text-muted">İlk ürünü eklemek için ürün yönetimi bölümüne gidin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>İstatistikler</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $productCount }}</h3>
                        <p class="text-muted mb-0">Toplam Ürün</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Stok:</span>
                        <strong>{{ number_format($totalStock) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Değer:</span>
                        <strong>{{ number_format($totalValue, 2) }} ₺</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Aktif Ürünler:</span>
                        <strong>{{ $category->products()->active()->count() }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span>Pasif Ürünler:</span>
                        <strong>{{ $category->products()->where('is_active', false)->count() }}</strong>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.create') }}?category={{ $category->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Bu Kategoriye Ürün Ekle
                        </a>
                        
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>Kategoriyi Düzenle
                        </a>
                        
                        @if($productCount > 0)
                            <a href="{{ route('products.index') }}?category={{ $category->id }}" class="btn btn-outline-info">
                                <i class="bi bi-box me-2"></i>Kategorideki Ürünleri Gör
                            </a>
                        @endif
                        
                        @if($productCount == 0)
                            <form action="{{ route('categories.destroy', $category) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-2"></i>Kategoriyi Sil
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 