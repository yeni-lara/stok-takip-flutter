<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'company_name' => 'ABC Market',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_name' => 'XYZ Gıda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_name' => 'Deneme Firması',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_name' => 'Test Şirketi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_name' => 'Örnek Ltd.',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('customers')->insert($customers);
    }
}
