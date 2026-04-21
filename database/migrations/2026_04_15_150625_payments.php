<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('restaurant_order_id')->nullable()->constrained('restaurant_orders')->onDelete('cascade');
            
            $table->decimal('amount', 12, 2);
            
            $table->enum('payment_method', ['cash', 'transfer', 'credit_card', 'e_wallet', 'charge_to_room']);
            
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};