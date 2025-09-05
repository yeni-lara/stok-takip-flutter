import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:permission_handler/permission_handler.dart';
import '../config/app_colors.dart';
import '../services/api_service.dart';
import 'qr_scanner_screen.dart';

class StockExitScreen extends StatefulWidget {
  const StockExitScreen({super.key});

  @override
  State<StockExitScreen> createState() => _StockExitScreenState();
}

class _StockExitScreenState extends State<StockExitScreen> {
  final _formKey = GlobalKey<FormState>();
  final _productCodeController = TextEditingController();
  final _quantityController = TextEditingController();
  final _notesController = TextEditingController();
  
  String? selectedCustomerId;
  String? selectedCustomerName;
  bool _isLoading = false;
  bool _showProductInfo = false;
  
  // API'den gelen müşteri listesi
  List<Map<String, dynamic>> customers = [];
  
  // QR scan sonrası ürün bilgileri
  Map<String, dynamic>? productInfo;

  @override
  void initState() {
    super.initState();
    _loadCustomers();
  }

  @override
  void dispose() {
    _productCodeController.dispose();
    _quantityController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  // Müşteri listesini API'den yükle
  Future<void> _loadCustomers() async {
    try {
      final customerList = await ApiService.getCustomers();
      setState(() {
        customers = customerList;
      });
    } catch (e) {
      print('❌ Müşteri listesi yüklenemedi: $e');
    }
  }

  // Kamera izni kontrol et ve QR tarayıcıyı aç
  Future<void> _scanQRCode() async {
    // Kamera izni kontrol et
    final status = await Permission.camera.request();
    if (status != PermissionStatus.granted) {
      _showError('Kamera izni gerekli!');
      return;
    }

    try {
      // QR Scanner'ı aç
      final result = await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => const QRScannerScreen(),
        ),
      );

      print('🔍 QR Scanner sonucu: $result');

      if (result != null && result is String && result.isNotEmpty) {
        // QR kod başarıyla tarandı
        print('✅ QR kod alındı: $result');
        await _loadProductInfo(result);
      } else {
        print('❌ QR kod alınamadı veya boş');
      }
    } catch (e) {
      print('❌ QR Scanner hatası: $e');
      _showError('QR tarama sırasında hata oluştu!');
    }
  }

  // Ürün bilgilerini API'den yükle
  Future<void> _loadProductInfo(String barcode) async {
    if (barcode.isEmpty) {
      _showError('Geçersiz barkod!');
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      print('📡 Ürün bilgileri yükleniyor: $barcode');
      final product = await ApiService.getProductByBarcode(barcode);
      
      if (product != null) {
        print('✅ Ürün bilgileri yüklendi: ${product['name']}');
        setState(() {
          productInfo = product;
          _productCodeController.text = barcode;
          _showProductInfo = true;
          _isLoading = false;
        });
        
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Ürün bilgileri yüklendi: ${product['name']}'),
            backgroundColor: AppColors.successGreen,
            duration: const Duration(seconds: 2),
          ),
        );
      } else {
        print('❌ Ürün bulunamadı: $barcode');
        setState(() {
          _isLoading = false;
        });
        _showError('Bu barkod ile ürün bulunamadı!');
      }
    } catch (e) {
      print('❌ Ürün bilgileri yüklenirken hata: $e');
      setState(() {
        _isLoading = false;
      });
      _showError('Ürün bilgileri yüklenirken hata oluştu: $e');
    }
  }

  Future<void> _submitStockExit(String productCode, int quantity, String? customerId, String notes) async {
    // Android: currentProductId gerekli
    if (productInfo == null || productInfo!['id'] == null) {
      _showError('Ürün bilgileri eksik! Lütfen QR kodu tekrar tarayın.');
      return;
    }

    final productId = productInfo!['id'] as int;

    setState(() {
      _isLoading = true;
    });

    try {
      print('📡 Stok çıkış işlemi başlatılıyor...');
      print('📡 Ürün ID: $productId, Miktar: $quantity, Müşteri ID: $customerId');
      
      // Android: product_id kullanılıyor
      final result = await ApiService.stockExit(
        productId: productId, // Android: product_id
        quantity: quantity,
        customerId: customerId != null ? int.tryParse(customerId) : null,
        notes: notes.isEmpty ? null : notes,
      );

      setState(() {
        _isLoading = false;
      });

      print('📡 API Sonucu: $result');
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'İşlem tamamlandı'),
            backgroundColor: result['success'] ? AppColors.successGreen : AppColors.dangerRed,
            duration: const Duration(seconds: 3),
          ),
        );
        
        if (result['success']) {
          // Android: Başarılı işlem sonrası ana sayfaya dön
          Navigator.pop(context);
        }
      }
    } catch (e) {
      print('❌ Stok çıkış hatası: $e');
      setState(() {
        _isLoading = false;
      });
      if (mounted) {
        _showError('İşlem sırasında hata oluştu: $e');
      }
    }
  }

  void _validateAndSubmit() {
    if (!_formKey.currentState!.validate()) return;

    final productCode = _productCodeController.text.trim();
    final quantity = _quantityController.text.trim();
    final customerId = selectedCustomerId;
    final notes = _notesController.text.trim();

    // Android validasyon kuralları
    if (productCode.isEmpty) {
      _showError('Ürün kodu gerekli!');
      return;
    }

    final quantityInt = int.tryParse(quantity);
    if (quantity.isEmpty || quantityInt == null || quantityInt < 1) {
      _showError('Geçerli miktar giriniz (min: 1)!');
      return;
    }

    // Android: Müşteri veya açıklama kontrolü
    if (customerId == null && notes.isEmpty) {
      _showError('Müşteri veya açıklama gerekli!');
      return;
    }

    // Stok kontrolü
    if (productInfo != null) {
      final currentStock = productInfo!['current_stock'] ?? 0;
      if (quantityInt > currentStock) {
        _showError('Yetersiz stok! Mevcut stok: $currentStock adet');
        return;
      }
    }

    // Android: Ürün ID kontrolü
    if (productInfo == null || productInfo!['id'] == null) {
      _showError('Ürün bilgileri eksik! Lütfen QR kodu tekrar tarayın.');
      return;
    }

    _showConfirmationDialog(productCode, quantityInt, customerId, notes);
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.dangerRed,
      ),
    );
  }

  void _showConfirmationDialog(String productCode, int quantity, String? customerId, String notes) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          backgroundColor: AppColors.mediumGray,
          title: const Text(
            'Stok Çıkış Onayı',
            style: TextStyle(color: AppColors.white),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (productInfo != null) 
                Text('Ürün: ${productInfo!['name'] ?? 'Bilinmiyor'}', style: const TextStyle(color: AppColors.lightGray)),
              Text('Barkod: $productCode', style: const TextStyle(color: AppColors.lightGray)),
              Text('Miktar: $quantity', style: const TextStyle(color: AppColors.lightGray)),
              Text('Müşteri: ${selectedCustomerName ?? "Belirtilmedi"}', style: const TextStyle(color: AppColors.lightGray)),
              if (notes.isNotEmpty) Text('Açıklama: $notes', style: const TextStyle(color: AppColors.lightGray)),
            ],
          ),
          actions: [
            TextButton(
              child: const Text('İptal', style: TextStyle(color: AppColors.lightGray)),
              onPressed: () => Navigator.of(context).pop(),
            ),
            TextButton(
              child: const Text('Onayla', style: TextStyle(color: AppColors.successGreen)),
              onPressed: () {
                Navigator.of(context).pop();
                _submitStockExit(productCode, quantity, customerId, notes);
              },
            ),
          ],
        );
      },
    );
  }

  void _clearForm() {
    _productCodeController.clear();
    _quantityController.clear();
    _notesController.clear();
    setState(() {
      selectedCustomerId = null;
      selectedCustomerName = null;
      _showProductInfo = false;
      productInfo = null;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Android: background="@color/black"
      backgroundColor: AppColors.black,
      appBar: AppBar(
        // Android: title = "Stok Çıkış"
        title: const Text('Stok Çıkış'),
        backgroundColor: AppColors.black,
        foregroundColor: AppColors.white,
        centerTitle: true,
        elevation: 0,
      ),
      body: SizedBox(
        width: double.infinity,
        height: double.infinity,
        // Android: android:padding="24dp"
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: SingleChildScrollView(
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // QR Scan Button - Android'deki gibi
                  SizedBox(
                    width: double.infinity,
                    height: 60,
                    child: Container(
                      margin: const EdgeInsets.only(bottom: 24),
                      child: ElevatedButton.icon(
                        onPressed: _isLoading ? null : _scanQRCode,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.successGreen,
                          foregroundColor: AppColors.white,
                          shape: const RoundedRectangleBorder(
                            borderRadius: BorderRadius.all(Radius.circular(8)),
                          ),
                          elevation: 0,
                        ),
                        icon: const Icon(Icons.qr_code_scanner, size: 24),
                        label: const Text(
                          '📱 QR Kod Tara',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ),

                  // Ürün bilgi kartı (QR scan sonrası gösterilecek)
                  if (_showProductInfo && productInfo != null) ...[
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      margin: const EdgeInsets.only(bottom: 24),
                      decoration: const BoxDecoration(
                        color: AppColors.mediumGray,
                        borderRadius: BorderRadius.all(Radius.circular(8)),
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Ürün resmi - Android: image_path ile
                          Container(
                            width: 80,
                            height: 80,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              color: AppColors.darkGray,
                            ),
                            child: productInfo!['image'] != null
                                ? ClipRRect(
                                    borderRadius: BorderRadius.circular(8),
                                    child: CachedNetworkImage(
                                      imageUrl: productInfo!['image'],
                                      fit: BoxFit.cover,
                                      placeholder: (context, url) => const Center(
                                        child: CircularProgressIndicator(
                                          color: AppColors.primaryBlue,
                                          strokeWidth: 2,
                                        ),
                                      ),
                                      errorWidget: (context, url, error) => const Icon(
                                        Icons.inventory_2,
                                        color: AppColors.lightGray,
                                        size: 32,
                                      ),
                                    ),
                                  )
                                : const Icon(
                                    Icons.inventory_2,
                                    color: AppColors.lightGray,
                                    size: 32,
                                  ),
                          ),
                          const SizedBox(width: 16),
                          // Ürün bilgileri - Android örneklerine göre
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  productInfo!['name'] ?? 'Bilinmeyen Ürün',
                                  style: const TextStyle(
                                    color: AppColors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                if (productInfo!['category'] != null)
                                  Text(
                                    'Kategori: ${productInfo!['category']}',
                                    style: const TextStyle(color: AppColors.lightGray, fontSize: 12),
                                  ),
                                if (productInfo!['barcode'] != null)
                                  Text(
                                    'Barkod: ${productInfo!['barcode']}',
                                    style: const TextStyle(color: AppColors.lightGray, fontSize: 12),
                                  ),
                                const SizedBox(height: 8),
                                // Android: current_stock ile stok badge
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    // Android: Düşük stok uyarısı (<=5)
                                    color: (productInfo!['current_stock'] ?? 0) <= 5 
                                        ? AppColors.dangerRed 
                                        : AppColors.successGreen,
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: Text(
                                    'Stok: ${productInfo!['current_stock'] ?? 0} adet',
                                    style: const TextStyle(
                                      color: AppColors.white,
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                                // Android: Düşük stok uyarısı mesajı
                                if ((productInfo!['current_stock'] ?? 0) <= 5) ...[
                                  const SizedBox(height: 4),
                                  Text(
                                    'Düşük stok uyarısı! Mevcut stok: ${productInfo!['current_stock'] ?? 0}',
                                    style: const TextStyle(
                                      color: AppColors.dangerRed,
                                      fontSize: 11,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],

                  // Form Başlığı - Android'deki gibi
                  const Text(
                    'Çıkış Bilgileri',
                    style: TextStyle(
                      color: AppColors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Android: Barkod Numarası (disabled)
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _productCodeController,
                      enabled: false,
                      style: const TextStyle(
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: const InputDecoration(
                        hintText: 'Barkod Numarası',
                        hintStyle: TextStyle(color: AppColors.lightGray),
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        contentPadding: EdgeInsets.all(16),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Android: Miktar
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _quantityController,
                      style: const TextStyle(
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: const InputDecoration(
                        hintText: 'Miktar',
                        hintStyle: TextStyle(color: AppColors.lightGray),
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        contentPadding: EdgeInsets.all(16),
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Miktar gerekli';
                        }
                        final quantity = int.tryParse(value);
                        if (quantity == null || quantity < 1) {
                          return 'Geçerli miktar giriniz (min: 1)';
                        }
                        return null;
                      },
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Android: Müşteri Spinner - API'den gelen liste
                  const Text(
                    'Müşteri (Opsiyonel)',
                    style: TextStyle(
                      color: AppColors.white,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    width: double.infinity,
                    height: 60,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    decoration: const BoxDecoration(
                      color: AppColors.mediumGray,
                      borderRadius: BorderRadius.all(Radius.circular(8)),
                    ),
                    child: DropdownButton<String>(
                      value: selectedCustomerId,
                      hint: const Text(
                        'Müşteri Seçiniz',
                        style: TextStyle(color: AppColors.lightGray),
                      ),
                      isExpanded: true,
                      underline: const SizedBox(),
                      dropdownColor: AppColors.mediumGray,
                      style: const TextStyle(color: AppColors.white),
                      items: [
                        // Android: "Müşteri Seçiniz" ilk seçenek
                        const DropdownMenuItem<String>(
                          value: null,
                          child: Text('Müşteri Seçiniz'),
                        ),
                        // API'den gelen müşteriler - Android: company_name
                        ...customers.map((customer) {
                          return DropdownMenuItem<String>(
                            value: customer['id'].toString(),
                            child: Text(customer['company_name'] ?? customer['name'] ?? 'Bilinmeyen Müşteri'),
                          );
                        }).toList(),
                      ],
                      onChanged: (String? newValue) {
                        setState(() {
                          selectedCustomerId = newValue;
                          if (newValue != null) {
                            // Seçilen müşterinin adını bul
                            final customer = customers.firstWhere(
                              (c) => c['id'].toString() == newValue,
                              orElse: () => {'company_name': null, 'name': null},
                            );
                            selectedCustomerName = customer['company_name'] ?? customer['name'];
                          } else {
                            selectedCustomerName = null;
                          }
                        });
                      },
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Android: Açıklama
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _notesController,
                      style: const TextStyle(
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: const InputDecoration(
                        hintText: 'Açıklama (Opsiyonel - Müşteri yoksa zorunlu)',
                        hintStyle: TextStyle(color: AppColors.lightGray),
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        contentPadding: EdgeInsets.all(16),
                      ),
                      maxLines: 3,
                      minLines: 3,
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Butonlar - Android'deki gibi
                  Row(
                    children: [
                      // Temizle butonu
                      Expanded(
                        child: SizedBox(
                          height: 60,
                          child: ElevatedButton(
                            onPressed: _clearForm,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.warningOrange,
                              foregroundColor: AppColors.white,
                              shape: const RoundedRectangleBorder(
                                borderRadius: BorderRadius.all(Radius.circular(8)),
                              ),
                              elevation: 0,
                            ),
                            child: const Text(
                              '🧹 Temizle',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Gönder butonu
                      Expanded(
                        child: SizedBox(
                          height: 60,
                          child: ElevatedButton(
                            onPressed: _isLoading ? null : _validateAndSubmit,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.successGreen,
                              foregroundColor: AppColors.white,
                              shape: const RoundedRectangleBorder(
                                borderRadius: BorderRadius.all(Radius.circular(8)),
                              ),
                              elevation: 0,
                            ),
                            child: _isLoading
                                ? const SizedBox(
                                    width: 24,
                                    height: 24,
                                    child: CircularProgressIndicator(
                                      color: AppColors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Text(
                                    '✅ Gönder',
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
} 