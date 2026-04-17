<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\MenuController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\RoomController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () { return view('welcome'); });
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms');
    Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings');
    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus');
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments');
});