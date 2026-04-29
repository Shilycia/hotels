<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Room availability queries
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['room_id', 'check_in_date', 'check_out_date', 'status']);
            $table->index('guest_id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index('room_type_id');
            $table->index('status');
        });

        // Package & Menu bundles
        Schema::table('packages', function (Blueprint $table) {
            $table->index('room_type_id');
            $table->index('restaurant_menu_id');
            $table->index('is_active');
        });

        Schema::table('paket_menu_items', function (Blueprint $table) {
            $table->index('paket_id');
            $table->index('menu_id');
        });

        // Orders & Payments
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->index('guest_id');
            $table->index('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['booking_id', 'payment_status']);
            $table->index(['restaurant_order_id', 'payment_status']);
            $table->index(['package_order_id', 'payment_status']);
        });

        // Discounts
        Schema::table('discounts', function (Blueprint $table) {
            $table->index(['is_active', 'valid_until']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'check_in_date', 'check_out_date', 'status']);
            $table->dropIndex('guest_id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('room_type_id');
            $table->dropIndex('status');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex('room_type_id');
            $table->dropIndex('restaurant_menu_id');
            $table->dropIndex('is_active');
        });

        Schema::table('paket_menu_items', function (Blueprint $table) {
            $table->dropIndex('paket_id');
            $table->dropIndex('menu_id');
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropIndex('guest_id');
            $table->dropIndex('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['booking_id', 'payment_status']);
            $table->dropIndex(['restaurant_order_id', 'payment_status']);
            $table->dropIndex(['package_order_id', 'payment_status']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'valid_until']);
            $table->dropIndex('code');
        });
    }
};
