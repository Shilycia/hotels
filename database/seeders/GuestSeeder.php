<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;

class GuestSeeder extends Seeder
{
    public function run(): void
    {
        Guest::create([
            'name'            => 'Bintang Putra Adryan',
            'email'           => 'bintang@gmail.com',
            'password'        => Hash::make('password'),
            'phone'           => '081234567890',
            'identity_number' => '3201010101010001',
            'address'         => 'Cibinong, Bogor',
            'photo_url'       => 'img/testimonial-1.jpg'
        ]);

        Guest::create([
            'name'            => 'Baghas Tamu',
            'email'           => 'baghas@gmail.com',
            'password'        => Hash::make('password'),
            'phone'           => '087766554433',
            'identity_number' => '3201010101010003',
            'address'         => 'Depok',
            'photo_url'       => 'img/testimonial-3.jpg'
        ]);
    }
}