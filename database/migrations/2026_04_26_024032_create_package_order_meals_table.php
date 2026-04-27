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
        Schema::create('package_order_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_order_id')->constrained('package_orders')->onDelete('cascade');
            $table->foreignId('restaurant_menu_id')->constrained('restaurant_menus')->onDelete('cascade');
            $table->enum('meal_time', ['breakfast', 'lunch', 'dinner', 'anytime']);
            $table->date('date');
            $table->tinyInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_order_meals');
    }
};
