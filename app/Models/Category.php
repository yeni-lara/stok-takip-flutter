<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Bu kategoriye ait ürünler
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Aktif kategoriler için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kategorideki ürün sayısı
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
}
