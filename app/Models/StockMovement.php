<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'note',
        'reference_number',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
    ];

    /**
     * Hareketin ait olduğu ürün
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Hareketi yapan kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hareketin ait olduğu müşteri (sadece çıkış ve iade için)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Belirli bir tarih aralığında scope
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Belirli bir hareket tipine göre scope
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Belirli bir ürüne göre scope
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Belirli bir kullanıcıya göre scope
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Hareket tipinin rengini döndür
     */
    public function getTypeColorAttribute()
    {
        switch ($this->type) {
            case 'giriş':
                return 'success';
            case 'çıkış':
                return 'danger';
            case 'iade':
                return 'warning';
            default:
                return 'secondary';
        }
    }

    /**
     * Hareket tipinin simgesini döndür
     */
    public function getTypeIconAttribute()
    {
        switch ($this->type) {
            case 'giriş':
                return 'bi-arrow-up-circle';
            case 'çıkış':
                return 'bi-arrow-down-circle';
            case 'iade':
                return 'bi-arrow-repeat';
            default:
                return 'bi-circle';
        }
    }
}
