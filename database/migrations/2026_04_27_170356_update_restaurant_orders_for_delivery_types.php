<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            
            // 1. Cek dan tangani kolom booking_id secara cerdas
            if (Schema::hasColumn('restaurant_orders', 'booking_id')) {
                // Jika sudah ada, ubah menjadi boleh kosong (nullable)
                $table->unsignedBigInteger('booking_id')->nullable()->change();
            } else {
                // Jika belum ada sama sekali, buatkan kolom barunya
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            }
            
            // 2. Tambahkan kolom tipe pesanan dan nomor meja dengan aman
            if (!Schema::hasColumn('restaurant_orders', 'order_type')) {
                $table->enum('order_type', ['room_service', 'dine_in', 'takeaway'])->default('dine_in');
            }
            
            if (!Schema::hasColumn('restaurant_orders', 'table_number')) {
                $table->string('table_number')->nullable();
            }
            
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_orders', 'order_type')) {
                $table->dropColumn('order_type');
            }
            if (Schema::hasColumn('restaurant_orders', 'table_number')) {
                $table->dropColumn('table_number');
            }
            
          
        });
    }
};