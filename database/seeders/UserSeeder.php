<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
$adminRole = Role::where('slug', 'admin')->firstOrFail();
        $staffRole = Role::where('slug', 'staff')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'admin@hotelneo.com'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Bintang Staff',
                'password' => Hash::make('password'),
                'foto'     => 'img/team-1.jpg'
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@hotelneo.com'],
            [
                'role_id'  => $staffRole->id,
                'name'     => 'Fahrevi Staff',
                'password' => Hash::make('password'),
                'foto'     => 'img/team-2.jpg'
            ]
        );
    }
}