<?php

use Illuminate\Support\Facades\Route;

// Import Admin Controllers
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

// Import Guest Controllers
use App\Http\Controllers\Users\GuestAuthController;
use App\Http\Controllers\Users\PageController;
use App\Http\Controllers\Users\GuestPaymentController;

/*
|--------------------------------------------------------------------------
| 1. PORTAL TAMU (FRONTEND)
|--------------------------------------------------------------------------
*/



// --- Akses Publik (Tanpa Login) ---
Route::get('/', [PageController::class, 'index'])->name('home'); 
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/rooms', [PageController::class, 'roomCatalog'])->name('rooms.index');
Route::get('/rooms/{roomType}', [PageController::class, 'roomDetail'])->name('rooms.show'); 
Route::get('/restaurant', [PageController::class, 'menuCatalog'])->name('restaurant.index'); 

// --- Otentikasi Tamu (Hanya untuk yang BELUM login) ---
Route::middleware('guest.guest')->group(function () {
    Route::get('/login', [GuestAuthController::class, 'showLoginForm'])->name('guest.login');
    Route::post('/login', [GuestAuthController::class, 'login'])->name('guest.login.submit');
    Route::get('/register', [GuestAuthController::class, 'showRegisterForm'])->name('guest.register');
    Route::post('/register', [GuestAuthController::class, 'register'])->name('guest.register.submit');
    Route::post('/logout', [GuestAuthController::class, 'logout'])->name('guest.logout');
    Route::get('/forgot-password', [GuestAuthController::class, 'forgotPassword'])->name('guest.forgot');
});

// --- Area Privat Tamu (Wajib Login Guest) ---
Route::middleware('guest.auth')->group(function () { 
    Route::post('/logout', [GuestAuthController::class, 'logout'])->name('guest.logout');
    
    // Profil & Riwayat [cite: 129, 130, 131, 132]
    // GANTI MENJADI INI:
    Route::get('/profile', [GuestAuthController::class, 'profile'])->name('guest.profile');
    Route::put('/profile/update', [GuestAuthController::class, 'updateProfile'])->name('guest.profile.update');
    // Proses Reservasi & Pesanan [cite: 121, 125, 126]
    Route::get('/checkout/room', [PageController::class, 'checkoutRoom'])->name('checkout.room');
    Route::get('/checkout/restaurant', [PageController::class, 'checkoutRestaurant'])->name('checkout.restaurant');
    Route::get('/package/{package}/customize', [PageController::class, 'customizePackage'])->name('package.customize');
    
    // Pembayaran Midtrans [cite: 127, 135]
    Route::post('/payment/process', [GuestPaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/invoice/{payment}', [PageController::class, 'invoice'])->name('guest.invoice');

    Route::get('/profile/edit', [PageController::class, 'editProfile'])->name('guest.profile.edit');
});

/*
|--------------------------------------------------------------------------
| 2. PORTAL ADMIN (BACK-OFFICE)
|--------------------------------------------------------------------------
*/

// --- Login Admin ---
Route::get('/admin/login', [AdminAuth::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuth::class, 'login']);

// --- Area Internal Admin (Wajib Login Admin) ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () { 
    
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::resource('room-types', RoomTypeController::class); 
    Route::resource('rooms', RoomController::class); 
    Route::resource('bookings', BookingController::class);

    Route::resource('menus', MenuController::class);
    Route::resource('packages', PackageController::class); 
    Route::resource('orders', OrderController::class);

    Route::resource('discounts', DiscountController::class); 
    Route::resource('payments', PaymentController::class)->except(['create', 'store', 'edit', 'show']);
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index'); 

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class); // <--- TAMBAHKAN BARIS INI
    });
}); 

/*
|--------------------------------------------------------------------------
| 3. WEBHOOK MIDTRANS
|--------------------------------------------------------------------------
*/
// Jalur ini dikecualikan dari CSRF di bootstrap/app.php [cite: 140]
Route::post('/webhook/midtrans/callback', [PaymentController::class, 'webhookCallback']);