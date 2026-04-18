<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        // Data Tamu Pertama
        Guest::create([
            'identity_number' => '3171234567890001', // Tambahkan baris ini
            'name' => 'Bintang Adryan',
            'email' => 'bintang.putra@example.com',
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia'
        ]);

        // Data Tamu Kedua
        Guest::create([
            'identity_number' => '3279876543210002', // Tambahkan baris ini
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@example.com',
            'phone' => '089876543210',
            'address' => 'Depok, Jawa Barat'
        ]);
    }
}