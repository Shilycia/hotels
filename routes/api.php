<?php

use Illuminate\Support\Facades\Route;

// Import Controller dari Namespace Api
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RestaurantOrderController;
use App\Http\Controllers\Api\WebhookController;

// --- Public API Routes ---
// Endpoint untuk daftar kamar (biasanya tamu ingin lihat kamar sebelum login)
Route::get('/rooms', [RoomController::class, 'index']);

// Webhook Midtrans (Harus bisa diakses publik oleh server Midtrans)
Route::post('/midtrans/callback', [WebhookController::class, 'midtransCallback']);

// --- Protected API Routes (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Booking Kamar
    Route::post('/bookings', [BookingController::class, 'store']);

    // Pembayaran Midtrans
    Route::post('/payments/midtrans-token', [PaymentController::class, 'createMidtransToken']);

    // Pesanan Restoran
    Route::post('/restaurant/orders', [RestaurantOrderController::class, 'store']);
});