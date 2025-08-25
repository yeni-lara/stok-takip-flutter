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
            // Bireysel Müşteriler
            [
                'type' => 'individual',
                'name' => 'Ahmet',
                'surname' => 'Yılmaz',
                'company_name' => null,
                'phone' => '0532 123 45 67',
                'email' => 'ahmet.yilmaz@email.com',
                'address' => 'Atatürk Mahallesi, Cumhuriyet Caddesi No:15',
                'city' => 'İstanbul',
                'tax_number' => null,
                'tax_office' => null,
                'notes' => 'Düzenli müşteri',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'individual',
                'name' => 'Fatma',
                'surname' => 'Demir',
                'company_name' => null,
                'phone' => '0505 987 65 43',
                'email' => 'fatma.demir@email.com',
                'address' => 'Yenişehir Mahallesi, İnönü Bulvarı No:28',
                'city' => 'Ankara',
                'tax_number' => null,
                'tax_office' => null,
                'notes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'individual',
                'name' => 'Mehmet',
                'surname' => 'Kaya',
                'company_name' => null,
                'phone' => '0542 456 78 90',
                'email' => 'mehmet.kaya@email.com',
                'address' => 'Alsancak Mahallesi, Kordon Boyu No:45',
                'city' => 'İzmir',
                'tax_number' => null,
                'tax_office' => null,
                'notes' => 'Toptan alım yapar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Kurumsal Müşteriler
            [
                'type' => 'corporate',
                'name' => null,
                'surname' => null,
                'company_name' => 'ABC Teknoloji Ltd. Şti.',
                'phone' => '0212 555 12 34',
                'email' => 'info@abcteknoloji.com',
                'address' => 'Maslak Mahallesi, Teknoloji Caddesi No:10',
                'city' => 'İstanbul',
                'tax_number' => '1234567890',
                'tax_office' => 'Sarıyer',
                'notes' => 'Büyük kurumsal müşteri',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'corporate',
                'name' => null,
                'surname' => null,
                'company_name' => 'XYZ Market Zinciri A.Ş.',
                'phone' => '0312 444 56 78',
                'email' => 'siparis@xyzmarket.com',
                'address' => 'Çankaya Mahallesi, Ticaret Merkezi No:5',
                'city' => 'Ankara',
                'tax_number' => '0987654321',
                'tax_office' => 'Çankaya',
                'notes' => 'Düzenli toptan alım',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'corporate',
                'name' => null,
                'surname' => null,
                'company_name' => 'DEF Gıda San. ve Tic. Ltd. Şti.',
                'phone' => '0232 777 99 11',
                'email' => 'satin.alma@defgida.com',
                'address' => 'Bornova Organize Sanayi Bölgesi No:22',
                'city' => 'İzmir',
                'tax_number' => '5678901234',
                'tax_office' => 'Bornova',
                'notes' => 'Gıda sektörü müşterisi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('customers')->insert($customers);
    }
}
