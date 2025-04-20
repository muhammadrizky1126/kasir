<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('members')->insert([
                'id' => Str::uuid(),
                'member_code' => 'MBR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => 'Member ' . $i,
                'phone_number' => '08' . rand(1000000000, 9999999999),
                'email' => 'member' . $i . '@example.com',
                'address' => 'Jl. Contoh Alamat No. ' . $i,
                'date_of_birth' => now()->subYears(rand(18, 40))->subDays(rand(1, 365))->toDateString(),
                'points' => rand(0, 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
