<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin rolünün ID'sini al
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@stoktakip.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'role_id' => $adminRole->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
