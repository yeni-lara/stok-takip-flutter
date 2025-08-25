<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('barcode')->unique()->nullable(); // EAN-13 barkod
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('unit_price', 10, 2); // Birim fiyat
            $table->decimal('tax_rate', 5, 2)->default(18.00); // KDV oranı %
            $table->integer('current_stock')->default(0); // Mevcut stok
            $table->integer('min_stock')->default(0); // Minimum stok uyarı limiti
            $table->string('image_path')->nullable(); // Ürün resmi yolu
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
