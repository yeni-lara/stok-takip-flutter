import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class AuthService {
  // 🔐 Login
  static Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final response = await http.post(
        Uri.parse(AppConfig.apiLogin),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          'email': username,
          'password': password,
        }),
      ).timeout(AppConfig.connectTimeout);

      final data = json.decode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        // Token'ı kaydet
        await _saveAuthData(data['data']);
        return {'success': true, 'message': 'Giriş başarılı'};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Giriş başarısız'};
      }
    } catch (e) {
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
    await prefs.setString(AppConfig.keyAuthToken, data['token']);
    await prefs.setString(AppConfig.keyUserId, data['user']['id'].toString());
    await prefs.setString(AppConfig.keyUserName, data['user']['name']);
    await prefs.setString(AppConfig.keyUserEmail, data['user']['email']);
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