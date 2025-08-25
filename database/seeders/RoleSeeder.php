<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role; // Added this import for Role model

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Yönetici',
                'description' => 'Tüm yetkilere sahip sistem yöneticisi',
                'permissions' => [
                    'user_management',
                    'role_management',
                    'product_management',
                    'category_management',
                    'supplier_management',
                    'customer_management',
                    'stock_entry',
                    'stock_exit',
                    'stock_return',
                    'stock_count',
                    'stock_movement_management',
                    'view_reports',
                    'export_reports',
                    'settings_management',
                    'admin'
                ],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'yardımcı',
                'display_name' => 'Yardımcı Müdür',
                'description' => 'Sınırlı yetkilere sahip yardımcı müdür',
                'permissions' => [
                    'product_management',
                    'category_management',
                    'supplier_management',
                    'customer_management',
                    'stock_entry',
                    'stock_exit',
                    'stock_return',
                    'stock_count',
                    'view_reports'
                ],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'teslimat_elemanı',
                'display_name' => 'Teslimat Elemanı',
                'description' => 'Sadece stok çıkış ve iade yetkisi olan kullanıcı',
                'permissions' => [
                    'stock_exit',
                    'stock_return',
                    'customer_management' // Müşteri seçebilsin diye
                ],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }
    }
}
