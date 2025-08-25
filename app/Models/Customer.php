<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Bu müşteriye ait stok hareketleri
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Aktif müşteriler için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Müşteri adı (basit)
     */
    public function getNameAttribute()
    {
        return $this->company_name;
    }

    /**
     * Toplam stok çıkışı
     */
    public function getTotalStockOutAttribute()
    {
        return $this->stockMovements()->where('type', 'çıkış')->sum('quantity');
    }

    /**
     * Toplam stok iadesi
     */
    public function getTotalStockReturnAttribute()
    {
        return $this->stockMovements()->where('type', 'iade')->sum('quantity');
    }

    /**
     * Son işlem tarihi
     */
    public function getLastTransactionDateAttribute()
    {
        $lastMovement = $this->stockMovements()->latest()->first();
        return $lastMovement ? $lastMovement->created_at : null;
    }

    /**
     * Müşteri aktif mi?
     */
    public function isActive()
    {
        return $this->is_active;
    }
}
