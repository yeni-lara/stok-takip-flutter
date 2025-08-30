<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Kullanıcının rolü ile ilişki
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Kullanıcının stok hareketleri ile ilişki
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Kullanıcının belirli bir yetkiye sahip olup olmadığını kontrol eder
     */
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }

        $permissions = $this->role->permissions;
        
        // Permissions artık array olarak saklanıyor, sadece varlığını kontrol et
        return is_array($permissions) && in_array($permission, $permissions);
    }

    /**
     * Admin kontrolü
     */
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    /**
     * Aktif kullanıcı kontrolü
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Aktif kullanıcılar scope'u
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
