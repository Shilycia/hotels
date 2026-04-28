<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_order_details', function (Blueprint $table) {
            // Tambahkan kolom notes jika belum ada
            if (!Schema::hasColumn('restaurant_order_details', 'notes')) {
                $table->text('notes')->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_order_details', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_order_details', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};