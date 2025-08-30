package com.stoktakip.mobile

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
        val input = EditText(this)
        input.hint = "QR kod veya ürün kodu girin"
        
        AlertDialog.Builder(this)
            .setTitle("QR Kod Tarama")
            .setMessage("İade edilecek ürün kodunu girin:")
            .setView(input)
            .setPositiveButton("Tamam") { _, _ ->
                val code = input.text.toString()
                if (code.isNotEmpty()) {
                    etProductCode.setText(code)
                    Toast.makeText(this, "Ürün kodu: $code", Toast.LENGTH_SHORT).show()
                }
            }
            .setNegativeButton("İptal", null)
            .show()
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
} 