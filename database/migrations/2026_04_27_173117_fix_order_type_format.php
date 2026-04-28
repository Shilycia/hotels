<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Memaksa MySQL mengubah kolom menjadi VARCHAR(50) agar teks "room_service" muat dengan aman
        DB::statement("ALTER TABLE restaurant_orders MODIFY order_type VARCHAR(50) DEFAULT 'dine_in'");
    }

    public function down(): void
    {
        // Kosongkan saja down-nya
    }
};