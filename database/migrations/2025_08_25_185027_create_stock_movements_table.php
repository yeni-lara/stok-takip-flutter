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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Hareketi yapan kullanıcı
            $table->enum('type', ['giriş', 'çıkış', 'iade']); // Hareket tipi
            $table->integer('quantity'); // Miktar
            $table->integer('previous_stock'); // Önceki stok
            $table->integer('new_stock'); // Yeni stok
            $table->text('note')->nullable(); // Açıklama
            $table->string('reference_number')->nullable(); // Referans numarası (fatura vs.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
