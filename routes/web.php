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
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// KEMBALIKAN NAMA RUTE KAMAR:
Route::get('/rooms/{id}', [PageController::class, 'roomDetail'])->name('rooms.show'); 

// TAMBAHKAN 2 RUTE MENU INI:
Route::get('/restaurant', [PageController::class, 'menuCatalog'])->name('menus');
Route::get('/restaurant/{id}', [PageController::class, 'menuDetail'])->name('menu.detail');

// --- Otentikasi Tamu (Hanya untuk yang BELUM login) ---
Route::middleware('guest.guest')->group(function () {
    Route::get('/login', [GuestAuthController::class, 'showLoginForm'])->name('guest.login');
    Route::post('/login', [GuestAuthController::class, 'login'])->name('guest.login.submit');
    Route::get('/register', [GuestAuthController::class, 'showRegisterForm'])->name('guest.register');
    Route::post('/register', [GuestAuthController::class, 'register'])->name('guest.register.submit');
    Route::get('/forgot-password', [GuestAuthController::class, 'forgotPassword'])->name('guest.forgot');
});

// --- Area Privat Tamu (Wajib Login Guest) ---
Route::middleware('guest.auth')->group(function () { 
    Route::post('/logout', [GuestAuthController::class, 'logout'])->name('guest.logout');
    
    // Profil & Riwayat
    Route::get('/profile', [GuestAuthController::class, 'profile'])->name('guest.profile');
    Route::put('/profile/update', [GuestAuthController::class, 'updateProfile'])->name('guest.profile.update');
    Route::get('/profile/edit', [PageController::class, 'editProfile'])->name('guest.profile.edit');

    // --- Area Restoran & Keranjang ---
    Route::post('/restaurant/cart/add', [PageController::class, 'addToRestaurantCart'])->name('restaurant.cart.add');
    Route::get('/checkout/restaurant', [PageController::class, 'checkoutRestaurant'])->name('checkout.restaurant');
    Route::post('/checkout/restaurant/remove', [PageController::class, 'removeFromRestaurantCart'])->name('restaurant.cart.remove');
    Route::post('/restaurant/order/store', [PageController::class, 'storeRestaurantOrder'])->name('restaurant.order.store');
    
    // Proses Reservasi & Pesanan
    Route::get('/checkout/room', [PageController::class, 'checkoutRoom'])->name('checkout.room');
    Route::post('/checkout/apply-voucher', [PageController::class, 'applyVoucher'])->name('voucher.apply'); 
    Route::post('/booking/store', [PageController::class, 'storeBooking'])->name('booking.store');
    Route::get('/checkout/restaurant', [PageController::class, 'checkoutRestaurant'])->name('checkout.restaurant');
    Route::get('/package/{package}/customize', [PageController::class, 'customizePackage'])->name('package.customize');
    Route::post('/package/store', [PageController::class, 'storePackageOrder'])->name('package.store');    

    Route::get('/payment/{id}', [GuestPaymentController::class, 'showPayment'])->name('guest.payment.show');
    Route::post('/payment/{id}/status', [GuestPaymentController::class, 'updateStatus'])->name('guest.pay.status');
    // Pembayaran Midtrans
    Route::post('/payment/process', [GuestPaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/invoice/{payment}', [PageController::class, 'invoice'])->name('guest.invoice');
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
        Route::resource('roles', RoleController::class);
    });
}); 

/*
|--------------------------------------------------------------------------
| 3. WEBHOOK MIDTRANS
|--------------------------------------------------------------------------
*/
Route::post('/webhook/midtrans/callback', [PaymentController::class, 'webhookCallback']);