package com.stoktakip.mobile

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
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
        
        if (productCode.isEmpty() || quantity.isEmpty() || returnReason.isEmpty()) {
            Toast.makeText(this, "Lütfen tüm gerekli alanları doldurun!", Toast.LENGTH_SHORT).show()
            return
        }
        
        // Onay dialog'u göster
        AlertDialog.Builder(this)
            .setTitle("Stok İade Onayı")
            .setMessage("""
                Ürün Kodu: $productCode
                Miktar: $quantity
                İade Sebebi: $returnReason
                
                Onaylıyor musunuz?
            """.trimIndent())
            .setPositiveButton("Onayla") { _, _ ->
                // Başarılı mesajı göster
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