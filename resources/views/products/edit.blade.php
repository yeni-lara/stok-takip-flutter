@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-box me-2"></i>Ürün Düzenle: {{ $product->name }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Görüntüle
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ürün Bilgilerini Düzenle</h5>
                    <div>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info me-2">
                            <i class="bi bi-eye me-2"></i>Görüntüle
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $product->name) }}" 
                                           placeholder="Ürün adını giriniz"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode" class="form-label">Barkod</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control @error('barcode') is-invalid @enderror" 
                                               id="barcode" 
                                               name="barcode" 
                                               value="{{ old('barcode', $product->barcode) }}" 
                                               placeholder="Barkod giriniz">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Ürün açıklaması (opsiyonel)">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Kategori seçiniz</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Tedarikçi</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" 
                                            name="supplier_id">
                                        <option value="">Tedarikçi seçiniz (opsiyonel)</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" 
                                                    {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit_price" class="form-label">Birim Fiyat (₺) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('unit_price') is-invalid @enderror" 
                                           id="unit_price" 
                                           name="unit_price" 
                                           value="{{ old('unit_price', $product->unit_price) }}" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">KDV Oranı (%) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('tax_rate') is-invalid @enderror" 
                                           id="tax_rate" 
                                           name="tax_rate" 
                                           value="{{ old('tax_rate', $product->tax_rate) }}" 
                                           placeholder="18"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           required>
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">KDV Dahil Fiyat</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="price_with_tax" 
                                           readonly 
                                           placeholder="0.00 ₺">
                                    <div class="form-text">Otomatik hesaplanır</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_stock" class="form-label">Minimum Stok Uyarısı <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('min_stock') is-invalid @enderror" 
                                           id="min_stock" 
                                           name="min_stock" 
                                           value="{{ old('min_stock', $product->min_stock) }}" 
                                           placeholder="5"
                                           min="0"
                                           required>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Bu sayının altında stok uyarısı verilir</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mevcut Stok</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="{{ $product->current_stock }}" 
                                           readonly>
                                    <div class="form-text">Stok hareketi ile değiştirilir</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Ürün Resmi</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, GIF formatlarında, maksimum 2MB. Boş bırakılırsa mevcut resim korunur.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktif Ürün</strong>
                                </label>
                                <div class="form-text">Aktif ürünler sistem genelinde görünür</div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pencil me-2"></i>Ürünü Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Mevcut Resim -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Mevcut Resim</h6>
                </div>
                <div class="card-body text-center">
                    <img id="current_image" 
                         src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 200px;">
                </div>
            </div>

            <!-- Yeni Resim Önizleme -->
            <div class="card mt-4" id="new_image_card" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Yeni Resim Önizleme</h6>
                </div>
                <div class="card-body text-center">
                    <img id="image_preview" 
                         src="" 
                         alt="Yeni Resim" 
                         class="img-fluid rounded" 
                         style="max-height: 200px;">
                </div>
            </div>

            <!-- Ürün İstatistikleri -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Ürün İstatistikleri</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Mevcut Stok:</span>
                        <strong class="text-{{ $product->isLowStock() ? 'warning' : 'success' }}">{{ $product->current_stock }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Minimum Stok:</span>
                        <strong>{{ $product->min_stock }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Stok Değeri:</span>
                        <strong>{{ number_format($product->total_value, 2) }} ₺</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Hareket Sayısı:</span>
                        <strong>{{ $product->stockMovements()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Oluşturma:</span>
                        <strong>{{ $product->created_at->format('d.m.Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Uyarılar -->
            @if($product->stockMovements()->count() > 0)
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Bu ürüne ait {{ $product->stockMovements()->count() }} stok hareketi bulunmaktadır.
                </div>
            @endif

            @if($product->isLowStock())
                <div class="alert alert-warning mt-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Dikkat:</strong> Bu ürün düşük stokta!
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Yeni resim önizleme
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const newImageCard = document.getElementById('new_image_card');
            const preview = document.getElementById('image_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    newImageCard.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                newImageCard.style.display = 'none';
            }
        });

        // KDV dahil fiyat hesaplama
        function calculatePriceWithTax() {
            const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const priceWithTax = unitPrice * (1 + (taxRate / 100));
            document.getElementById('price_with_tax').value = priceWithTax.toFixed(2) + ' ₺';
        }

        document.getElementById('unit_price').addEventListener('input', calculatePriceWithTax);
        document.getElementById('tax_rate').addEventListener('input', calculatePriceWithTax);

        // Barkod üretme
        function generateBarcode() {
            const barcode = '999' + Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');
            document.getElementById('barcode').value = barcode;
        }

        // Sayfa yüklendiğinde hesaplamaları yap
        document.addEventListener('DOMContentLoaded', function() {
            calculatePriceWithTax();
        });
    </script>
@endpush 