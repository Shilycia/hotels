<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('identity_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        
    }
};
