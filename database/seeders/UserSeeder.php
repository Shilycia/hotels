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
        $adminRole = Role::where('slug', 'admin')->first();
        $staffRole = Role::where('slug', 'staff')->first();

        User::create([
            'role_id'  => $adminRole->id,
            'name'     => 'Bintang Admin',
            'email'    => 'admin@hotelneo.com',
            'password' => Hash::make('password'),
            'foto'     => 'img/team-1.jpg'
        ]);

        User::create([
            'role_id'  => $staffRole->id,
            'name'     => 'Fahrevi Staff',
            'email'    => 'staff@hotelneo.com',
            'password' => Hash::make('password'),
            'foto'     => 'img/team-2.jpg'
        ]);
    }
}