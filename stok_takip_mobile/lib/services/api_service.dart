import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import 'auth_service.dart';

class ApiService {
  // Müşteri listesini getir - Android: company_name kullanılıyor
  static Future<List<Map<String, dynamic>>> getCustomers() async {
    try {
      final token = await AuthService.getAuthToken();
      if (token == null) {
        throw Exception('Token bulunamadı');
      }

      final response = await http.get(
        Uri.parse(AppConfig.apiCustomers),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print('📡 Customers API Response Status: ${response.statusCode}');
      print('📡 Customers API Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // Android: {"success": true, "customers": [...]}
        if (data['success'] == true && data['customers'] != null) {
          final List<Map<String, dynamic>> customers = [];
          final customersArray = data['customers'] as List;
          
          for (var customer in customersArray) {
            customers.add({
              'id': customer['id'],
              // Android: company_name kullanılıyor
              'name': customer['company_name'] ?? 'Bilinmeyen Müşteri',
              'company_name': customer['company_name'],
              'email': customer['email'],
              'phone': customer['phone'],
            });
          }
          
          return customers;
        } else {
          return [];
        }
      } else {
        throw Exception('Müşteri listesi alınamadı: ${response.statusCode}');
      }
    } catch (e) {
      print('❌ Müşteri listesi hatası: $e');
      return [];
    }
  }

  // Ürün bilgilerini barkod ile getir - Android: image_path, current_stock
  static Future<Map<String, dynamic>?> getProductByBarcode(String barcode) async {
    try {
      final token = await AuthService.getAuthToken();
      if (token == null) {
        throw Exception('Token bulunamadı');
      }

      final response = await http.get(
        Uri.parse('${AppConfig.apiProductByBarcode}/$barcode'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print('📡 Product API Response Status: ${response.statusCode}');
      print('📡 Product API Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // Android: {"success": true, "product": {...}}
        if (data['success'] == true && data['product'] != null) {
          final product = data['product'];
          
          // Android örneklerine göre düzenleme
          return {
            'id': product['id'],
            'name': product['name'],
            'category': product['category'],
            'barcode': product['barcode'],
            // Android: current_stock kullanılıyor
            'stock': product['current_stock'] ?? 0,
            'current_stock': product['current_stock'] ?? 0,
            // Android: image_path kullanılıyor ve BASE_URL ile birleştiriliyor
            'image_path': product['image_path'],
            'image': product['image_path'] != null && product['image_path'].toString().isNotEmpty
                ? '${AppConfig.baseUrl}/${product['image_path']}'
                : null,
            'price': product['price'],
            'sales_count': product['sales_count'] ?? 0,
          };
        } else {
          return null;
        }
      } else if (response.statusCode == 404) {
        return null; // Ürün bulunamadı
      } else {
        throw Exception('Ürün bilgisi alınamadı: ${response.statusCode}');
      }
    } catch (e) {
      print('❌ Ürün bilgisi hatası: $e');
      return null;
    }
  }

  // Stok çıkış işlemi - Android: product_id, note kullanıyor
  static Future<Map<String, dynamic>> stockExit({
    required int productId, // Android: product_id kullanıyor
    required int quantity,
    int? customerId,
    String? notes,
  }) async {
    try {
      final token = await AuthService.getAuthToken();
      if (token == null) {
        throw Exception('Token bulunamadı');
      }

      // Android request body formatı
      final body = {
        'product_id': productId, // Android: product_id
        'quantity': quantity,
        if (customerId != null) 'customer_id': customerId,
        if (notes != null && notes.isNotEmpty) 'note': notes, // Android: note
      };

      print('📡 Stock Exit API Request:');
      print('📡 URL: ${AppConfig.apiStockExit}');
      print('📡 Headers: Authorization: Bearer $token');
      print('📡 Body: ${json.encode(body)}');

      final response = await http.post(
        Uri.parse(AppConfig.apiStockExit),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: json.encode(body),
      );

      print('📡 Stock Exit API Response Status: ${response.statusCode}');
      print('📡 Stock Exit API Response Body: ${response.body}');

      if (response.body.isEmpty) {
        return {
          'success': false,
          'message': 'Sunucudan boş yanıt alındı',
        };
      }

      final data = json.decode(response.body);
      
      if (response.statusCode == 200) {
        return {
          'success': data['success'] ?? true,
          'message': data['message'] ?? 'Stok çıkış işlemi başarılı',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Stok çıkış işlemi başarısız (${response.statusCode})',
        };
      }
    } catch (e) {
      print('❌ Stok çıkış hatası: $e');
      return {
        'success': false,
        'message': 'Bağlantı hatası: $e',
      };
    }
  }

  // Stok iade işlemi - Android: product_id, note kullanıyor
  static Future<Map<String, dynamic>> stockReturn({
    required int productId, // Android: product_id kullanıyor
    required int quantity,
    int? customerId,
    String? notes,
  }) async {
    try {
      final token = await AuthService.getAuthToken();
      if (token == null) {
        throw Exception('Token bulunamadı');
      }

      // Android request body formatı
      final body = {
        'product_id': productId, // Android: product_id
        'quantity': quantity,
        if (customerId != null) 'customer_id': customerId,
        if (notes != null && notes.isNotEmpty) 'note': notes, // Android: note
      };

      print('📡 Stock Return API Request:');
      print('📡 URL: ${AppConfig.apiStockReturn}');
      print('📡 Headers: Authorization: Bearer $token');
      print('📡 Body: ${json.encode(body)}');

      final response = await http.post(
        Uri.parse(AppConfig.apiStockReturn),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: json.encode(body),
      );

      print('📡 Stock Return API Response Status: ${response.statusCode}');
      print('📡 Stock Return API Response Body: ${response.body}');

      if (response.body.isEmpty) {
        return {
          'success': false,
          'message': 'Sunucudan boş yanıt alındı',
        };
      }

      final data = json.decode(response.body);
      
      if (response.statusCode == 200) {
        return {
          'success': data['success'] ?? true,
          'message': data['message'] ?? 'Stok iade işlemi başarılı',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Stok iade işlemi başarısız (${response.statusCode})',
        };
      }
    } catch (e) {
      print('❌ Stok iade hatası: $e');
      return {
        'success': false,
        'message': 'Bağlantı hatası: $e',
      };
    }
  }
} 