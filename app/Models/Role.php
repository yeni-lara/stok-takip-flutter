<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Bu role sahip kullanıcılar
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Belirli bir izni kontrol et
     */
    public function hasPermission($permission)
    {
        return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
    }

    /**
     * Aktif roller için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
