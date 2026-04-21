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
            $table->string('category'); // 🟢 Ini adalah kolom yang bikin error tadi
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('foto_url')->nullable(); // 🟢 Dan ini nama foto yang benar
            $table->boolean('is_available')->default(true);
            
            // Kolom detail
            $table->integer('prep_time')->default(15);
            $table->integer('calories')->nullable();
            $table->string('allergens')->nullable();
            $table->string('serving')->nullable();
            $table->float('rating')->default(5.0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_menus');
    }
};