<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test kullanıcısı oluştur (admin/1234)
        User::updateOrCreate(
            ['email' => 'admin@stoktakip.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('1234'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test kullanıcısı oluşturuldu: admin@test.com / 1234');
    }
}
