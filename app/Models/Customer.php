<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'surname',
        'company_name',
        'phone',
        'email',
        'address',
        'city',
        'tax_number',
        'tax_office',
        'notes',
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
     * Bireysel müşteriler için scope
     */
    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    /**
     * Kurumsal müşteriler için scope
     */
    public function scopeCorporate($query)
    {
        return $query->where('type', 'corporate');
    }

    /**
     * Müşteri tam adı
     */
    public function getFullNameAttribute()
    {
        if ($this->type === 'corporate') {
            return $this->company_name;
        }
        
        return trim($this->name . ' ' . $this->surname);
    }

    /**
     * Müşteri görüntü adı (liste vs. için)
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->full_name;
        
        if ($this->type === 'corporate') {
            $name .= ' (Kurumsal)';
        }
        
        return $name;
    }

    /**
     * Müşteri tipinin Türkçe karşılığı
     */
    public function getTypeTextAttribute()
    {
        return $this->type === 'individual' ? 'Bireysel' : 'Kurumsal';
    }

    /**
     * İletişim bilgileri (telefon, email)
     */
    public function getContactInfoAttribute()
    {
        $contacts = [];
        
        if ($this->phone) {
            $contacts[] = $this->phone;
        }
        
        if ($this->email) {
            $contacts[] = $this->email;
        }
        
        return implode(' | ', $contacts);
    }

    /**
     * Tam adres
     */
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->address) {
            $address[] = $this->address;
        }
        
        if ($this->city) {
            $address[] = $this->city;
        }
        
        return implode(', ', $address);
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

    /**
     * Kurumsal müşteri mi?
     */
    public function isCorporate()
    {
        return $this->type === 'corporate';
    }

    /**
     * Bireysel müşteri mi?
     */
    public function isIndividual()
    {
        return $this->type === 'individual';
    }
}
