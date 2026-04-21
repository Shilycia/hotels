<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        Role::create(['name' => 'Staff Resepsionis', 'slug' => 'staff']);
    }
}