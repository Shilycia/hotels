<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Middleware\RedirectIfAdminLoggedIn;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Users\GuestPaymentController;
use App\Http\Controllers\Users\PageController;
use App\Http\Controllers\Users\GuestAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectIfGuestLoggedIn;

// ==========================================
// --- PUBLIC ROUTES ---
// ==========================================
Route::get('/', function () {
    return redirect()->route('home');
});

// ==========================================
// --- GUEST AUTHENTICATION ROUTES ---
// ==========================================

Route::middleware([RedirectIfAdminLoggedIn::class])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout Admin (Harus di luar middleware)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🟢 Rute yang DIBLOKIR jika GUEST sudah login
Route::middleware([RedirectIfGuestLoggedIn::class])->group(function () {
    // Login
    Route::get('/guest/login', [GuestAuthController::class, 'showLogin'])->name('guest.login');
    Route::post('/guest/login', [GuestAuthController::class, 'login'])->name('guest.login.submit');
    
    // Register
    Route::get('/guest/register', [GuestAuthController::class, 'showRegister'])->name('guest.register');
    Route::post('/guest/register', [GuestAuthController::class, 'register'])->name('guest.register.submit');
    
    // Reset Password
    Route::get('/guest/forgot-password', [GuestAuthController::class, 'showForgot'])->name('guest.forgot');
    Route::post('/guest/forgot-password', [GuestAuthController::class, 'sendResetLink'])->name('guest.password.email');
    Route::get('/guest/reset-password/{token}', [GuestAuthController::class, 'showResetForm'])->name('guest.password.reset');
    Route::post('/guest/reset-password', [GuestAuthController::class, 'resetPassword'])->name('guest.password.update');
});

// Logout Guest (Harus di luar middleware atas agar bisa diakses saat sudah login)
Route::post('/guest/logout', [GuestAuthController::class, 'logout'])->name('guest.logout');

// Profil & Riwayat Transaksi Tamu
Route::get('/guest/profile', [PageController::class, 'guestProfile'])->name('guest.profile');


// ==========================================
// --- ADMIN AUTHENTICATION ROUTES ---
// ==========================================

// Logout Admin (Harus di luar middleware 'guest')
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================
// --- PROTECTED ADMIN ROUTES ---
// ==========================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen User
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); 
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Manajemen Kamar
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Manajemen Tipe Kamar
    Route::get('/room-types', [RoomTypeController::class, 'index'])->name('room-types');
    Route::post('/room-types', [RoomTypeController::class, 'store'])->name('room-types.store');
    Route::put('/room-types/{roomType}', [RoomTypeController::class, 'update'])->name('room-types.update');
    Route::delete('/room-types/{roomType}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');

    // Manajemen Menu Restoran
    Route::get('/menus', [MenuController::class, 'index'])->name('menus');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

    // Manajemen Order Restoran
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Manajemen Booking
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store'); 
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Laporan & Midtrans
    Route::get('/reports/income', [ReportController::class, 'incomeReport'])->name('reports.income');
    Route::get('/midtrans/status/{orderId}', [ReportController::class, 'checkMidtransStatus'])->name('midtrans.status');

    // Payment Admin
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Manajemen Role
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});


// ==========================================
// --- MIDTRANS & PAYMENT GUEST ROUTES ---
// ==========================================
Route::post('/admin/midtrans/callback', [PaymentController::class, 'midtransCallback']);
Route::get('/pay/{payment}', [GuestPaymentController::class, 'show'])->name('guest.pay');
Route::post('/pay/{payment}/status', [GuestPaymentController::class, 'updateFrontendStatus'])->name('guest.pay.status');


// ==========================================
// --- PUBLIC FRONTEND ROUTES (GUEST) ---
// ==========================================

// Halaman Informasi
Route::get('/home', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/testimonial', [PageController::class, 'testimonial'])->name('testimonial');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::post('/newsletter/subscribe', [PageController::class, 'subscribe'])->name('newsletter.subscribe');

// Katalog Kamar
Route::get('/rooms', [PageController::class, 'rooms'])->name('rooms');
Route::get('/rooms/{id}', [PageController::class, 'roomDetail'])->name('room.detail');

// Rute Booking 
Route::get('/booking', [PageController::class, 'booking'])->name('booking');
Route::post('/booking', [PageController::class, 'storeBooking'])->name('booking.store');

// ==========================================
// --- FRONTEND RESTAURANT ROUTES ---
// ==========================================
Route::get('/menus', [PageController::class, 'restaurant'])->name('menus');
Route::get('/menus/{id}', [PageController::class, 'menuDetail'])->name('menu.detail');
Route::post('/menus/order', [PageController::class, 'storeRestaurantOrder'])->name('menus.order'); 
Route::get('/menus/confirmation/{id}', [PageController::class, 'orderConfirmation'])->name('orders.confirmation');