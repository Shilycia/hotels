<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        RoomType::create([
            'name'           => 'Junior Suite',
            'description'    => 'Kamar elegan dengan pemandangan kota yang menawan.',
            'price'          => 1000000,
            'foto'           => 'img/room-1.jpg',
            'rating'         => 4,
            'bed_type'       => 'King Bed',
            'bath_count'     => 1,
            'adult_capacity' => 2,
            'child_capacity' => 1
        ]);

        RoomType::create([
            'name'           => 'Executive Suite',
            'description'    => 'Kemewahan ekstra dengan fasilitas premium untuk bisnis.',
            'price'          => 1500000,
            'foto'           => 'img/room-2.jpg',
            'rating'         => 5,
            'bed_type'       => 'Twin Bed',
            'bath_count'     => 2,
            'adult_capacity' => 2,
            'child_capacity' => 2
        ]);

        RoomType::create([
            'name'           => 'Super Deluxe',
            'description'    => 'Puncak kemewahan dengan balkon pribadi dan jacuzzi.',
            'price'          => 2000000,
            'foto'           => 'img/room-3.jpg',
            'rating'         => 5,
            'bed_type'       => 'Queen Bed',
            'bath_count'     => 2,
            'adult_capacity' => 3,
            'child_capacity' => 2
        ]);
    }
}