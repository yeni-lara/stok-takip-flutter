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
                                    <div id="barcode-scanner" style="max-width: 400px; margin: 0 auto;">
                                        <div id="video" style="width: 400px; height: 400px; border: 2px dashed #ffc107; border-radius: 8px; background: #000; margin: 0 auto; overflow: hidden; position: relative;"></div>
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
                                <div id="product-info" class="form-control form-control-lg bg-light" style="min-height: 120px;">
                                    <div class="d-flex align-items-center">
                                        <div id="product-image" style="width: 80px; height: 80px; margin-right: 15px; display: none;">
                                            <img id="product-img" src="" alt="Ürün Resmi" 
                                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                        </div>
                                        <div id="product-details" class="flex-grow-1">
                                            <span class="text-muted">Barkod okutarak ürün seçin</span>
                                        </div>
                                    </div>
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

<!-- Html5Qrcode for QR and Barcode Scanning -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

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
    let html5QrCode = null;

    // Barkod değiştiğinde ürün bilgisini getir
    barcodeInput.addEventListener('input', function() {
        if (this.value.length >= 8) { // Minimum barkod uzunluğu
            fetchProductByBarcode(this.value);
        } else if (this.value.length === 0) {
            clearProductInfo();
        }
    });

    // Ürün bilgilerini temizle
    function clearProductInfo() {
        currentProduct = null;
        productIdInput.value = '';
        
        const productImageElement = document.getElementById('product-image');
        const productDetailsElement = document.getElementById('product-details');
        
        productImageElement.style.display = 'none';
        productDetailsElement.innerHTML = '<span class="text-muted">Barkod okutarak ürün seçin</span>';
        
        stockInfo.style.display = 'none';
        quantityInput.value = '';
        quantityInput.max = '';
    }

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
        
        // Ürün resmi için URL oluştur
        let productImage = '/images/no-image.svg';
        
        if (product.image_url) {
            productImage = product.image_url;
        } else if (product.image_path && product.image_path.trim() !== '') {
            productImage = product.image_path.startsWith('http') 
                ? product.image_path 
                : '/' + product.image_path.replace(/^\/+/, '');
        }
        
        console.log('Product image path:', product.image_path);
        console.log('Product image URL:', product.image_url);
        console.log('Final image URL:', productImage);
        
        const productImageElement = document.getElementById('product-image');
        const productImgElement = document.getElementById('product-img');
        const productDetailsElement = document.getElementById('product-details');
        
        // Resmi güncelle
        productImgElement.src = productImage;
        productImgElement.onerror = function() {
            this.src = '/images/no-image.svg';
        };
        productImageElement.style.display = 'block';
        
        // Ürün detaylarını güncelle
        productDetailsElement.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong style="font-size: 16px;">${product.name}</strong><br>
                    <small class="text-muted">${product.category || 'Kategori yok'}</small><br>
                    <small class="text-info">Barkod: ${product.barcode || 'Yok'}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-6">${product.current_stock} adet</span>
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

    // QR/Barkod tarayıcı başlat
    function startBarcodeScanner() {
        // Html5Qrcode desteği kontrolü
        if (!Html5Qrcode.getCameras) {
            alert('Tarayıcınız QR/barkod taramayı desteklemiyor!');
            cameraStatus.innerHTML = '<span class="text-danger">❌ Tarayıcı desteklenmiyor</span>';
            return;
        }

        cameraStatus.innerHTML = '<span class="text-info">📷 Kamera başlatılıyor...</span>';

        // Video div'inin var olduğunu kontrol et
        const videoElement = document.getElementById('video');
        if (!videoElement) {
            alert('Video elementi bulunamadı!');
            cameraStatus.innerHTML = '<span class="text-danger">❌ Video elementi bulunamadı</span>';
            return;
        }

        // Html5Qrcode instance oluştur
        html5QrCode = new Html5Qrcode("video");

        const config = {
            fps: 20,
            qrbox: { width: 300, height: 300 },
            aspectRatio: 1.0,
            supportedScanTypes: [
                Html5QrcodeScanType.SCAN_TYPE_CAMERA
            ],
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        };

        // Kamera listesini al ve başlat
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                console.log('Bulunan kameralar:', devices);
                
                // Arka kamerayı tercih et
                let cameraId = devices[0].id;
                
                // Arka kamera ara (environment facing)
                for (let device of devices) {
                    if (device.label && 
                        (device.label.toLowerCase().includes('back') || 
                         device.label.toLowerCase().includes('environment') ||
                         device.label.toLowerCase().includes('rear'))) {
                        cameraId = device.id;
                        console.log('Arka kamera seçildi:', device.label);
                        break;
                    }
                }

                // Kamera kısıtları
                const cameraConfig = {
                    ...config,
                    videoConstraints: {
                        facingMode: "environment",
                        width: { ideal: 400 },
                        height: { ideal: 400 }
                    }
                };

                // Taramayı başlat
                html5QrCode.start(
                    cameraId,
                    cameraConfig,
                    (decodedText, decodedResult) => {
                        // Başarılı tarama - QR kod veya barkod
                        console.log('QR/Barkod tespit edildi:', decodedText);
                        
                        if (decodedText && decodedText.trim().length >= 3) {
                            barcodeInput.value = decodedText.trim();
                            fetchProductByBarcode(decodedText.trim());
                            stopBarcodeScanner();
                        }
                    },
                    (error) => {
                        // Tarama hatası (normal, sürekli çalışır)
                        // console.log('Scan error:', error);
                    }
                ).then(() => {
                    isScanning = true;
                    startScanBtn.style.display = 'none';
                    stopScanBtn.style.display = 'inline-block';
                    cameraStatus.innerHTML = '<span class="text-success">✅ Tarama aktif - QR/Barkodu okutun</span>';
                    console.log('QR/Barkod tarayıcı başarıyla başlatıldı');
                }).catch(err => {
                    console.error('Kamera başlatma hatası:', err);
                    console.error('Hata detayları:', {
                        name: err.name,
                        message: err.message,
                        stack: err.stack
                    });
                    
                    let errorMessage = 'Kamera erişimi başarısız: ';
                    
                    if (err.name === 'NotAllowedError') {
                        errorMessage += 'Kamera izni verilmedi. Tarayıcı ayarlarından kamera iznini etkinleştirin.';
                    } else if (err.name === 'NotFoundError') {
                        errorMessage += 'Kamera bulunamadı.';
                    } else if (err.name === 'NotReadableError') {
                        errorMessage += 'Kamera başka bir uygulama tarafından kullanılıyor.';
                    } else {
                        errorMessage += err.message;
                    }
                    
                    alert(errorMessage);
                    cameraStatus.innerHTML = '<span class="text-danger">❌ ' + errorMessage + '</span>';
                });
            } else {
                alert('Hiç kamera bulunamadı!');
                cameraStatus.innerHTML = '<span class="text-danger">❌ Kamera bulunamadı</span>';
            }
        }).catch(err => {
            console.error('Kamera listesi alınamadı:', err);
            alert('Kamera listesi alınamadı: ' + err.message);
            cameraStatus.innerHTML = '<span class="text-danger">❌ Kamera hatası</span>';
        });
    }

    // QR/Barkod tarayıcı durdur
    function stopBarcodeScanner() {
        if (isScanning && html5QrCode) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                startScanBtn.style.display = 'inline-block';
                stopScanBtn.style.display = 'none';
                startScanBtn.textContent = 'Kamerayı Başlat';
                cameraStatus.innerHTML = '<span class="text-secondary">⏹️ Tarama durduruldu</span>';
                console.log('QR/Barkod tarayıcı durduruldu');
            }).catch(err => {
                console.error('Scanner durdurma hatası:', err);
            });
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