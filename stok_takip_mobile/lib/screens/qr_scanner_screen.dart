import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../config/app_colors.dart';

class QRScannerScreen extends StatefulWidget {
  const QRScannerScreen({super.key});

  @override
  State<QRScannerScreen> createState() => _QRScannerScreenState();
}

class _QRScannerScreenState extends State<QRScannerScreen> {
  MobileScannerController cameraController = MobileScannerController();
  bool _isFlashOn = false;
  bool _isFrontCamera = false;
  bool _isProcessing = false; // QR kod işlenirken tekrar işlemeyi engelle

  @override
  void dispose() {
    cameraController.dispose();
    super.dispose();
  }

  void _toggleFlash() {
    setState(() {
      _isFlashOn = !_isFlashOn;
    });
    cameraController.toggleTorch();
  }

  void _switchCamera() {
    setState(() {
      _isFrontCamera = !_isFrontCamera;
    });
    cameraController.switchCamera();
  }

  void _onDetect(BarcodeCapture capture) {
    // Zaten işleniyorsa tekrar işleme
    if (_isProcessing) return;
    
    final List<Barcode> barcodes = capture.barcodes;
    if (barcodes.isNotEmpty) {
      final String code = barcodes.first.rawValue ?? '';
      if (code.isNotEmpty) {
        setState(() {
          _isProcessing = true;
        });
        
        print('🔍 QR Kod bulundu: $code');
        
        // QR kod bulundu, geri dön ve sadece 1 kez çağır
        Future.delayed(const Duration(milliseconds: 500), () {
          if (mounted) {
            Navigator.pop(context, code);
          }
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.black,
      appBar: AppBar(
        title: const Text('QR Kod Tara'),
        backgroundColor: AppColors.black,
        foregroundColor: AppColors.white,
        centerTitle: true,
        elevation: 0,
        actions: [
          // Flash toggle
          IconButton(
            onPressed: _isProcessing ? null : _toggleFlash,
            icon: Icon(
              _isFlashOn ? Icons.flash_on : Icons.flash_off,
              color: _isFlashOn ? AppColors.warningOrange : AppColors.white,
            ),
          ),
          // Camera switch
          IconButton(
            onPressed: _isProcessing ? null : _switchCamera,
            icon: const Icon(
              Icons.flip_camera_ios,
              color: AppColors.white,
            ),
          ),
        ],
      ),
      body: Column(
        children: [
          // Kamera görüntüsü
          Expanded(
            flex: 4,
            child: Container(
              width: double.infinity,
              margin: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                  color: _isProcessing ? AppColors.successGreen : AppColors.primaryBlue, 
                  width: 2
                ),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(14),
                child: Stack(
                  children: [
                    MobileScanner(
                      controller: cameraController,
                      onDetect: _onDetect,
                    ),
                    if (_isProcessing)
                      Container(
                        color: Colors.black54,
                        child: const Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              CircularProgressIndicator(
                                color: AppColors.successGreen,
                              ),
                              SizedBox(height: 16),
                              Text(
                                'QR Kod İşleniyor...',
                                style: TextStyle(
                                  color: AppColors.white,
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ),

          // Bilgi alanı
          Expanded(
            flex: 1,
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    _isProcessing ? Icons.check_circle : Icons.qr_code_scanner,
                    size: 48,
                    color: _isProcessing ? AppColors.successGreen : AppColors.primaryBlue,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    _isProcessing 
                        ? 'QR kod başarıyla okundu!'
                        : 'QR kodu kamera görüntüsü içine yerleştirin',
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      color: AppColors.white,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _isProcessing 
                        ? 'Ürün bilgileri yükleniyor...'
                        : 'QR kod otomatik olarak taranacak',
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      color: AppColors.lightGray,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
} 