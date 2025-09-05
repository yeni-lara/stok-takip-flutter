import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class AuthService {
  // 🔐 Login
  static Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      print('🔑 Login denemesi: $username'); // Debug log
      
      final response = await http.post(
        Uri.parse(AppConfig.apiLogin),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          'email': username,
          'password': password,
        }),
      ).timeout(AppConfig.connectTimeout);

      print('📡 API Response Status: ${response.statusCode}'); // Debug log
      print('📡 API Response Body: ${response.body}'); // Debug log
      
      final data = json.decode(response.body);

      // Laravel API formatına göre kontrol et
      if (response.statusCode == 200 && data.containsKey('token')) {
        // Token'ı kaydet - Laravel formatında
        print('✅ Login başarılı, token kaydediliyor...'); // Debug log
        await _saveAuthData(data);
        return {'success': true, 'message': data['message'] ?? 'Giriş başarılı'};
      } else {
        print('❌ Login başarısız: ${data['message']}'); // Debug log
        return {'success': false, 'message': data['message'] ?? 'Giriş başarısız'};
      }
    } catch (e) {
      print('🚨 Login hatası: $e'); // Debug log
      return {'success': false, 'message': 'Bağlantı hatası: $e'};
    }
  }

  // 🚪 Logout
  static Future<bool> logout() async {
    try {
      final token = await getAuthToken();
      if (token != null) {
        await http.post(
          Uri.parse(AppConfig.apiLogout),
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $token',
          },
        ).timeout(AppConfig.connectTimeout);
      }
    } catch (e) {
      print('Logout API hatası: $e');
    }

    // Local verileri temizle
    await _clearAuthData();
    return true;
  }

  // 🔍 Login kontrolü
  static Future<bool> isUserLoggedIn() async {
    final token = await getAuthToken();
    return token != null && token.isNotEmpty;
  }

  // 🎫 Token al
  static Future<String?> getAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(AppConfig.keyAuthToken);
  }

  // 👤 Kullanıcı bilgilerini al
  static Future<Map<String, String?>> getUserData() async {
    final prefs = await SharedPreferences.getInstance();
    return {
      'id': prefs.getString(AppConfig.keyUserId),
      'name': prefs.getString(AppConfig.keyUserName),
      'email': prefs.getString(AppConfig.keyUserEmail),
    };
  }

  // 💾 Auth verilerini kaydet
  static Future<void> _saveAuthData(Map<String, dynamic> data) async {
    final prefs = await SharedPreferences.getInstance();
    print('💾 Kaydedilen data: $data'); // Debug log
    
    await prefs.setString(AppConfig.keyAuthToken, data['token']);
    await prefs.setString(AppConfig.keyUserId, data['user']['id'].toString());
    await prefs.setString(AppConfig.keyUserName, data['user']['name']);
    await prefs.setString(AppConfig.keyUserEmail, data['user']['email']);
    
    print('💾 Token kaydedildi: ${data['token']}'); // Debug log
  }

  // 🗑️ Auth verilerini temizle
  static Future<void> _clearAuthData() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConfig.keyAuthToken);
    await prefs.remove(AppConfig.keyUserId);
    await prefs.remove(AppConfig.keyUserName);
    await prefs.remove(AppConfig.keyUserEmail);
  }
} 