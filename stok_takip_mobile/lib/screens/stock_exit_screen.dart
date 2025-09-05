import 'package:flutter/material.dart';

class StockExitScreen extends StatefulWidget {
  @override
  _StockExitScreenState createState() => _StockExitScreenState();
}

class _StockExitScreenState extends State<StockExitScreen> {
  final _formKey = GlobalKey<FormState>();
  final _barcodeController = TextEditingController();
  final _quantityController = TextEditingController();
  final _customerController = TextEditingController();

  @override
  void dispose() {
    _barcodeController.dispose();
    _quantityController.dispose();
    _customerController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Stok Çıkış'),
        backgroundColor: Colors.red,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              // Barkod girişi
              TextFormField(
                controller: _barcodeController,
                decoration: InputDecoration(
                  labelText: 'Barkod',
                  prefixIcon: Icon(Icons.qr_code),
                  suffixIcon: IconButton(
                    icon: Icon(Icons.qr_code_scanner),
                    onPressed: () {
                      // QR Scanner açılacak
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('QR Tarayıcı yakında!')),
                      );
                    },
                  ),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Barkod gerekli';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Miktar
              TextFormField(
                controller: _quantityController,
                decoration: InputDecoration(
                  labelText: 'Miktar',
                  prefixIcon: Icon(Icons.numbers),
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Miktar gerekli';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // Müşteri
              TextFormField(
                controller: _customerController,
                decoration: InputDecoration(
                  labelText: 'Müşteri',
                  prefixIcon: Icon(Icons.person),
                ),
              ),
              SizedBox(height: 24),

              // Kaydet butonu
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: () {
                    if (_formKey.currentState!.validate()) {
                      // API çağrısı yapılacak
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Stok çıkış işlemi başarılı!'),
                          backgroundColor: Colors.green,
                        ),
                      );
                      Navigator.pop(context);
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red,
                  ),
                  child: Text('Stok Çıkış Yap'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
} 