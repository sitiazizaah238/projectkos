<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;   // <-- INI WAJIB

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kos.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
    }
}
