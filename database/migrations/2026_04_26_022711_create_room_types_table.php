<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 12, 2);
            $table->tinyInteger('adult_capacity');
            $table->tinyInteger('child_capacity');
            $table->text('description')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->string('bed_type', 100)->nullable(); 
            $table->tinyInteger('bath_count')->default(1);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};