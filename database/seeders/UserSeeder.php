<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder untuk Pemilik
        User::create([
            'name' => 'Bapak Pemilik',
            'email' => 'pemilik@kos.com',
            'password' => Hash::make('password123'),
            'role' => 'pemilik',
            'status' => 'aktif',
        ]);

        // Seeder untuk Penyewa
        User::create([
            'name' => 'Mas Penyewa',
            'email' => 'penyewa@kos.com',
            'password' => Hash::make('password123'),
            'role' => 'penyewa',
            'status' => 'aktif',
        ]);
    }
}
