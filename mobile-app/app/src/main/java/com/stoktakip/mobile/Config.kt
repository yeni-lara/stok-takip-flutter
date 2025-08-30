package com.stoktakip.mobile

object Config {
    // 🌐 API BASE URL - TEK DEĞİŞTİRECEĞİNİZ YER!
    // Development (şu anki)
    const val BASE_URL = "http://192.168.1.27:8000"
    
    // Production (canlı sunucuda sadece bu satırı değiştirin)
    // const val BASE_URL = "https://www.siteniz.com"
    
    // 📡 API Endpoints
    const val API_LOGIN = "$BASE_URL/api/login"
    const val API_LOGOUT = "$BASE_URL/api/logout"
    const val API_USER = "$BASE_URL/api/user"
    
    // 🏷️ Ürün API'leri (gelecekte eklenecek)
    const val API_PRODUCT_BY_BARCODE = "$BASE_URL/api/products/by-barcode"
    
    // 📦 Stok API'leri (gelecekte eklenecek)
    const val API_STOCK_EXIT = "$BASE_URL/api/stock/exit"
    const val API_STOCK_RETURN = "$BASE_URL/api/stock/return"
    
    // ⚙️ Timeout Ayarları
    const val CONNECT_TIMEOUT = 30L
    const val READ_TIMEOUT = 30L
    
    // 🔐 SharedPreferences Key'leri
    const val PREFS_NAME = "StokTakipPrefs"
    const val KEY_AUTH_TOKEN = "auth_token"
    const val KEY_USER_ID = "user_id"
    const val KEY_USER_NAME = "user_name"
    const val KEY_USER_EMAIL = "user_email"
} 