<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'display_name' => 'Admin (Genel Müdür)',
                'description' => 'Tüm yetkilere sahip genel müdür',
                'permissions' => json_encode([
                    'user_management' => true,
                    'product_management' => true,
                    'category_management' => true,
                    'supplier_management' => true,
                    'stock_entry' => true,
                    'stock_exit' => true,
                    'stock_return' => true,
                    'stock_count' => true,
                    'reports_view' => true,
                    'reports_export' => true,
                    'system_settings' => true
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'yardımcı',
                'display_name' => 'Yardımcı (Yardımcı Müdür)',
                'description' => 'Sınırlı yetkilere sahip yardımcı müdür',
                'permissions' => json_encode([
                    'user_management' => false,
                    'product_management' => true,
                    'category_management' => true,
                    'supplier_management' => true,
                    'stock_entry' => true,
                    'stock_exit' => true,
                    'stock_return' => true,
                    'stock_count' => true,
                    'reports_view' => true,
                    'reports_export' => false,
                    'system_settings' => false
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'teslimat_elemanı',
                'display_name' => 'Teslimat Elemanı (Şoför)',
                'description' => 'Sadece stok çıkış ve iade yetkisi olan teslimat elemanı',
                'permissions' => json_encode([
                    'user_management' => false,
                    'product_management' => false,
                    'category_management' => false,
                    'supplier_management' => false,
                    'stock_entry' => false,
                    'stock_exit' => true,
                    'stock_return' => true,
                    'stock_count' => false,
                    'reports_view' => false,
                    'reports_export' => false,
                    'system_settings' => false
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('roles')->insert($roles);
    }
}
