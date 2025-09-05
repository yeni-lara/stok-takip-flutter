import 'package:flutter/material.dart';

class StockReturnScreen extends StatefulWidget {
  @override
  _StockReturnScreenState createState() => _StockReturnScreenState();
}

class _StockReturnScreenState extends State<StockReturnScreen> {
  final _formKey = GlobalKey<FormState>();
  final _barcodeController = TextEditingController();
  final _quantityController = TextEditingController();
  final _reasonController = TextEditingController();

  @override
  void dispose() {
    _barcodeController.dispose();
    _quantityController.dispose();
    _reasonController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Stok İade'),
        backgroundColor: Colors.orange,
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
                  labelText: 'İade Miktarı',
                  prefixIcon: Icon(Icons.numbers),
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'İade miktarı gerekli';
                  }
                  return null;
                },
              ),
              SizedBox(height: 16),

              // İade sebebi
              TextFormField(
                controller: _reasonController,
                decoration: InputDecoration(
                  labelText: 'İade Sebebi',
                  prefixIcon: Icon(Icons.note),
                ),
                maxLines: 3,
              ),
              SizedBox(height: 24),

              // İade butonu
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: () {
                    if (_formKey.currentState!.validate()) {
                      // API çağrısı yapılacak
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Stok iade işlemi başarılı!'),
                          backgroundColor: Colors.green,
                        ),
                      );
                      Navigator.pop(context);
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.orange,
                  ),
                  child: Text('Stok İade Yap'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
} 