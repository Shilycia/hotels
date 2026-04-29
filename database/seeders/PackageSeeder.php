<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\RestaurantMenu;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // Sample packages after menus/rooms are seeded
        $roomTypes = RoomType::all();
        $bundleMenus = RestaurantMenu::where('category', 'paket')->get();
        $foodMenus = RestaurantMenu::whereIn('category', ['food', 'drink'])->where('can_bundle_with_room', true)->get();

        if ($roomTypes->isEmpty()) {
            $this->command->info('No room types found. Skipping PackageSeeder.');
            return;
        }

        Package::create([
            'room_type_id' => $roomTypes->first()->id,
            'name' => 'Paket Staycation Weekend',
            'description' => '2 malam Superior Room + sarapan + dinner spesial + late check-out.',
            'total_price' => 2500000,
            'is_active' => true,
        ]);

        // Link to bundle menus if exist
        if (!$bundleMenus->isEmpty()) {
            $pkg = Package::latest()->first();
            $pkg->restaurantMenus()->attach($bundleMenus->random(2)->pluck('id'));
        }

        $this->command->info('Sample packages created.');
    }
}
