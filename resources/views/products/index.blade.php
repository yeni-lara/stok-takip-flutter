@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-box me-2"></i>Ürünler
                </h1>
                <div>
                    <button type="button" class="btn btn-success me-2" id="qr-print-btn">
                        <i class="bi bi-qr-code me-1"></i>QR Çıktısı Al (<span id="selected-count">Tümü</span>)
                    </button>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Yeni Ürün
                    </a>
                </div>
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
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
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
                                                <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" data-barcode="{{ $product->barcode }}" data-name="{{ $product->name }}">
                                            </td>
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
        // Checkbox işlevselliği
        const selectAll = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectedCountSpan = document.getElementById('selected-count');

        // Tümünü seç/bırak
        selectAll.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Her checkbox değişiminde sayacı güncelle
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                
                // Tümü seçili checkbox durumunu güncelle
                const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
                selectAll.checked = checkedCount === productCheckboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < productCheckboxes.length;
            });
        });

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (checkedCount === 0) {
                selectedCountSpan.textContent = 'Tümü';
            } else {
                selectedCountSpan.textContent = `${checkedCount} Seçili`;
            }
        }

        // QR Çıktısı Al butonu
        document.getElementById('qr-print-btn').addEventListener('click', function() {
            generateQRPrintPage();
        });

        // QR kod oluşturma fonksiyonu (API kullanarak)
        function generateQRCode(text, size = 100) {
            const baseUrl = 'https://api.qrserver.com/v1/create-qr-code/';
            const params = new URLSearchParams({
                size: `${size}x${size}`,
                data: text,
                format: 'png'
            });
            return `${baseUrl}?${params}`;
        }

        async function generateQRPrintPage() {
            const btn = document.getElementById('qr-print-btn');
            const originalText = btn.innerHTML;
            
            try {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Hazırlanıyor...';
                
                // Seçili ürünleri kontrol et
                const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
                let products = [];
                
                if (selectedCheckboxes.length > 0) {
                    // Seçili ürünlerden veri topla
                    products = Array.from(selectedCheckboxes).map(checkbox => ({
                        id: checkbox.value,
                        name: checkbox.dataset.name,
                        barcode: checkbox.dataset.barcode
                    }));
                } else {
                    // Hiç seçili değilse API'den tümünü al
                    const response = await fetch('/api/products');
                    products = await response.json();
                }
                
                if (products.length === 0) {
                    alert('Hiç ürün bulunamadı!');
                    return;
                }
                
                // Yeni pencere aç
                const printWindow = window.open('', '_blank');
                let htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>QR Kod Çıktısı</title>
                        <style>
                            @page {
                                margin: 10mm;
                                size: A4;
                            }
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 0;
                            }
                            .qr-grid {
                                display: grid;
                                grid-template-columns: repeat(2, 1fr);
                                gap: 2mm;
                                page-break-inside: avoid;
                                margin-bottom: 3mm;
                            }
                            .qr-item {
                                border: 1px dashed #ccc;
                                padding: 3mm;
                                height: 45mm;
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                page-break-inside: avoid;
                            }
                            .qr-code {
                                flex: 0 0 auto;
                                margin-right: 6mm;
                            }
                            .qr-info {
                                flex: 1;
                                text-align: left;
                                display: flex;
                                flex-direction: column;
                                justify-content: center;
                                padding-left: 3mm;
                            }
                            .product-name {
                                font-size: 10px;
                                font-weight: bold;
                                margin-bottom: 2mm;
                                word-wrap: break-word;
                                line-height: 1.3;
                            }
                            .barcode-text {
                                font-family: monospace;
                                font-size: 9px;
                                margin-top: 1mm;
                            }
                            .page-break {
                                page-break-before: always;
                            }
                            @media print {
                                .no-print { display: none; }
                            }

                            .print-controls {
                                position: fixed;
                                top: 10px;
                                right: 10px;
                                background: white;
                                padding: 10px;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-controls no-print">
                            <button onclick="window.print()" style="margin-right: 10px;">🖨️ Yazdır</button>
                            <button onclick="window.close()">❌ Kapat</button>
                        </div>
                        

                `;
                
                // Her sayfa için 10'lu gruplar halinde QR kodları oluştur
                for (let i = 0; i < products.length; i += 10) {
                    if (i > 0) {
                        htmlContent += '<div class="page-break"></div>';
                    }
                    
                    htmlContent += '<div class="qr-grid">';
                    
                    const pageProducts = products.slice(i, i + 10);
                    for (const product of pageProducts) {
                        const qrImageUrl = product.barcode ? generateQRCode(product.barcode, 130) : '';
                        htmlContent += `
                            <div class="qr-item">
                                <div class="qr-code">
                                    ${product.barcode ? 
                                        `<img src="${qrImageUrl}" alt="QR Code" style="width: 130px; height: 130px;">` : 
                                        '<div style="width: 130px; height: 130px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999; font-size: 9px;">Barkod Yok</div>'
                                    }
                                </div>
                                <div class="qr-info">
                                    <div class="product-name">${product.name}</div>
                                    <div class="barcode-text">${product.barcode || 'Barkod Yok'}</div>
                                </div>
                            </div>
                        `;
                    }
                    
                    htmlContent += '</div>';
                }
                
                htmlContent += `
                    </body>
                    </html>
                `;
                
                printWindow.document.write(htmlContent);
                printWindow.document.close();
                
            } catch (error) {
                console.error('QR çıktısı oluşturma hatası:', error);
                alert('QR çıktısı oluşturulurken hata oluştu: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

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