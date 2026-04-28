<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable(); 
            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_transaction_amount', 12, 2)->nullable();
            $table->enum('applicable_to', ['bookings', 'restaurant_orders', 'package_orders', 'all'])->nullable();
            $table->boolean('is_stackable')->default(false); 
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            
            // [D-01] FIX: Tambahkan limitasi penggunaan
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};