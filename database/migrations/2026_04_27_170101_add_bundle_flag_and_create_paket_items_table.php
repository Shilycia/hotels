<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kondisi/flag di tabel menu
        Schema::table('restaurant_menus', function (Blueprint $table) {
            // true = bisa dimasukkan ke paket kamar, false = khusus beli terpisah
            $table->boolean('can_bundle_with_room')->default(false)->after('is_available');
        });

        // 2. Buat tabel penghubung untuk relasi Paket -> Isi Menu
        Schema::create('paket_menu_items', function (Blueprint $table) {
            $table->id();
            // paket_id mengambil dari ID restaurant_menus (yang kategorinya 'paket')
            $table->foreignId('paket_id')->constrained('restaurant_menus')->onDelete('cascade');
            // menu_id mengambil dari ID restaurant_menus (yang kategorinya food/drink dll)
            $table->foreignId('menu_id')->constrained('restaurant_menus')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_menu_items');
        
        Schema::table('restaurant_menus', function (Blueprint $table) {
            $table->dropColumn('can_bundle_with_room');
        });
    }
};