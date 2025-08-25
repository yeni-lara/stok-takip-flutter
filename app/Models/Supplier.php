<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Bu tedarikçiye ait ürünler
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Aktif tedarikçiler için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Tedarikçideki ürün sayısı
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Tam adres döndür
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->phone ? "Tel: " . $this->phone : null,
            $this->email ? "E-mail: " . $this->email : null,
        ]);
        
        return implode(' | ', $parts);
    }
}
