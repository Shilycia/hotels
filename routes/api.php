<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RestaurantOrderController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\RoomAdminController;
use App\Http\Controllers\Api\Admin\BookingAdminController;
use App\Http\Controllers\Api\Admin\MenuAdminController;
use App\Http\Controllers\Api\Admin\OrderAdminController;
use App\Http\Controllers\Api\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/midtrans/callback', [PaymentController::class, 'callback']); // Webhook Midtrans

/*
|--------------------------------------------------------------------------
| Protected Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Guest & Room Info
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::apiResource('guests', GuestController::class);

    // Transactions
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/restaurant/order', [RestaurantOrderController::class, 'store']);
    Route::post('/payments/snap-token', [PaymentController::class, 'createSnapToken']);

    /*
    |--------------------------------------------------------------------------
    | Admin Specific Routes (Role Admin Required)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        // CRUD Management
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('rooms', RoomAdminController::class);
        Route::apiResource('bookings', BookingAdminController::class);
        Route::apiResource('menus', MenuAdminController::class);
        Route::apiResource('orders', OrderAdminController::class);

        // Advanced Admin Tools
        Route::get('/midtrans/status/{order_id}', [ReportController::class, 'checkMidtransStatus']);
        Route::get('/reports/income', [ReportController::class, 'incomeReport']);
        Route::get('/reports/occupancy', [ReportController::class, 'occupancyReport']);
    });
});