<x-app-layout>
    <x-slot name="header">
        <i class="bi bi-box me-2"></i>Yeni Ürün Oluştur
    </x-slot>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ürün Bilgileri</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Geri Dön
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
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
                                               value="{{ old('barcode') }}" 
                                               placeholder="Boş bırakılırsa otomatik oluşturulur">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Boş bırakılırsa sistem otomatik barkod oluşturacaktır.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Ürün açıklaması (opsiyonel)">{{ old('description') }}</textarea>
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
                                                    {{ old('category_id', $selectedCategory) == $category->id ? 'selected' : '' }}>
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
                                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                           value="{{ old('unit_price') }}" 
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
                                           value="{{ old('tax_rate', '18') }}" 
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
                                    <label for="current_stock" class="form-label">Başlangıç Stok <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('current_stock') is-invalid @enderror" 
                                           id="current_stock" 
                                           name="current_stock" 
                                           value="{{ old('current_stock', '0') }}" 
                                           placeholder="0"
                                           min="0"
                                           required>
                                    @error('current_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_stock" class="form-label">Minimum Stok Uyarısı <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('min_stock') is-invalid @enderror" 
                                           id="min_stock" 
                                           name="min_stock" 
                                           value="{{ old('min_stock', '5') }}" 
                                           placeholder="5"
                                           min="0"
                                           required>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Bu sayının altında stok uyarısı verilir</div>
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
                            <div class="form-text">JPG, PNG, GIF formatlarında, maksimum 2MB</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Ürün Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Resim Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Resim Önizleme</h6>
                </div>
                <div class="card-body text-center">
                    <img id="image_preview" 
                         src="{{ asset('images/no-image.svg') }}" 
                         alt="Resim Önizleme" 
                         class="img-fluid rounded" 
                         style="max-height: 200px;">
                </div>
            </div>

            <!-- Barkod Önizleme -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-upc me-2"></i>Barkod Önizleme</h6>
                </div>
                <div class="card-body text-center">
                    <div id="barcode_preview" class="border rounded p-3 bg-light">
                        <span class="text-muted">Barkod buraya gelecek</span>
                    </div>
                </div>
            </div>

            <!-- Yardımcı Bilgi -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Bilgi</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>Ürün adı zorunludur</li>
                        <li>Barkod boş bırakılırsa otomatik oluşturulur</li>
                        <li>Resim maksimum 2MB olabilir</li>
                        <li>KDV dahil fiyat otomatik hesaplanır</li>
                        <li>Minimum stok uyarı için kullanılır</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Resim önizleme
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image_preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
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
            updateBarcodePreview(barcode);
        }

        // Barkod önizleme
        function updateBarcodePreview(barcode) {
            const preview = document.getElementById('barcode_preview');
            if (barcode && barcode.length > 0) {
                preview.innerHTML = `
                    <div class="font-monospace fw-bold fs-5">${barcode}</div>
                    <div class="mt-2">
                        <svg width="150" height="20">
                            ${generateBarcodeLines(barcode)}
                        </svg>
                    </div>
                `;
            } else {
                preview.innerHTML = '<span class="text-muted">Barkod buraya gelecek</span>';
            }
        }

        // Basit barkod çizgileri (görsel amaçlı)
        function generateBarcodeLines(barcode) {
            let lines = '';
            for (let i = 0; i < barcode.length; i++) {
                const x = i * 10 + 10;
                const height = parseInt(barcode[i]) + 10;
                lines += `<rect x="${x}" y="0" width="8" height="${height}" fill="#000"/>`;
            }
            return lines;
        }

        // Barkod input değiştiğinde önizlemeyi güncelle
        document.getElementById('barcode').addEventListener('input', function(e) {
            updateBarcodePreview(e.target.value);
        });

        // Sayfa yüklendiğinde hesaplamaları yap
        document.addEventListener('DOMContentLoaded', function() {
            calculatePriceWithTax();
            updateBarcodePreview(document.getElementById('barcode').value);
        });
    </script>
    @endpush
</x-app-layout> 