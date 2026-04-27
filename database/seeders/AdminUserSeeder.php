<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Role Super Admin (dan beberapa role standar lainnya)
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin']
        );

        Role::firstOrCreate(
            ['slug' => 'resepsionis'],
            ['name' => 'Resepsionis']
        );

        Role::firstOrCreate(
            ['slug' => 'restoran'],
            ['name' => 'Staf Restoran']
        );

        // 2. Buat Akun Super Admin Utama
        User::firstOrCreate(
            ['email' => 'admin@hotelneo.com'], // Patokan agar tidak duplikat jika di-seed ulang
            [
                'name' => 'Administrator Utama',
                'role_id' => $superAdminRole->id,
                'password' => Hash::make('password123'), // Default password
            ]
        );
    }
}