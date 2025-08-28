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
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-arrow-down-circle me-2"></i>Stok Çıkışı
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Barkod Okuyucu Alanı -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="bi bi-qr-code-scan me-2"></i>Barkod Okuyucu
                                    </h5>
                                    <p class="text-muted">Ürün barkodunu kamera ile okutun</p>
                                    <div id="barcode-scanner" style="max-width: 500px; margin: 0 auto;">
                                        <video id="video" style="width: 100%; height: 350px; border: 2px dashed #ffc107; border-radius: 8px; object-fit: cover; background: #000;"></video>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" id="start-scan" class="btn btn-warning">
                                            <i class="bi bi-camera me-2"></i>Kamerayı Başlat
                                        </button>
                                        <button type="button" id="stop-scan" class="btn btn-secondary" style="display: none;">
                                            <i class="bi bi-stop-circle me-2"></i>Kamerayı Durdur
                                        </button>
                                        <div id="camera-status" class="mt-2 text-sm"></div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Alternatif olarak barkod numarasını manuel girebilirsiniz</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Çıkış Formu -->
                    <form method="POST" action="{{ route('stock-movements.store') }}" id="exit-form">
                        @csrf
                        <input type="hidden" name="type" value="çıkış">

                        <div class="row g-3">
                            <!-- Barkod/Ürün Seçimi -->
                            <div class="col-md-6">
                                <label for="barcode" class="form-label">
                                    <i class="bi bi-upc-scan me-1"></i>Barkod Numarası
                                </label>
                                <input type="text" class="form-control form-control-lg" id="barcode" 
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
                                    <i class="bi bi-hash me-1"></i>Miktar
                                </label>
                                <input type="number" class="form-control form-control-lg" id="quantity" name="quantity" 
                                       min="1" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Müşteri -->
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">
                                    <i class="bi bi-building me-1"></i>Müşteri (Opsiyonel)
                                </label>
                                <select class="form-select form-select-lg" id="customer_id" name="customer_id">
                                    <option value="">Müşteri seçin (opsiyonel)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Not -->
                            <div class="col-12">
                                <label for="note" class="form-label">
                                    <i class="bi bi-sticky me-1"></i>Açıklama
                                </label>
                                <textarea class="form-control" id="note" name="note" rows="3" 
                                          placeholder="Teslimat notu veya açıklama..."></textarea>
                                <div class="form-text"></div>
                                <div class="invalid-feedback"></div>
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
                                    <button type="submit" class="btn btn-warning btn-lg flex-fill" id="submit-btn" disabled>
                                        <i class="bi bi-arrow-down-circle me-2"></i>Stok Çıkışı Yap
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
    const cameraStatus = document.getElementById('camera-status');
    
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
                const errorData = await response.json();
                showError(errorData.message || 'Ürün bulunamadı!');
                console.log('Error response:', errorData);
            }
        } catch (error) {
            showError('Ürün aranırken hata oluştu!');
            console.error('Fetch error:', error);
        }
    }

    // Ürün bilgisini göster
    function showProductInfo(product) {
        currentProduct = product;
        productIdInput.value = product.id;
        console.log('Product ID set to:', product.id);
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
        
        stockText.textContent = `Mevcut stok: ${product.current_stock} adet`;
        stockInfo.style.display = 'block';
        
        if (product.current_stock > 0) {
            quantityInput.max = product.current_stock;
            quantityInput.value = 1;
            checkFormValid();
        } else {
            showError('Bu ürünün stoğu bulunmuyor!');
        }
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
        const customerId = document.getElementById('customer_id').value;
        const noteValue = document.getElementById('note').value.trim();
        
        const isValid = productIdInput.value && 
                       quantityInput.value &&
                       (customerId || noteValue); // Müşteri VEYA açıklama olmalı
        
        submitBtn.disabled = !isValid;
        
        // Hata mesajları göster/gizle
        const noteField = document.getElementById('note');
        if (!customerId && !noteValue) {
            noteField.classList.add('is-invalid');
            noteField.nextElementSibling.nextElementSibling.textContent = 'Müşteri seçilmediğinde açıklama zorunludur';
        } else {
            noteField.classList.remove('is-invalid');
        }
    }

    // Form elemanları değiştiğinde kontrol et
    [quantityInput, document.getElementById('customer_id'), document.getElementById('note')].forEach(input => {
        input.addEventListener('change', checkFormValid);
        input.addEventListener('input', checkFormValid);
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
        // Önce kamera izni kontrol et
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Bu tarayıcı kamera erişimini desteklemiyor!');
            cameraStatus.innerHTML = '<span class="text-danger">❌ Tarayıcı desteklenmiyor</span>';
            return;
        }

        // HTTPS kontrolü
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            alert('Kamera erişimi için HTTPS gereklidir!');
            cameraStatus.innerHTML = '<span class="text-warning">⚠️ HTTPS gerekli</span>';
            return;
        }

        cameraStatus.innerHTML = '<span class="text-info">📷 Kamera erişimi isteniyor...</span>';

        // Önce kamera iznini al
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: "environment",
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        })
        .then(function(stream) {
            // Kamera erişimi başarılı, şimdi Quagga'yı başlat
            video.srcObject = stream;
            video.play();

            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: video,
                    constraints: {
                        width: { min: 640, ideal: 1280, max: 1920 },
                        height: { min: 480, ideal: 720, max: 1080 },
                        facingMode: "environment"
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 2,
                frequency: 10,
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader", 
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "codabar_reader",
                        "upc_reader",
                        "upc_e_reader"
                    ]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error('Quagga init error:', err);
                    alert('Barkod tarayıcı başlatılamadı: ' + err.message);
                    // Stream'i kapat
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                    return;
                }
                
                console.log('Quagga başarıyla başlatıldı');
                Quagga.start();
                isScanning = true;
                startScanBtn.style.display = 'none';
                stopScanBtn.style.display = 'inline-block';
                startScanBtn.textContent = 'Tarama Aktif...';
                cameraStatus.innerHTML = '<span class="text-success">✅ Tarama aktif - Barkodu okutun</span>';
            });
        })
        .catch(function(err) {
            console.error('Camera access error:', err);
            let errorMessage = 'Kamera erişimi başarısız: ';
            
            switch(err.name) {
                case 'NotAllowedError':
                    errorMessage += 'Kamera izni verilmedi. Tarayıcı ayarlarından kamera iznini etkinleştirin.';
                    break;
                case 'NotFoundError':
                    errorMessage += 'Kamera bulunamadı.';
                    break;
                case 'NotReadableError':
                    errorMessage += 'Kamera başka bir uygulama tarafından kullanılıyor.';
                    break;
                default:
                    errorMessage += err.message;
            }
            
            alert(errorMessage);
            cameraStatus.innerHTML = '<span class="text-danger">❌ ' + errorMessage + '</span>';
        });

        // Barkod tespit edildiğinde
        Quagga.onDetected(function(data) {
            if (data && data.codeResult && data.codeResult.code) {
                const barcode = data.codeResult.code;
                console.log('Barkod tespit edildi:', barcode);
                
                // Tekrarlanan okumaları önle
                if (barcode.length >= 8) {
                    barcodeInput.value = barcode;
                    fetchProductByBarcode(barcode);
                    stopBarcodeScanner();
                }
            }
        });
    }

    // Barkod tarayıcı durdur
    function stopBarcodeScanner() {
        if (isScanning) {
            try {
                Quagga.stop();
                
                // Video stream'i durdur
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }
                
                isScanning = false;
                startScanBtn.style.display = 'inline-block';
                stopScanBtn.style.display = 'none';
                startScanBtn.textContent = 'Kamerayı Başlat';
                cameraStatus.innerHTML = '<span class="text-secondary">⏹️ Tarama durduruldu</span>';
                
                console.log('Barkod tarayıcı durduruldu');
            } catch (error) {
                console.error('Scanner durdurma hatası:', error);
            }
        }
    }

    // Form gönderimi
    document.getElementById('exit-form').addEventListener('submit', function(e) {
        if (!currentProduct) {
            e.preventDefault();
            alert('Lütfen geçerli bir ürün seçin!');
            return;
        }

        const quantity = parseInt(quantityInput.value);
        if (quantity > currentProduct.current_stock) {
            e.preventDefault();
            alert(`Maksimum ${currentProduct.current_stock} adet çıkış yapabilirsiniz!`);
            return;
        }

        // Müşteri veya açıklama kontrolü
        const customerId = document.getElementById('customer_id').value;
        const noteValue = document.getElementById('note').value.trim();
        if (!customerId && !noteValue) {
            e.preventDefault();
            alert('!');
            return;
        }
    });
});

// Başarı modal'ını kapat
function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
}
</script>
@endsection 