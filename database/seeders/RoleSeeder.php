<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator']
        );
        Role::firstOrCreate(
            ['slug' => 'staff'],
            ['name' => 'Staff Resepsionis']
        );
    }
}