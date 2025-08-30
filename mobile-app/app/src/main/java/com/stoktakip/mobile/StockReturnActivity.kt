package com.stoktakip.mobile

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat

class StockReturnActivity : AppCompatActivity() {

                    private lateinit var etProductCode: EditText
                private lateinit var etQuantity: EditText
                private lateinit var etReturnReason: EditText
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
        private const val QR_SCAN_REQUEST_CODE = 101
    }
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_stock_return)
        
        // Action bar'ı özelleştir
        supportActionBar?.apply {
            title = "Stok İade"
            setBackgroundDrawable(ContextCompat.getDrawable(this@StockReturnActivity, R.color.black))
        }
        
        // View'ları bul
        findViews()
        
        // Click listener'ları ekle
        setupClickListeners()
    }
    
                    private fun findViews() {
                    etProductCode = findViewById(R.id.etProductCode)
                    etQuantity = findViewById(R.id.etQuantity)
                    etReturnReason = findViewById(R.id.etReturnReason)
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
                    val returnReason = etReturnReason.text.toString()
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

                    // İade sebebi veya açıklama kontrolü (web sayfasındaki gibi)
                    if (returnReason.isEmpty() && notes.isEmpty()) {
                        Toast.makeText(this, "İade sebebi veya açıklama gerekli!", Toast.LENGTH_SHORT).show()
                        return
                    }

                    // Onay dialog'u göster
                    AlertDialog.Builder(this)
                        .setTitle("Stok İade Onayı")
                        .setMessage("""
                            Ürün Kodu: $productCode
                            Miktar: $quantity
                            İade Sebebi: ${if (returnReason.isNotEmpty()) returnReason else "Belirtilmedi"}
                            Açıklama: ${if (notes.isNotEmpty()) notes else "Belirtilmedi"}

                            Onaylıyor musunuz?
                        """.trimIndent())
                        .setPositiveButton("Onayla") { _, _ ->
                            // TODO: API'ye gönder
                            Toast.makeText(this, "Stok iade işlemi tamamlandı!", Toast.LENGTH_LONG).show()
                            finish() // Ana sayfaya dön
                        }
                        .setNegativeButton("İptal", null)
                        .show()
                }
    
                    private fun clearForm() {
                    etProductCode.text.clear()
                    etQuantity.text.clear()
                    etReturnReason.text.clear()
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
                        }
                    }
                }
            } 