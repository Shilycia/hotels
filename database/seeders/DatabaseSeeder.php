<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DiscountSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            RestaurantMenuSeeder::class,
            AdminUserSeeder::class,
            GuestSeeder::class,
            UserSeeder::class,
            PackageSeeder::class,
        ]);
    }
}

