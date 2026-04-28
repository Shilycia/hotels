<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            
            // [B-04] FIX: Memisahkan table_or_room menjadi dua kolom yang jelas
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade'); 
            $table->string('table_number', 20)->nullable();
            
            // [B-04] FIX ENUM: Langsung gunakan nilai yang benar sejak awal
            $table->enum('order_type', ['dine_in', 'takeaway', 'room_service']);
            
            $table->decimal('total_amount', 12, 2);
            $table->text('notes')->nullable();
            
            // BONUS FIX: Tambahkan 'cancelled' ke enum status agar sinkron dengan fitur gagal bayar Midtrans!
            $table->enum('status', ['pending', 'preparing', 'served', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};