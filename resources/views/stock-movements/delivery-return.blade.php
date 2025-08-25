@extends('layouts.app')

@section('content')
<!-- Başarı Modal'ı -->
@if(session('success'))
<div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>İşlem Başarılı
                </h5>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5>{{ session('success') }}</h5>
                <p class="text-muted">İşleminiz başarıyla tamamlandı.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" onclick="document.getElementById('successModal').style.display='none'">
                    <i class="bi bi-check me-2"></i>Tamam
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-2"></i>Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-arrow-repeat me-2"></i>Stok İadesi
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Barkod Okuyucu Alanı -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="bi bi-qr-code-scan me-2"></i>Barkod Okuyucu
                                    </h5>
                                    <p class="text-muted">İade edilecek ürün barkodunu kamera ile okutun</p>
                                    <div id="barcode-scanner" style="max-width: 400px; margin: 0 auto;">
                                        <video id="video" style="width: 100%; height: 300px; border: 2px dashed #0dcaf0; border-radius: 8px;"></video>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" id="start-scan" class="btn btn-info">
                                            <i class="bi bi-camera me-2"></i>Kamerayı Başlat
                                        </button>
                                        <button type="button" id="stop-scan" class="btn btn-secondary" style="display: none;">
                                            <i class="bi bi-stop-circle me-2"></i>Kamerayı Durdur
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Alternatif olarak barkod numarasını manuel girebilirsiniz</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok İade Formu -->
                    <form method="POST" action="{{ route('stock-movements.store') }}" id="return-form">
                        @csrf
                        <input type="hidden" name="type" value="iade">

                        <div class="row g-3">
                            <!-- Barkod/Ürün Seçimi -->
                            <div class="col-md-6">
                                <label for="barcode" class="form-label">
                                    <i class="bi bi-upc-scan me-1"></i>Barkod Numarası
                                </label>
                                <input type="text" class="form-control form-control-lg" id="barcode" name="barcode" 
                                       placeholder="Barkod okutun veya girin" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Ürün Bilgisi -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-box-seam me-1"></i>Seçili Ürün
                                </label>
                                <div id="product-info" class="form-control form-control-lg bg-light">
                                    <span class="text-muted">Barkod okutarak ürün seçin</span>
                                </div>
                                <input type="hidden" name="product_id" id="product_id">
                            </div>

                            <!-- Miktar -->
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">
                                    <i class="bi bi-hash me-1"></i>İade Miktarı
                                </label>
                                <input type="number" class="form-control form-control-lg" id="quantity" name="quantity" 
                                       min="1" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Müşteri -->
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">
                                    <i class="bi bi-building me-1"></i>İade Eden Müşteri
                                </label>
                                <select class="form-select form-select-lg" id="customer_id" name="customer_id" required>
                                    <option value="">Müşteri seçin</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Not -->
                            <div class="col-12">
                                <label for="note" class="form-label">
                                    <i class="bi bi-sticky me-1"></i>İade Sebebi/Not
                                </label>
                                <textarea class="form-control" id="note" name="note" rows="3" 
                                          placeholder="İade sebebi veya açıklama..." required></textarea>
                                <div class="form-text">İade sebebini belirtmeniz zorunludur</div>
                            </div>

                            <!-- Mevcut Stok Bilgisi -->
                            <div class="col-12">
                                <div id="stock-info" class="alert alert-info" style="display: none;">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span id="stock-text"></span>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-info btn-lg flex-fill" id="submit-btn" disabled>
                                        <i class="bi bi-arrow-repeat me-2"></i>Stok İadesi Yap
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">
                                        <i class="bi bi-arrow-left me-2"></i>Geri
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QuaggaJS for Barcode Scanning -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode');
    const productInfo = document.getElementById('product-info');
    const productIdInput = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stock-info');
    const stockText = document.getElementById('stock-text');
    const submitBtn = document.getElementById('submit-btn');
    const startScanBtn = document.getElementById('start-scan');
    const stopScanBtn = document.getElementById('stop-scan');
    const video = document.getElementById('video');
    const noteInput = document.getElementById('note');
    
    let isScanning = false;
    let currentProduct = null;

    // Barkod değiştiğinde ürün bilgisini getir
    barcodeInput.addEventListener('input', function() {
        if (this.value.length >= 8) { // Minimum barkod uzunluğu
            fetchProductByBarcode(this.value);
        }
    });

    // Ürün bilgisi getir
    async function fetchProductByBarcode(barcode) {
        try {
            const response = await fetch(`/test-barcode/${barcode}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            if (response.ok) {
                const product = await response.json();
                showProductInfo(product);
            } else {
                showError('Ürün bulunamadı!');
            }
        } catch (error) {
            showError('Ürün aranırken hata oluştu!');
        }
    }

    // Ürün bilgisini göster
    function showProductInfo(product) {
        currentProduct = product;
        productIdInput.value = product.id;
        productInfo.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <strong>${product.name}</strong><br>
                    <small class="text-muted">${product.category || 'Kategori yok'}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary">${product.current_stock} adet</span>
                </div>
            </div>
        `;
        
        stockText.textContent = `Mevcut stok: ${product.current_stock} adet (İade sonrası: ${product.current_stock + 1} adet olacak)`;
        stockInfo.style.display = 'block';
        
        quantityInput.value = 1;
        checkFormValid();
    }

    // Hata göster
    function showError(message) {
        productInfo.innerHTML = `<span class="text-danger">${message}</span>`;
        productIdInput.value = '';
        stockInfo.style.display = 'none';
        currentProduct = null;
        checkFormValid();
    }

    // Form geçerliliğini kontrol et
    function checkFormValid() {
        const isValid = productIdInput.value && 
                       quantityInput.value && 
                       document.getElementById('customer_id').value &&
                       noteInput.value.trim().length > 0;
        submitBtn.disabled = !isValid;
    }

    // Form elemanları değiştiğinde kontrol et
    [quantityInput, document.getElementById('customer_id'), noteInput].forEach(input => {
        input.addEventListener('input', checkFormValid);
        input.addEventListener('change', checkFormValid);
    });

    // Kamera başlat
    startScanBtn.addEventListener('click', function() {
        if (!isScanning) {
            startBarcodeScanner();
        }
    });

    // Kamera durdur
    stopScanBtn.addEventListener('click', function() {
        if (isScanning) {
            stopBarcodeScanner();
        }
    });

    // Barkod tarayıcı başlat
    function startBarcodeScanner() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: video,
                constraints: {
                    width: 400,
                    height: 300,
                    facingMode: "environment"
                }
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
            }
        }, function(err) {
            if (err) {
                alert('Kamera erişimi başarısız: ' + err);
                return;
            }
            Quagga.start();
            isScanning = true;
            startScanBtn.style.display = 'none';
            stopScanBtn.style.display = 'inline-block';
        });

        // Barkod tespit edildiğinde
        Quagga.onDetected(function(data) {
            const barcode = data.codeResult.code;
            barcodeInput.value = barcode;
            fetchProductByBarcode(barcode);
            stopBarcodeScanner();
        });
    }

    // Barkod tarayıcı durdur
    function stopBarcodeScanner() {
        if (isScanning) {
            Quagga.stop();
            isScanning = false;
            startScanBtn.style.display = 'inline-block';
            stopScanBtn.style.display = 'none';
        }
    }

    // Form gönderimi
    document.getElementById('return-form').addEventListener('submit', function(e) {
        if (!currentProduct) {
            e.preventDefault();
            alert('Lütfen geçerli bir ürün seçin!');
            return;
        }

        if (!noteInput.value.trim()) {
            e.preventDefault();
            alert('İade sebebini belirtmeniz zorunludur!');
            return;
        }
    });
});
</script>
@endsection 