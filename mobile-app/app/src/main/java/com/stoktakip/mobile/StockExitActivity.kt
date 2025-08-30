package com.stoktakip.mobile

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat

class StockExitActivity : AppCompatActivity() {
    
    private lateinit var etProductCode: EditText
    private lateinit var etQuantity: EditText
    private lateinit var etCustomer: EditText
    private lateinit var etNotes: EditText
    private lateinit var btnScanQR: Button
    private lateinit var btnSubmit: Button
    private lateinit var btnClear: Button
    
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
        etCustomer = findViewById(R.id.etCustomer)
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
            .setMessage("Ürün kodunu girin:")
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
        val customer = etCustomer.text.toString()
        
        if (productCode.isEmpty() || quantity.isEmpty() || customer.isEmpty()) {
            Toast.makeText(this, "Lütfen tüm gerekli alanları doldurun!", Toast.LENGTH_SHORT).show()
            return
        }
        
        // Onay dialog'u göster
        AlertDialog.Builder(this)
            .setTitle("Stok Çıkış Onayı")
            .setMessage("""
                Ürün Kodu: $productCode
                Miktar: $quantity
                Müşteri: $customer
                
                Onaylıyor musunuz?
            """.trimIndent())
            .setPositiveButton("Onayla") { _, _ ->
                // Başarılı mesajı göster
                Toast.makeText(this, "Stok çıkış işlemi tamamlandı!", Toast.LENGTH_LONG).show()
                finish() // Ana sayfaya dön
            }
            .setNegativeButton("İptal", null)
            .show()
    }
    
    private fun clearForm() {
        etProductCode.text.clear()
        etQuantity.text.clear()
        etCustomer.text.clear()
        etNotes.text.clear()
        Toast.makeText(this, "Form temizlendi", Toast.LENGTH_SHORT).show()
    }
} 