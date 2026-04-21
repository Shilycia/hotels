<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->onDelete('cascade');
            
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['placed', 'preparing', 'on_the_way', 'delivered', 'paid'])->default('placed');
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};