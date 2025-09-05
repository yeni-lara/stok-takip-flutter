class AppConfig {
  // 🌐 Base URL
  static const String baseUrl = "https://stok.sicakye.com";
  
  // 📡 API Endpoints
  static const String apiLogin = "$baseUrl/api/login";
  static const String apiLogout = "$baseUrl/api/logout";
  static const String apiUser = "$baseUrl/api/user";
  
  // 📦 Ürün API'leri
  static const String apiProductByBarcode = "$baseUrl/api/products/by-barcode";
  
  // 👥 Müşteri API'leri
  static const String apiCustomers = "$baseUrl/api/customers";
  
  // 📊 Stok API'leri
  static const String apiStockExit = "$baseUrl/api/stock/exit";
  static const String apiStockReturn = "$baseUrl/api/stock/return";
  
  // ⏱️ Timeout Ayarları
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration readTimeout = Duration(seconds: 30);
  
  // 🔑 SharedPreferences Key'leri
  static const String prefsName = "StokTakipPrefs";
  static const String keyAuthToken = "auth_token";
  static const String keyUserId = "user_id";
  static const String keyUserName = "user_name";
  static const String keyUserEmail = "user_email";
} 