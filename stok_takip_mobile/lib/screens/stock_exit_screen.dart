import 'package:flutter/material.dart';
import '../config/app_colors.dart';

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
  
  String? selectedCustomer;
  bool _isLoading = false;
  bool _showProductInfo = false;
  
  // Müşteri listesi (API'den gelecek)
  List<String> customers = ['Müşteri seçiniz...', 'Ahmet Yılmaz', 'Mehmet Öz', 'Ayşe Demir'];
  
  // Ürün bilgileri (QR scan sonrası gösterilecek)
  Map<String, dynamic> productInfo = {};

  @override
  void dispose() {
    _productCodeController.dispose();
    _quantityController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _scanQRCode() {
    // QR Scanner açılacak
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('QR Tarayıcı yakında aktif olacak!'),
        backgroundColor: AppColors.warningOrange,
      ),
    );
  }

  void _validateAndSubmit() {
    if (!_formKey.currentState!.validate()) return;

    final productCode = _productCodeController.text.trim();
    final quantity = _quantityController.text.trim();
    final customer = selectedCustomer;
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
    if ((customer == null || customer == 'Müşteri seçiniz...') && notes.isEmpty) {
      _showError('Müşteri veya açıklama gerekli!');
      return;
    }

    _showConfirmationDialog(productCode, quantity, customer, notes);
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.dangerRed,
      ),
    );
  }

  void _showConfirmationDialog(String productCode, String quantity, String? customer, String notes) {
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
              Text('Ürün Kodu: $productCode', style: const TextStyle(color: AppColors.lightGray)),
              Text('Miktar: $quantity', style: const TextStyle(color: AppColors.lightGray)),
              Text('Müşteri: ${customer ?? "Belirtilmedi"}', style: const TextStyle(color: AppColors.lightGray)),
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
                _submitStockExit();
              },
            ),
          ],
        );
      },
    );
  }

  void _submitStockExit() {
    setState(() {
      _isLoading = true;
    });

    // API çağrısı simülasyonu
    Future.delayed(const Duration(seconds: 2), () {
      setState(() {
        _isLoading = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Stok çıkış işlemi başarıyla tamamlandı!'),
          backgroundColor: AppColors.successGreen,
        ),
      );
      
      _clearForm();
    });
  }

  void _clearForm() {
    _productCodeController.clear();
    _quantityController.clear();
    _notesController.clear();
    setState(() {
      selectedCustomer = null;
      _showProductInfo = false;
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
                        onPressed: _scanQRCode,
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
                  if (_showProductInfo) ...[
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      margin: const EdgeInsets.only(bottom: 24),
                      decoration: const BoxDecoration(
                        color: AppColors.mediumGray,
                        borderRadius: BorderRadius.all(Radius.circular(8)),
                      ),
                      child: const Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Ürün Bilgileri',
                            style: TextStyle(
                              color: AppColors.white,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          SizedBox(height: 8),
                          Text(
                            'Ürün Adı: Örnek Ürün',
                            style: TextStyle(color: AppColors.lightGray),
                          ),
                          Text(
                            'Kategori: Örnek Kategori',
                            style: TextStyle(color: AppColors.lightGray),
                          ),
                          Text(
                            'Stok: 50 adet',
                            style: TextStyle(color: AppColors.lightGray),
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

                  // Android: Müşteri Spinner
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
                      value: selectedCustomer,
                      hint: const Text(
                        'Müşteri seçiniz...',
                        style: TextStyle(color: AppColors.lightGray),
                      ),
                      isExpanded: true,
                      underline: const SizedBox(),
                      dropdownColor: AppColors.mediumGray,
                      style: const TextStyle(color: AppColors.white),
                      items: customers.map((String customer) {
                        return DropdownMenuItem<String>(
                          value: customer,
                          child: Text(customer),
                        );
                      }).toList(),
                      onChanged: (String? newValue) {
                        setState(() {
                          selectedCustomer = newValue;
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