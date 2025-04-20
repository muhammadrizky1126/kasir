<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'ADMIN',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'KARYAWAN',
            'email' => 'karyawan@gmail.com',
            'password' => Hash::make('1234'),
            'role' => 'user',
        ]);
    }
}
