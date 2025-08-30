package com.stoktakip.mobile

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat

class MainActivity : AppCompatActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
                            // Action bar'ı özelleştir
                    supportActionBar?.apply {
                        title = "Dashboard"
                        setBackgroundDrawable(ContextCompat.getDrawable(this@MainActivity, R.color.black))
                    }
        
        // Butonları bul ve click listener'ları ekle
        val btnStockExit = findViewById<Button>(R.id.btnStockExit)
        val btnStockReturn = findViewById<Button>(R.id.btnStockReturn)
        
        btnStockExit.setOnClickListener {
            val intent = Intent(this, StockExitActivity::class.java)
            startActivity(intent)
        }
        
        btnStockReturn.setOnClickListener {
            val intent = Intent(this, StockReturnActivity::class.java)
            startActivity(intent)
        }
    }
}