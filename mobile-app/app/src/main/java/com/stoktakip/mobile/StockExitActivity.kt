package com.stoktakip.mobile

import android.content.Intent
import android.os.Bundle
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.Spinner
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import android.view.View
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject
import java.io.IOException
import java.util.concurrent.TimeUnit
import com.bumptech.glide.Glide

class StockExitActivity : AppCompatActivity() {

                    private lateinit var etProductCode: EditText
                private lateinit var etQuantity: EditText
                private lateinit var spinnerCustomer: Spinner
                private lateinit var etNotes: EditText
                private lateinit var btnScanQR: Button
                private lateinit var btnSubmit: Button
                private lateinit var btnClear: Button
                
                // Ürün bilgi kartı elementleri
                private lateinit var productInfoCard: LinearLayout
                private lateinit var productImage: ImageView
                private lateinit var productName: TextView
                private lateinit var productCategory: TextView
                private lateinit var productBarcode: TextView
                private lateinit var stockBadge: TextView
                private lateinit var stockInfo: LinearLayout
                private lateinit var stockText: TextView

    companion object {
        private const val QR_SCAN_REQUEST_CODE = 100
    }
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_stock_exit)
        
        // Action bar'ı özelleştir
        supportActionBar?.apply {
            title = "Stok Çıkış"
            setBackgroundDrawable(ContextCompat.getDrawable(this@StockExitActivity, R.color.black))
        }
        
        // View'ları bul
        findViews()
        
        // Click listener'ları ekle
        setupClickListeners()
    }
    
    private fun findViews() {
                            etProductCode = findViewById(R.id.etProductCode)
                    etQuantity = findViewById(R.id.etQuantity)
                    spinnerCustomer = findViewById(R.id.spinnerCustomer)
                    etNotes = findViewById(R.id.etNotes)
        btnScanQR = findViewById(R.id.btnScanQR)
        btnSubmit = findViewById(R.id.btnSubmit)
        btnClear = findViewById(R.id.btnClear)
        
        // Ürün bilgi kartı elementleri
        productInfoCard = findViewById(R.id.productInfoCard)
        productImage = findViewById(R.id.productImage)
        productName = findViewById(R.id.productName)
        productCategory = findViewById(R.id.productCategory)
        productBarcode = findViewById(R.id.productBarcode)
        stockBadge = findViewById(R.id.stockBadge)
        stockInfo = findViewById(R.id.stockInfo)
        stockText = findViewById(R.id.stockText)
    }
    
    private fun setupClickListeners() {
        btnScanQR.setOnClickListener {
            showQRScanDialog()
        }
        
        btnSubmit.setOnClickListener {
            validateAndSubmit()
        }
        
        btnClear.setOnClickListener {
            clearForm()
        }
    }
    
        private fun showQRScanDialog() {
        // Gerçek kamera ile QR tarama
        val intent = Intent(this, QRScannerActivity::class.java)
        startActivityForResult(intent, QR_SCAN_REQUEST_CODE)
    }
    
                    private fun validateAndSubmit() {
                    val productCode = etProductCode.text.toString()
                    val quantity = etQuantity.text.toString()
                    val customer = spinnerCustomer.selectedItem?.toString() ?: ""
                    val notes = etNotes.text.toString()

                    // Validasyon kuralları (web sayfasındaki gibi)
                    if (productCode.isEmpty()) {
                        Toast.makeText(this, "Ürün kodu gerekli!", Toast.LENGTH_SHORT).show()
                        return
                    }

                    if (quantity.isEmpty() || quantity.toIntOrNull() == null || quantity.toInt() < 1) {
                        Toast.makeText(this, "Geçerli miktar giriniz (min: 1)!", Toast.LENGTH_SHORT).show()
                        return
                    }

                    // Müşteri veya açıklama kontrolü (web sayfasındaki gibi)
                    if (customer.isEmpty() && notes.isEmpty()) {
                        Toast.makeText(this, "Müşteri veya açıklama gerekli!", Toast.LENGTH_SHORT).show()
                        return
                    }

                    // Onay dialog'u göster
                    AlertDialog.Builder(this)
                        .setTitle("Stok Çıkış Onayı")
                        .setMessage("""
                            Ürün Kodu: $productCode
                            Miktar: $quantity
                            Müşteri: ${if (customer.isNotEmpty()) customer else "Belirtilmedi"}
                            Açıklama: ${if (notes.isNotEmpty()) notes else "Belirtilmedi"}

                            Onaylıyor musunuz?
                        """.trimIndent())
                        .setPositiveButton("Onayla") { _, _ ->
                            // TODO: API'ye gönder
                            Toast.makeText(this, "Stok çıkış işlemi tamamlandı!", Toast.LENGTH_LONG).show()
                            finish() // Ana sayfaya dön
                        }
                        .setNegativeButton("İptal", null)
                        .show()
                }
    
                    private fun clearForm() {
                    etProductCode.text.clear()
                    etQuantity.text.clear()
                    spinnerCustomer.setSelection(0)
                    etNotes.text.clear()
                    Toast.makeText(this, "Form temizlendi", Toast.LENGTH_SHORT).show()
                }

                                override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
                    super.onActivityResult(requestCode, resultCode, data)

                    if (requestCode == QR_SCAN_REQUEST_CODE && resultCode == RESULT_OK) {
                        val scannedCode = data?.getStringExtra(QRScannerActivity.EXTRA_SCANNED_CODE)
                        if (scannedCode != null) {
                            etProductCode.setText(scannedCode.toString())
                            Toast.makeText(this, "QR kod tarandı: $scannedCode", Toast.LENGTH_SHORT).show()
                            
                            // Barkod ile ürün bilgisini getir
                            fetchProductByBarcode(scannedCode.toString())
                        }
                    }
                }

                private fun fetchProductByBarcode(barcode: String) {
                    // API endpoint
                    val url = "${Config.API_PRODUCT_BY_BARCODE}/$barcode"
                    
                    // HTTP request
                    val request = Request.Builder()
                        .url(url)
                        .get()
                        .build()

                    // HTTP client
                    val client = OkHttpClient.Builder()
                        .connectTimeout(Config.CONNECT_TIMEOUT, TimeUnit.SECONDS)
                        .readTimeout(Config.READ_TIMEOUT, TimeUnit.SECONDS)
                        .build()

                    client.newCall(request).enqueue(object : Callback {
                        override fun onFailure(call: Call, e: IOException) {
                            runOnUiThread {
                                Toast.makeText(this@StockExitActivity, "Ürün bilgisi alınamadı: ${e.message}", Toast.LENGTH_LONG).show()
                            }
                        }

                        override fun onResponse(call: Call, response: Response) {
                            val responseBody = response.body?.string()
                            
                            runOnUiThread {
                                if (response.isSuccessful && responseBody != null) {
                                    try {
                                        val jsonObject = JSONObject(responseBody)
                                        val success = jsonObject.getBoolean("success")
                                        
                                        if (success) {
                                            val product = jsonObject.getJSONObject("product")
                                            displayProductInfo(product)
                                            enableSubmitButton()
                                        } else {
                                            val message = jsonObject.getString("message")
                                            Toast.makeText(this@StockExitActivity, message, Toast.LENGTH_LONG).show()
                                            clearProductInfo()
                                        }
                                    } catch (e: Exception) {
                                        Toast.makeText(this@StockExitActivity, "Ürün bilgisi işlenirken hata: ${e.message}", Toast.LENGTH_LONG).show()
                                        clearProductInfo()
                                    }
                                } else {
                                    Toast.makeText(this@StockExitActivity, "Ürün bulunamadı", Toast.LENGTH_LONG).show()
                                    clearProductInfo()
                                }
                            }
                        }
                    })
                }

                private fun displayProductInfo(product: JSONObject) {
                    try {
                        // Ürün bilgilerini göster
                        productName.text = product.getString("name")
                        productCategory.text = product.getString("category") ?: ""
                        productBarcode.text = product.getString("barcode")
                        
                        // Stok bilgisini göster
                        val currentStock = product.getInt("current_stock")
                        stockBadge.text = "$currentStock"
                        
                        // Stok uyarısı
                        if (currentStock <= 5) {
                            stockInfo.visibility = View.VISIBLE
                            stockText.text = "Düşük stok uyarısı! Mevcut stok: $currentStock"
                        } else {
                            stockInfo.visibility = View.GONE
                        }
                        
                        // Ürün resmi varsa göster
                        val imagePath = product.optString("image_path", "")
                        if (imagePath.isNotEmpty()) {
                            productImage.visibility = View.VISIBLE
                            loadProductImage(imagePath)
                        } else {
                            productImage.visibility = View.GONE
                        }
                        
                        // Tüm elementleri görünür yap
                        productName.visibility = View.VISIBLE
                        productCategory.visibility = View.VISIBLE
                        productBarcode.visibility = View.VISIBLE
                        stockBadge.visibility = View.VISIBLE
                        
                    } catch (e: Exception) {
                        Toast.makeText(this, "Ürün bilgisi gösterilirken hata: ${e.message}", Toast.LENGTH_LONG).show()
                    }
                }

                private fun clearProductInfo() {
                    // Ürün bilgilerini temizle
                    productName.text = "Barkod okutarak ürün seçin"
                    productCategory.text = ""
                    productBarcode.text = ""
                    stockBadge.text = ""
                    
                    // Elementleri gizle
                    productName.visibility = View.VISIBLE
                    productCategory.visibility = View.GONE
                    productBarcode.visibility = View.GONE
                    stockBadge.visibility = View.GONE
                    stockInfo.visibility = View.GONE
                    productImage.visibility = View.GONE
                    
                    // Submit butonunu devre dışı bırak
                    btnSubmit.isEnabled = false
                }

                private fun enableSubmitButton() {
                    btnSubmit.isEnabled = true
                }

                private fun loadProductImage(imagePath: String) {
                    try {
                        // API base URL'den resim URL'ini oluştur
                        val fullImageUrl = "${Config.BASE_URL}/$imagePath"
                        
                        // Glide ile resmi yükle
                        Glide.with(this)
                            .load(fullImageUrl)
                            .placeholder(R.drawable.ic_launcher_foreground)
                            .error(R.drawable.ic_launcher_foreground)
                            .centerCrop()
                            .into(productImage)
                            
                    } catch (e: Exception) {
                        // Hata durumunda placeholder göster
                        productImage.setImageResource(R.drawable.ic_launcher_foreground)
                    }
                }

                private fun setupCustomerSpinner() {
                    // API'den müşteri listesini çek
                    fetchCustomersFromAPI()
                }

                private fun fetchCustomersFromAPI() {
                    // API endpoint
                    val url = "${Config.BASE_URL}/api/customers"
                    
                    // HTTP request
                    val request = Request.Builder()
                        .url(url)
                        .get()
                        .build()

                    // HTTP client
                    val client = OkHttpClient.Builder()
                        .connectTimeout(Config.CONNECT_TIMEOUT, TimeUnit.SECONDS)
                        .readTimeout(Config.READ_TIMEOUT, TimeUnit.SECONDS)
                        .build()

                    client.newCall(request).enqueue(object : Callback {
                        override fun onFailure(call: Call, e: IOException) {
                            runOnUiThread {
                                Toast.makeText(this@StockExitActivity, "Müşteri listesi alınamadı: ${e.message}", Toast.LENGTH_LONG).show()
                                // Hata durumunda boş liste göster
                                setupEmptyCustomerSpinner()
                            }
                        }

                        override fun onResponse(call: Call, response: Response) {
                            val responseBody = response.body?.string()
                            
                            runOnUiThread {
                                if (response.isSuccessful && responseBody != null) {
                                    try {
                                        val jsonObject = JSONObject(responseBody)
                                        val success = jsonObject.getBoolean("success")
                                        
                                        if (success) {
                                            val customersArray = jsonObject.getJSONArray("customers")
                                            val customerNames = mutableListOf<String>()
                                            customerNames.add("Müşteri Seçiniz")
                                            
                                            for (i in 0 until customersArray.length()) {
                                                val customer = customersArray.getJSONObject(i)
                                                customerNames.add(customer.getString("name"))
                                            }
                                            
                                            setupCustomerSpinnerWithData(customerNames.toTypedArray())
                                        } else {
                                            Toast.makeText(this@StockExitActivity, "Müşteri listesi alınamadı", Toast.LENGTH_LONG).show()
                                            setupEmptyCustomerSpinner()
                                        }
                                    } catch (e: Exception) {
                                        Toast.makeText(this@StockExitActivity, "Müşteri listesi işlenirken hata: ${e.message}", Toast.LENGTH_LONG).show()
                                        setupEmptyCustomerSpinner()
                                    }
                                } else {
                                    Toast.makeText(this@StockExitActivity, "Müşteri listesi alınamadı", Toast.LENGTH_LONG).show()
                                    setupEmptyCustomerSpinner()
                                }
                            }
                        }
                    })
                }

                private fun setupCustomerSpinnerWithData(customers: Array<String>) {
                    val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, customers)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinnerCustomer.adapter = adapter
                }

                private fun setupEmptyCustomerSpinner() {
                    val emptyCustomers = arrayOf("Müşteri bulunamadı")
                    val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, emptyCustomers)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinnerCustomer.adapter = adapter
                }
            } 