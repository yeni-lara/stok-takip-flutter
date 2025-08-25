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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['individual', 'corporate'])->default('individual'); // bireysel/kurumsal
            $table->string('name')->nullable(); // Ad/Firma adı
            $table->string('surname')->nullable(); // Soyad (bireysel için)
            $table->string('company_name')->nullable(); // Firma adı (kurumsal için)
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('tax_number')->nullable(); // Vergi numarası (kurumsal için)
            $table->string('tax_office')->nullable(); // Vergi dairesi (kurumsal için)
            $table->text('notes')->nullable(); // Notlar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
