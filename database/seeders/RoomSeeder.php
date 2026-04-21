<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil ID dari Room Type yang sudah dibuat sebelumnya
        $type1 = RoomType::where('name', 'Junior Suite')->first();
        $type2 = RoomType::where('name', 'Executive Suite')->first();
        $type3 = RoomType::where('name', 'Super Deluxe')->first();

        if ($type1) {
            Room::create(['room_type_id' => $type1->id, 'room_number' => '101', 'floor' => 1, 'status' => 'available']);
        }
        if ($type2) {
            Room::create(['room_type_id' => $type2->id, 'room_number' => '201', 'floor' => 2, 'status' => 'available']);
        }
        if ($type3) {
            Room::create(['room_type_id' => $type3->id, 'room_number' => '301', 'floor' => 3, 'status' => 'occupied']);
        }
    }
}