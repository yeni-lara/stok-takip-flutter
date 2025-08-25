<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'category_id',
        'supplier_id',
        'unit_price',
        'tax_rate',
        'current_stock',
        'min_stock',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Ürünün kategorisi ile ilişki
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Ürünün tedarikçisi ile ilişki
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Ürünün stok hareketleri ile ilişki
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Aktif ürünler için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Düşük stoklu ürünler için scope
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock');
    }

    /**
     * KDV dahil fiyat hesapla
     */
    public function getPriceWithTaxAttribute()
    {
        return $this->unit_price * (1 + ($this->tax_rate / 100));
    }

    /**
     * Stok durumu kontrol et
     */
    public function isLowStock()
    {
        return $this->current_stock <= $this->min_stock;
    }

    /**
     * Stok güncelle
     */
    public function updateStock($quantity, $type, $userId, $note = null, $referenceNumber = null)
    {
        $previousStock = $this->current_stock;

        switch ($type) {
            case 'giriş':
                $newStock = $previousStock + $quantity;
                break;
            case 'çıkış':
                $newStock = $previousStock - $quantity;
                break;
            case 'iade':
                $newStock = $previousStock + $quantity;
                break;
            default:
                throw new \InvalidArgumentException('Geçersiz hareket tipi: ' . $type);
        }

        // Stok negatif olamaz
        if ($newStock < 0) {
            throw new \Exception('Stok miktarı negatif olamaz.');
        }

        // Stok hareketini kaydet
        StockMovement::create([
            'product_id' => $this->id,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'note' => $note,
            'reference_number' => $referenceNumber,
        ]);

        // Ürün stokunu güncelle
        $this->update(['current_stock' => $newStock]);

        return $this;
    }

    /**
     * Toplam stok değeri (KDV dahil)
     */
    public function getTotalValueAttribute()
    {
        return $this->current_stock * $this->price_with_tax;
    }

    /**
     * Ürün resmi URL'si
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }
        return asset('images/no-image.svg'); // Varsayılan resim
    }

    /**
     * Resim var mı kontrolü
     */
    public function hasImage()
    {
        return !empty($this->image_path) && file_exists(public_path($this->image_path));
    }
}
