<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['food', 'drink', 'dessert', 'snack', 'paket']);
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('foto_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('prep_time')->nullable(); 
            $table->integer('calories')->nullable();
            $table->string('allergens')->nullable(); 
            $table->string('serving')->nullable();
            $table->decimal('rating', 3, 1)->default(0.0); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_menus');
    }
};