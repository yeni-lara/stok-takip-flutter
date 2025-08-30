package com.stoktakip.mobile

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import okhttp3.*
import java.io.IOException
import java.util.concurrent.TimeUnit

class MainActivity : AppCompatActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Oturum kontrolü - eğer giriş yapılmamışsa LoginActivity'ye yönlendir
        if (!isUserLoggedIn()) {
            redirectToLoginActivity()
            return
        }
        
        setContentView(R.layout.activity_main)
        
        // Action bar'ı özelleştir
        supportActionBar?.apply {
            title = "Dashboard"
            setBackgroundDrawable(ContextCompat.getDrawable(this@MainActivity, R.color.black))
        }
        
        // Butonları bul ve click listener'ları ekle
        val btnStockExit = findViewById<Button>(R.id.btnStockExit)
        val btnStockReturn = findViewById<Button>(R.id.btnStockReturn)
        val btnLogout = findViewById<Button>(R.id.btnLogout)
        
        btnStockExit.setOnClickListener {
            val intent = Intent(this, StockExitActivity::class.java)
            startActivity(intent)
        }
        
        btnStockReturn.setOnClickListener {
            val intent = Intent(this, StockReturnActivity::class.java)
            startActivity(intent)
        }
        
        btnLogout.setOnClickListener {
            showLogoutConfirmationDialog()
        }
    }

    private fun isUserLoggedIn(): Boolean {
        val sharedPrefs = getSharedPreferences(Config.PREFS_NAME, Context.MODE_PRIVATE)
        val token = sharedPrefs.getString(Config.KEY_AUTH_TOKEN, null)
        return !token.isNullOrEmpty()
    }

    private fun redirectToLoginActivity() {
        val intent = Intent(this@MainActivity, LoginActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }

    private fun showLogoutConfirmationDialog() {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle("Çıkış Onayı")
            .setMessage("Oturumu kapatmak istediğinizden emin misiniz?")
            .setPositiveButton("Evet, Çıkış Yap") { _, _ ->
                performLogout()
            }
            .setNegativeButton("İptal", null)
            .show()
    }

    private fun performLogout() {
        // API'ye logout isteği gönder
        callLogoutAPI()
    }

    private fun callLogoutAPI() {
        val sharedPrefs = getSharedPreferences(Config.PREFS_NAME, Context.MODE_PRIVATE)
        val token = sharedPrefs.getString(Config.KEY_AUTH_TOKEN, null)
        
        if (token.isNullOrEmpty()) {
            // Token yoksa direkt çıkış yap
            clearAuthData()
            redirectToLoginActivity()
            return
        }

        // HTTP request
        val request = Request.Builder()
            .url(Config.API_LOGOUT)
            .addHeader("Authorization", "Bearer $token")
            .addHeader("Accept", "application/json")
            .post(RequestBody.create(null, ""))
            .build()

        // HTTP client
        val client = OkHttpClient.Builder()
            .connectTimeout(Config.CONNECT_TIMEOUT, TimeUnit.SECONDS)
            .readTimeout(Config.READ_TIMEOUT, TimeUnit.SECONDS)
            .build()

        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                runOnUiThread {
                    // API hatası olsa bile local'den çıkış yap
                    Toast.makeText(this@MainActivity, "Çıkış yapıldı", Toast.LENGTH_SHORT).show()
                    clearAuthData()
                    redirectToLoginActivity()
                }
            }

            override fun onResponse(call: okhttp3.Call, response: okhttp3.Response) {
                runOnUiThread {
                    // Başarılı veya başarısız, her durumda local'den çıkış yap
                    Toast.makeText(this@MainActivity, "Çıkış yapıldı", Toast.LENGTH_SHORT).show()
                    clearAuthData()
                    redirectToLoginActivity()
                }
            }
        })
    }

    private fun clearAuthData() {
        val sharedPrefs = getSharedPreferences(Config.PREFS_NAME, Context.MODE_PRIVATE)
        sharedPrefs.edit().clear().apply()
    }
}