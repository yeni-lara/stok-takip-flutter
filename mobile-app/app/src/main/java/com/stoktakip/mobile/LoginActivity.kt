package com.stoktakip.mobile

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.ProgressBar
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject
import java.io.IOException
import java.util.concurrent.TimeUnit

class LoginActivity : AppCompatActivity() {

                    private lateinit var etUsername: EditText
                private lateinit var etPassword: EditText
                private lateinit var btnLogin: Button
                private lateinit var progressBar: ProgressBar

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
                    setupProgressBar()
    }

                    private fun findViews() {
                    etUsername = findViewById(R.id.etUsername)
                    etPassword = findViewById(R.id.etPassword)
                    btnLogin = findViewById(R.id.btnLogin)
                    progressBar = findViewById(R.id.progressBar)
                }

    private fun setupClickListeners() {
        btnLogin.setOnClickListener {
            validateAndLogin()
        }
    }

                    private fun validateAndLogin() {
                    val email = etUsername.text.toString()
                    val password = etPassword.text.toString()

                    if (email.isEmpty() || password.isEmpty()) {
                        Toast.makeText(this, "Lütfen tüm alanları doldurun!", Toast.LENGTH_SHORT).show()
                        return
                    }

                    // API'ye login isteği gönder
                    performLogin(email, password)
                }

                private fun performLogin(email: String, password: String) {
                    showLoading(true)

                    // API endpoint
                    val url = Config.API_LOGIN
                    
                    // JSON request body
                    val jsonBody = """
                        {
                            "email": "$email",
                            "password": "$password"
                        }
                    """.trimIndent()

                    // HTTP request
                    val request = Request.Builder()
                        .url(url)
                        .post(jsonBody.toRequestBody("application/json".toMediaType()))
                        .build()

                    // HTTP client
                    val client = OkHttpClient.Builder()
                        .connectTimeout(Config.CONNECT_TIMEOUT, TimeUnit.SECONDS)
                        .readTimeout(Config.READ_TIMEOUT, TimeUnit.SECONDS)
                        .build()

                    client.newCall(request).enqueue(object : Callback {
                        override fun onFailure(call: Call, e: IOException) {
                            runOnUiThread {
                                showLoading(false)
                                Toast.makeText(this@LoginActivity, "Bağlantı hatası: ${e.message}", Toast.LENGTH_LONG).show()
                            }
                        }

                        override fun onResponse(call: Call, response: Response) {
                            val responseBody = response.body?.string()
                            
                            runOnUiThread {
                                showLoading(false)
                                
                                if (response.isSuccessful && responseBody != null) {
                                    try {
                                        val jsonObject = JSONObject(responseBody)
                                        val token = jsonObject.getString("token")
                                        val message = jsonObject.getString("message")
                                        
                                        // Token'ı SharedPreferences'a kaydet
                                        saveAuthToken(token)
                                        
                                        Toast.makeText(this@LoginActivity, message, Toast.LENGTH_SHORT).show()
                                        
                                        // Dashboard'a yönlendir
                                        val intent = Intent(this@LoginActivity, MainActivity::class.java)
                                        startActivity(intent)
                                        finish()
                                    } catch (e: Exception) {
                                        Toast.makeText(this@LoginActivity, "Yanıt işlenirken hata: ${e.message}", Toast.LENGTH_LONG).show()
                                    }
                                } else {
                                    try {
                                        val jsonObject = JSONObject(responseBody ?: "{}")
                                        val errors = jsonObject.optJSONObject("errors")
                                        val errorMessage = if (errors != null) {
                                            errors.optString("email", "Giriş başarısız")
                                        } else {
                                            "Giriş başarısız"
                                        }
                                        Toast.makeText(this@LoginActivity, errorMessage, Toast.LENGTH_LONG).show()
                                    } catch (e: Exception) {
                                        Toast.makeText(this@LoginActivity, "Giriş başarısız", Toast.LENGTH_LONG).show()
                                    }
                                }
                            }
                        }
                    })
                }

                private fun showLoading(show: Boolean) {
                    progressBar.visibility = if (show) View.VISIBLE else View.GONE
                    btnLogin.isEnabled = !show
                    etUsername.isEnabled = !show
                    etPassword.isEnabled = !show
                }

                private fun setupProgressBar() {
                    // ProgressBar zaten layout'ta tanımlı, ek bir setup gerekmiyor
                }

                private fun saveAuthToken(token: String) {
                    val sharedPrefs = getSharedPreferences(Config.PREFS_NAME, Context.MODE_PRIVATE)
                    sharedPrefs.edit().putString(Config.KEY_AUTH_TOKEN, token).apply()
                }
} 