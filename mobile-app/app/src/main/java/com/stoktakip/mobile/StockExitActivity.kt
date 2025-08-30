package com.stoktakip.mobile

import android.content.Context
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
                
                // Ürün bilgileri (API'ye göndermek için)
                private var currentProductId: Int = 0
                private var currentProductStock: Int = 0
                
                // Müşteri bilgileri (ID ve isim eşleştirmesi için)
                private var customerIdMap: MutableMap<String, Int> = mutableMapOf()

    companion object {
        private const val QR_SCAN_REQUEST_CODE = 100
    }
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Oturum kontrolü - eğer giriş yapılmamışsa LoginActivity'ye yönlendir
        if (!isUserLoggedIn()) {
            redirectToLoginActivity()
            return
        }
        
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
        
        // Müşteri listesini API'den çek
        setupCustomerSpinner()
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

                    // Profesyonel onay ekranını göster
                    showProfessionalConfirmationDialog(productCode, quantity, customer, notes)
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
                        
                        // Ürün ID ve stok bilgisini sakla (API için)
                        currentProductId = product.getInt("id")
                        currentProductStock = currentStock
                        
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
                    
                    // Ürün ID ve stok bilgisini sıfırla
                    currentProductId = 0
                    currentProductStock = 0
                    
                    // Müşteri map'ini temizle
                    customerIdMap.clear()
                    
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
                                            
                                            // Müşteri ID map'ini temizle
                                            customerIdMap.clear()
                                            
                                            for (i in 0 until customersArray.length()) {
                                                val customer = customersArray.getJSONObject(i)
                                                val customerName = customer.getString("company_name")
                                                val customerId = customer.getInt("id")
                                                
                                                customerNames.add(customerName)
                                                customerIdMap[customerName] = customerId
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
                    // Özel adapter oluştur - seçilen değerin görünür olması için
                    val adapter = object : ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, customers) {
                        override fun getView(position: Int, convertView: android.view.View?, parent: android.view.ViewGroup): android.view.View {
                            val view = super.getView(position, convertView, parent)
                            val textView = view.findViewById<android.widget.TextView>(android.R.id.text1)
                            textView.setTextColor(ContextCompat.getColor(this@StockExitActivity, R.color.white))
                            return view
                        }
                        
                        override fun getDropDownView(position: Int, convertView: android.view.View?, parent: android.view.ViewGroup): android.view.View {
                            val view = super.getDropDownView(position, convertView, parent)
                            val textView = view.findViewById<android.widget.TextView>(android.R.id.text1)
                            textView.setTextColor(ContextCompat.getColor(this@StockExitActivity, R.color.black))
                            return view
                        }
                    }
                    
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinnerCustomer.adapter = adapter
                    
                    // Spinner seçim listener'ı ekle
                    spinnerCustomer.onItemSelectedListener = object : android.widget.AdapterView.OnItemSelectedListener {
                        override fun onItemSelected(parent: android.widget.AdapterView<*>?, view: android.view.View?, position: Int, id: Long) {
                            // Seçilen müşteriyi log'la (debug için)
                            val selectedCustomer = customers[position]
                            android.util.Log.d("StockExit", "Seçilen müşteri: $selectedCustomer")
                        }
                        
                        override fun onNothingSelected(parent: android.widget.AdapterView<*>?) {
                            // Hiçbir şey seçilmediğinde
                        }
                    }
                }

                private fun setupEmptyCustomerSpinner() {
                    val emptyCustomers = arrayOf("Müşteri bulunamadı")
                    val adapter = object : ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, emptyCustomers) {
                        override fun getView(position: Int, convertView: android.view.View?, parent: android.view.ViewGroup): android.view.View {
                            val view = super.getView(position, convertView, parent)
                            val textView = view.findViewById<android.widget.TextView>(android.R.id.text1)
                            textView.setTextColor(ContextCompat.getColor(this@StockExitActivity, R.color.white))
                            return view
                        }
                    }
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinnerCustomer.adapter = adapter
                }

                private fun showProfessionalConfirmationDialog(productCode: String, quantity: String, customer: String, notes: String) {
                    // Dialog layout'unu inflate et
                    val dialogView = layoutInflater.inflate(R.layout.dialog_stock_confirmation, null)
                    
                    // Dialog elementlerini bul
                    val tvTitle = dialogView.findViewById<TextView>(R.id.tvTitle)
                    val iconOperation = dialogView.findViewById<ImageView>(R.id.iconOperation)
                    val confirmProductImage = dialogView.findViewById<ImageView>(R.id.confirmProductImage)
                    val confirmProductName = dialogView.findViewById<TextView>(R.id.confirmProductName)
                    val confirmProductCategory = dialogView.findViewById<TextView>(R.id.confirmProductCategory)
                    val confirmProductBarcode = dialogView.findViewById<TextView>(R.id.confirmProductBarcode)
                    val confirmStockBadge = dialogView.findViewById<TextView>(R.id.confirmStockBadge)
                    val confirmQuantity = dialogView.findViewById<TextView>(R.id.confirmQuantity)
                    val confirmCustomer = dialogView.findViewById<TextView>(R.id.confirmCustomer)
                    val confirmNotes = dialogView.findViewById<TextView>(R.id.confirmNotes)
                    val confirmStockWarning = dialogView.findViewById<LinearLayout>(R.id.confirmStockWarning)
                    val confirmStockWarningText = dialogView.findViewById<TextView>(R.id.confirmStockWarningText)
                    val btnCancel = dialogView.findViewById<Button>(R.id.btnCancel)
                    val btnConfirm = dialogView.findViewById<Button>(R.id.btnConfirm)
                    
                    // Başlık ve ikon ayarla
                    tvTitle.text = "Stok Çıkış Onayı"
                    iconOperation.setImageResource(R.drawable.ic_launcher_foreground)
                    
                    // Ürün bilgilerini doldur
                    confirmProductName.text = productName.text.toString()
                    confirmProductCategory.text = productCategory.text.toString()
                    confirmProductBarcode.text = "Barkod: $productCode"
                    confirmStockBadge.text = "$currentProductStock"
                    confirmQuantity.text = quantity
                    confirmCustomer.text = if (customer.isNotEmpty()) customer else "Belirtilmedi"
                    confirmNotes.text = if (notes.isNotEmpty()) notes else "Belirtilmedi"
                    
                    // Ürün resmini yükle
                    if (productImage.visibility == View.VISIBLE) {
                        confirmProductImage.visibility = View.VISIBLE
                        confirmProductImage.setImageDrawable(productImage.drawable)
                    } else {
                        confirmProductImage.visibility = View.GONE
                    }
                    
                    // Stok uyarısı varsa göster
                    if (stockInfo.visibility == View.VISIBLE) {
                        confirmStockWarning.visibility = View.VISIBLE
                        confirmStockWarningText.text = stockText.text.toString()
                    } else {
                        confirmStockWarning.visibility = View.GONE
                    }
                    
                    // Dialog oluştur
                    val dialog = AlertDialog.Builder(this)
                        .setView(dialogView)
                        .setCancelable(false)
                        .create()
                    
                    // Buton click listener'ları
                    btnCancel.setOnClickListener {
                        dialog.dismiss()
                    }
                    
                    btnConfirm.setOnClickListener {
                        dialog.dismiss()
                        // API'ye gönder
                        submitStockExit(productCode, quantity, customer, notes)
                    }
                    
                    // Dialog'u göster
                    dialog.show()
                }

                private fun submitStockExit(productCode: String, quantity: String, customer: String, notes: String) {
                    // Loading göster
                    btnSubmit.isEnabled = false
                    btnSubmit.text = "Gönderiliyor..."
                    
                    // Müşteri ID'sini bul
                    val customerId = if (customer != "Müşteri Seçiniz" && customer != "Belirtilmedi") {
                        // Müşteri adından ID bul
                        val foundId = findCustomerIdByName(customer)
                        android.util.Log.d("StockExit", "Müşteri: $customer, Bulunan ID: $foundId")
                        foundId
                    } else {
                        android.util.Log.d("StockExit", "Müşteri seçilmedi veya geçersiz")
                        null
                    }
                    
                    // API request body oluştur
                    val requestBody = JSONObject().apply {
                        put("product_id", currentProductId)
                        put("quantity", quantity.toInt())
                        if (customerId != null) put("customer_id", customerId)
                        if (notes.isNotEmpty()) put("note", notes)
                    }
                    
                    // API'ye gönder
                    val url = Config.API_STOCK_EXIT
                    val mediaType = "application/json; charset=utf-8".toMediaType()
                    val body = requestBody.toString().toRequestBody(mediaType)
                    
                    val request = Request.Builder()
                        .url(url)
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build()
                    
                    val client = OkHttpClient.Builder()
                        .connectTimeout(Config.CONNECT_TIMEOUT, TimeUnit.SECONDS)
                        .readTimeout(Config.READ_TIMEOUT, TimeUnit.SECONDS)
                        .build()
                    
                    client.newCall(request).enqueue(object : Callback {
                        override fun onFailure(call: Call, e: IOException) {
                            runOnUiThread {
                                btnSubmit.isEnabled = true
                                btnSubmit.text = "Stok Çıkış Yap"
                                Toast.makeText(this@StockExitActivity, "Bağlantı hatası: ${e.message}", Toast.LENGTH_LONG).show()
                            }
                        }
                        
                        override fun onResponse(call: Call, response: Response) {
                            val responseBody = response.body?.string()
                            
                            runOnUiThread {
                                btnSubmit.isEnabled = true
                                btnSubmit.text = "Stok Çıkış Yap"
                                
                                if (response.isSuccessful && responseBody != null) {
                                    try {
                                        val jsonObject = JSONObject(responseBody)
                                        val success = jsonObject.getBoolean("success")
                                        
                                        if (success) {
                                            val message = jsonObject.getString("message")
                                            Toast.makeText(this@StockExitActivity, message, Toast.LENGTH_LONG).show()
                                            finish() // Ana sayfaya dön
                                        } else {
                                            val errorMessage = jsonObject.getString("message")
                                            Toast.makeText(this@StockExitActivity, errorMessage, Toast.LENGTH_LONG).show()
                                        }
                                    } catch (e: Exception) {
                                        Toast.makeText(this@StockExitActivity, "Yanıt işlenirken hata: ${e.message}", Toast.LENGTH_LONG).show()
                                    }
                                } else {
                                    Toast.makeText(this@StockExitActivity, "Sunucu hatası: ${response.code}", Toast.LENGTH_LONG).show()
                                }
                            }
                        }
                    })
                }

                private fun findCustomerIdByName(customerName: String): Int? {
                    // Müşteri adından ID'yi map'ten bul
                    return customerIdMap[customerName]
                }

                private fun isUserLoggedIn(): Boolean {
                    val sharedPrefs = getSharedPreferences(Config.PREFS_NAME, Context.MODE_PRIVATE)
                    val token = sharedPrefs.getString(Config.KEY_AUTH_TOKEN, null)
                    return !token.isNullOrEmpty()
                }

                private fun redirectToLoginActivity() {
                    val intent = Intent(this@StockExitActivity, LoginActivity::class.java)
                    intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                    startActivity(intent)
                    finish()
                }
            } 