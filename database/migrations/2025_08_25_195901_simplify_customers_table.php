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
        Schema::table('customers', function (Blueprint $table) {
            // Gereksiz kolonları kaldır
            $table->dropColumn([
                'type',
                'surname', 
                'company_name',
                'phone',
                'email',
                'address',
                'city',
                'tax_number',
                'tax_office',
                'notes'
            ]);
            
            // Name kolonunu company_name olarak yeniden adlandır
            $table->renameColumn('name', 'company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Geri al
            $table->renameColumn('company_name', 'name');
            
            // Kolonları geri ekle
            $table->enum('type', ['individual', 'corporate'])->default('individual');
            $table->string('surname')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->text('notes')->nullable();
        });
    }
};
