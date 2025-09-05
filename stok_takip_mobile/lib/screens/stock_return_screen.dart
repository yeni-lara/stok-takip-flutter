import 'package:flutter/material.dart';
import '../config/app_colors.dart';

class StockReturnScreen extends StatefulWidget {
  const StockReturnScreen({super.key});

  @override
  State<StockReturnScreen> createState() => _StockReturnScreenState();
}

class _StockReturnScreenState extends State<StockReturnScreen> {
  final _formKey = GlobalKey<FormState>();
  final _productCodeController = TextEditingController();
  final _quantityController = TextEditingController();
  final _reasonController = TextEditingController();
  
  String? selectedReason;
  bool _isLoading = false;
  bool _showProductInfo = false;
  
  // İade sebepleri (Android'deki gibi)
  List<String> returnReasons = [
    'İade sebebi seçiniz...',
    'Hasarlı ürün',
    'Yanlış ürün',
    'Müşteri iadesi',
    'Kalite problemi',
    'Diğer'
  ];

  @override
  void dispose() {
    _productCodeController.dispose();
    _quantityController.dispose();
    _reasonController.dispose();
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
    final reason = selectedReason;
    final customReason = _reasonController.text.trim();

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

    // İade sebebi kontrolü
    if (reason == null || reason == 'İade sebebi seçiniz...') {
      _showError('İade sebebi seçiniz!');
      return;
    }

    // "Diğer" seçilmişse açıklama zorunlu
    if (reason == 'Diğer' && customReason.isEmpty) {
      _showError('Diğer sebepler için açıklama gerekli!');
      return;
    }

    _showConfirmationDialog(productCode, quantity, reason, customReason);
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.dangerRed,
      ),
    );
  }

  void _showConfirmationDialog(String productCode, String quantity, String reason, String customReason) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          backgroundColor: AppColors.mediumGray,
          title: const Text(
            'Stok İade Onayı',
            style: TextStyle(color: AppColors.white),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Ürün Kodu: $productCode', style: const TextStyle(color: AppColors.lightGray)),
              Text('Miktar: $quantity', style: const TextStyle(color: AppColors.lightGray)),
              Text('İade Sebebi: $reason', style: const TextStyle(color: AppColors.lightGray)),
              if (customReason.isNotEmpty) Text('Açıklama: $customReason', style: const TextStyle(color: AppColors.lightGray)),
            ],
          ),
          actions: [
            TextButton(
              child: const Text('İptal', style: TextStyle(color: AppColors.lightGray)),
              onPressed: () => Navigator.of(context).pop(),
            ),
            TextButton(
              child: const Text('Onayla', style: TextStyle(color: AppColors.warningOrange)),
              onPressed: () {
                Navigator.of(context).pop();
                _submitStockReturn();
              },
            ),
          ],
        );
      },
    );
  }

  void _submitStockReturn() {
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
          content: Text('Stok iade işlemi başarıyla tamamlandı!'),
          backgroundColor: AppColors.warningOrange,
        ),
      );
      
      _clearForm();
    });
  }

  void _clearForm() {
    _productCodeController.clear();
    _quantityController.clear();
    _reasonController.clear();
    setState(() {
      selectedReason = null;
      _showProductInfo = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Android: background="@color/black"
      backgroundColor: AppColors.black,
      appBar: AppBar(
        // Android: title = "Stok İade"
        title: const Text('Stok İade'),
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
                          backgroundColor: AppColors.warningOrange,
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
                            'Satış Miktarı: 10 adet',
                            style: TextStyle(color: AppColors.lightGray),
                          ),
                        ],
                      ),
                    ),
                  ],

                  // Form Başlığı - Android'deki gibi
                  const Text(
                    'İade Bilgileri',
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
                        hintText: 'İade Miktarı',
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

                  // Android: İade Sebebi Spinner
                  const Text(
                    'İade Sebebi',
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
                      value: selectedReason,
                      hint: const Text(
                        'İade sebebi seçiniz...',
                        style: TextStyle(color: AppColors.lightGray),
                      ),
                      isExpanded: true,
                      underline: const SizedBox(),
                      dropdownColor: AppColors.mediumGray,
                      style: const TextStyle(color: AppColors.white),
                      items: returnReasons.map((String reason) {
                        return DropdownMenuItem<String>(
                          value: reason,
                          child: Text(reason),
                        );
                      }).toList(),
                      onChanged: (String? newValue) {
                        setState(() {
                          selectedReason = newValue;
                        });
                      },
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Android: Açıklama (Diğer seçilince zorunlu)
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _reasonController,
                      style: const TextStyle(
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: InputDecoration(
                        hintText: selectedReason == 'Diğer' 
                            ? 'Açıklama (Zorunlu)' 
                            : 'Ek Açıklama (Opsiyonel)',
                        hintStyle: const TextStyle(color: AppColors.lightGray),
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: const OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        contentPadding: const EdgeInsets.all(16),
                      ),
                      maxLines: 3,
                      minLines: 3,
                      validator: selectedReason == 'Diğer' 
                          ? (value) {
                              if (value == null || value.trim().isEmpty) {
                                return 'Diğer sebepler için açıklama gerekli';
                              }
                              return null;
                            }
                          : null,
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
                              backgroundColor: AppColors.dangerRed,
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
                      // İade butonu
                      Expanded(
                        child: SizedBox(
                          height: 60,
                          child: ElevatedButton(
                            onPressed: _isLoading ? null : _validateAndSubmit,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.warningOrange,
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
                                    '🔄 İade Et',
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