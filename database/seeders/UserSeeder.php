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
        //
        DB::table('users')->insert([
            [
                'username' => 'super_admin',
                'name' => 'Super Administrator',
                'email' => 'super_admin@example.com',
                'password' => Hash::make('secret.1'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'admin',
                'name' => 'Admin Toko',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin_toko',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'owner',
                'name' => 'Owner Toko',
                'email' => 'owner@example.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kasir1',
                'name' => 'Kasir Utama',
                'email' => 'kasir@example.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
