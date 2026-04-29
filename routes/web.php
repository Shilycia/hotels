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

Route::get('/', [PageController::class, 'index'])->name('home'); 
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/rooms', [PageController::class, 'roomCatalog'])->name('rooms.index');
Route::get('/rooms/{id}', [PageController::class, 'roomDetail'])->name('rooms.show'); 

// [BUG-01 & MISS-03] FIX: Menambahkan route GET contact agar bisa diakses
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

// [BUG-02 & BUG-04] FIX: Menambahkan route packages index dan package show
Route::get('/packages', [PageController::class, 'packagesIndex'])->name('packages.index');
Route::get('/packages/{package}', [PageController::class, 'packageShow'])->name('package.show');

Route::get('/restaurant', [PageController::class, 'menuCatalog'])->name('menus');
Route::get('/restaurant/{id}', [PageController::class, 'menuDetail'])->name('menu.detail');

// --- Otentikasi Tamu (Hanya untuk yang BELUM login) ---
Route::middleware(['guest.guest'])->group(function () {
    Route::get('/login', [GuestAuthController::class, 'showLoginForm'])->name('guest.login');
    Route::get('/register', [GuestAuthController::class, 'showRegisterForm'])->name('guest.register');
    Route::get('/forgot-password', [GuestAuthController::class, 'forgotPassword'])->name('guest.forgot');

    // Keamanan: Proteksi Brute Force pada percobaan login/register
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/login', [GuestAuthController::class, 'login'])->name('guest.login.submit');
        Route::post('/register', [GuestAuthController::class, 'register'])->name('guest.register.submit');
    });
});

// --- Area Privat Tamu (Wajib Login Guest) ---
Route::middleware('guest.auth')->group(function () { 
    Route::post('/logout', [GuestAuthController::class, 'logout'])->name('guest.logout');
    Route::get('/profile', [GuestAuthController::class, 'profile'])->name('guest.profile');
    Route::put('/profile/update', [GuestAuthController::class, 'updateProfile'])->name('guest.profile.update');
    Route::get('/profile/edit', [PageController::class, 'editProfile'])->name('guest.profile.edit');

    // Area Restoran
    Route::post('/restaurant/cart/add', [PageController::class, 'addToRestaurantCart'])->name('restaurant.cart.add');
    Route::get('/checkout/restaurant', [PageController::class, 'checkoutRestaurant'])->name('checkout.restaurant');
    Route::post('/checkout/restaurant/remove', [PageController::class, 'removeFromRestaurantCart'])->name('restaurant.cart.remove');
    Route::post('/restaurant/order/store', [PageController::class, 'storeRestaurantOrder'])->name('restaurant.order.store');
    
    // Area Kamar & Paket
    Route::get('/checkout/room', [PageController::class, 'checkoutRoom'])->name('checkout.room');
    Route::post('/checkout/apply-voucher', [PageController::class, 'applyVoucher'])->name('voucher.apply'); 
    Route::post('/booking/store', [PageController::class, 'storeBooking'])->name('booking.store');
    Route::get('/package/{package}/customize', [PageController::class, 'customizePackage'])->name('package.customize');
    Route::post('/package/store', [PageController::class, 'storePackageOrder'])->name('package.store');    

    // Pembayaran & Invoice
    Route::get('/payment/{id}', [GuestPaymentController::class, 'showPayment'])->name('guest.payment.show');
    Route::post('/payment/{id}/status', [GuestPaymentController::class, 'updateStatus'])->name('guest.pay.status');
    
    // MISS-01 FIX: Route payment.process dibiarkan sementara atau dihapus, 
    // karena Anda menggunakan fungsi showPayment dan updateStatus yang sudah sempurna. 
    // Jika tidak digunakan, amannya biarkan saja / hapus.
    // Route::post('/payment/process', [GuestPaymentController::class, 'processPayment'])->name('payment.process');
});

/*
|--------------------------------------------------------------------------
| 2. PORTAL ADMIN (BACK-OFFICE)
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuth::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuth::class, 'login'])->middleware('throttle:5,1');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () { 
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::resource('room-types', RoomTypeController::class)->except(['create', 'edit', 'show']); 
    Route::resource('rooms', RoomController::class)->except(['create', 'edit', 'show']); 
    Route::resource('bookings', BookingController::class)->except(['create', 'edit', 'show']);
    Route::resource('menus', MenuController::class)->except(['create', 'edit', 'show']);
    Route::resource('packages', PackageController::class)->except(['create', 'edit', 'show']); 
    Route::resource('orders', OrderController::class)->except(['create', 'edit', 'show']);
    Route::resource('discounts', DiscountController::class)->except(['create', 'edit', 'show']); 
    
    // Rute Payment sudah benar dari awal (ditambah pengecualian 'store')
    Route::resource('payments', PaymentController::class)->except(['create', 'store', 'edit', 'show']);
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index'); 

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);
    });
});

// Webhook Midtrans (Satu Jalur Terpusat)
Route::post('/webhook/midtrans/callback', [PaymentController::class, 'webhookCallback']);