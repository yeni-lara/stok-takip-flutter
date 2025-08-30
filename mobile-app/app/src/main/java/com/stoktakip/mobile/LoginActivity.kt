package com.stoktakip.mobile

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat

class LoginActivity : AppCompatActivity() {

    private lateinit var etUsername: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnLogin: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        // Action bar'ı özelleştir
        supportActionBar?.apply {
            title = "Kasabi'Et Stok Takip"
            setBackgroundDrawable(ContextCompat.getDrawable(this@LoginActivity, R.color.black))
        }

        findViews()
        setupClickListeners()
    }

    private fun findViews() {
        etUsername = findViewById(R.id.etUsername)
        etPassword = findViewById(R.id.etPassword)
        btnLogin = findViewById(R.id.btnLogin)
    }

    private fun setupClickListeners() {
        btnLogin.setOnClickListener {
            validateAndLogin()
        }
    }

    private fun validateAndLogin() {
        val username = etUsername.text.toString()
        val password = etPassword.text.toString()

        if (username.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Lütfen tüm alanları doldurun!", Toast.LENGTH_SHORT).show()
            return
        }

        // Basit doğrulama (gerçek uygulamada API'den kontrol edilir)
        if (username == "admin" && password == "1234") {
            Toast.makeText(this, "Giriş başarılı!", Toast.LENGTH_SHORT).show()
            
            // Dashboard'a yönlendir
            val intent = Intent(this, MainActivity::class.java)
            startActivity(intent)
            finish() // Login sayfasını kapat
        } else {
            Toast.makeText(this, "Kullanıcı adı veya şifre hatalı!", Toast.LENGTH_SHORT).show()
        }
    }
} 