<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Discount;
use App\Models\Guest;
use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\PackageOrderMeal;
use App\Models\Payment;
use App\Models\RestaurantMenu;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderDetail;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $activeDiscounts = Discount::where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->get();

        $featuredRooms = RoomType::limit(3)->get();

        $featuredMenus = RestaurantMenu::where('is_available', true)->limit(4)->get();

        $packages = Package::with('roomType')->where('is_active', true)->limit(3)->get();

        $staffs = User::with('role')->limit(4)->get();
        
        return view('users.pages.home', compact(
            'activeDiscounts', 'featuredRooms', 'featuredMenus', 'packages', 'staffs'
        ));
    }

    public function about()
    {
        return view('users.pages.about');
    }

    public function roomCatalog(Request $request)
    {
        $query = RoomType::query();
        
        if ($request->filled('adults')) {
            $query->where('adult_capacity', '>=', $request->adults);
        }
        if ($request->filled('children')) {
            $query->where('child_capacity', '>=', $request->children);
        }

        $roomTypes = $query->get();
        return view('users.pages.rooms', compact('roomTypes'));
    }

    // Menampilkan halaman detail tipe kamar
    public function roomDetail($id)
    {
        $roomType = RoomType::findOrFail($id);
        
        $activeDiscounts = Discount::where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->where(function($query) {
                $query->where('applicable_to', 'all')
                      ->orWhere('applicable_to', 'room');
            })
            ->get();

        return view('users.pages.room-detail', compact('roomType', 'activeDiscounts'));
    }

    public function applyVoucher(Request $request)
    {
        $voucher = Discount::where('code', $request->code)->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->whereIn('applicable_to', ['all', 'bookings'])->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak valid atau kedaluwarsa.']);
        }
        if ($voucher->min_transaction_amount && $request->subtotal < $voucher->min_transaction_amount) {
            return response()->json(['success' => false, 'message' => 'Minimal transaksi belum terpenuhi.']);
        }

        // Hitung nominal voucher
        $voucherAmount = ($voucher->discount_type == 'percentage') ? ($request->subtotal * $voucher->discount_value / 100) : $voucher->discount_value;

        // Logika Penggabungan (Stackable)
        if ($voucher->is_stackable) {
            $finalTotal = $request->subtotal - $request->auto_discount_amount - $voucherAmount;
            $msg = 'Voucher berhasil digabungkan dengan promo otomatis!';
        } else {
            // Jika tidak bisa digabung, voucher ini MENGGANTIKAN promo otomatis
            $finalTotal = $request->subtotal - $voucherAmount;
            $msg = 'Voucher diterapkan! (Promo otomatis dibatalkan karena tidak dapat digabung).';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'voucher_amount' => $voucherAmount,
            'is_stackable' => $voucher->is_stackable,
            'final_total' => max(0, $finalTotal) // Harga tidak boleh minus
        ]);
    }

    public function menuCatalog(Request $request)
    {
        $query = RestaurantMenu::with('paketItems');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menus = $query->get();
        return view('users.pages.restaurant', compact('menus'));
    }

    public function menuDetail($id)
    {
        // 2. Tambahkan with('paketItems') di sini juga
        $menu = RestaurantMenu::with('paketItems')->findOrFail($id);
        
        $relatedMenus = RestaurantMenu::where('category', $menu->category)
            ->where('id', '!=', $id)
            ->where('is_available', true)
            ->limit(3)
            ->get();

        $activeBooking = null;
        if(session()->has('guest_id')) {
            $activeBooking = Booking::with('room')->where('guest_id', session('guest_id'))
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->first();
        }

        return view('users.pages.menu-detail', compact('menu', 'relatedMenus', 'activeBooking'));
    }

    // ==========================================
    // AREA PRIVAT TAMU (WAJIB LOGIN)
    // ==========================================

    public function profile()
    {
        $guestId = session('guest_id');
        
        $guest = Guest::with([
            'bookings.room.roomType', 
            'restaurantOrders.details.menu'
        ])->findOrFail($guestId);

        $bookings = $guest->bookings;
        $restaurantOrders = $guest->restaurantOrders;

        return view('users.pages.profile', compact('guest', 'bookings', 'restaurantOrders'));
    }

    public function storeBooking(Request $request)
    {
        if (!session()->has('guest_id')) {
            return redirect()->route('guest.login')->with('error', 'Sesi habis. Silakan masuk kembali.');
        }

        $request->validate([
            'room_type_id'    => 'required|exists:room_types,id',
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
            'special_request' => 'nullable|string|max:500',
        ]);

        // CEK KAMAR FISIK
        $availableRoom = Room::where('room_type_id', $request->room_type_id)->first();
        if (!$availableRoom) {
            // INI YANG MEMBUAT TOMBOL ANDA TERDIAM SEBELUMNYA!
            return back()->with('error', 'Mohon maaf, belum ada alokasi kamar fisik untuk tipe kamar ini di sistem hotel.');
        }

        // HITUNG ULANG DI SERVER
        $checkInDate = Carbon::parse($request->check_in);
        $checkOutDate = Carbon::parse($request->check_out);
        $totalNights = $checkInDate->diffInDays($checkOutDate);
        $subtotal = $availableRoom->roomType->price * $totalNights;

        // 1. Hitung Diskon Otomatis
        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $finalTotal = $subtotal - $autoDiscountAmount;

        // 2. Diskon Voucher Manual
        if ($request->filled('voucher_code')) {
            $voucher = Discount::where('code', $request->voucher_code)->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->first();
            if ($voucher) {
                $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;
                $finalTotal = ($voucher->is_stackable) ? ($subtotal - $autoDiscountAmount - $voucherAmount) : ($subtotal - $voucherAmount);
            }
        }

        // SIMPAN KE DATABASE
        $booking = new Booking();
        $booking->guest_id = session('guest_id');
        $booking->room_id = $availableRoom->id;
        $booking->check_in_date = $request->check_in;
        $booking->check_out_date = $request->check_out;
        $booking->total_nights = $totalNights;
        $booking->total_amount = max(0, $finalTotal); 
        $booking->status = 'pending'; 
        $booking->special_request = $request->special_request;
        $booking->save();

        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->amount = $booking->total_amount;
        $payment->payment_status = 'pending';
        $payment->payment_method = 'midtrans';
        $payment->save();

        return redirect()->route('guest.payment.show', $payment->id)->with('success', 'Reservasi berhasil dibuat! Silakan bayar.');
    }

    public function storePackageOrder(Request $request)
    {
        $request->validate([
            'package_id'      => 'required|exists:packages,id',
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
        ]);

        $package = Package::findOrFail($request->package_id);

        // 1. Hitung total harga dasar paket
        $totalAmount = $package->total_price;

        // 2. Tambahkan harga menu ekstra jika tamu mencentangnya
        if ($request->has('extra_menus')) {
            $extraMenus = RestaurantMenu::whereIn('id', $request->extra_menus)->get();
            foreach ($extraMenus as $menu) {
                // Asumsi: tamu memesan 1 porsi untuk setiap menu ekstra yang dicentang
                $totalAmount += $menu->price; 
            }
        }

        // 3. Simpan Pesanan Paket ke Database
        $order = new PackageOrder();
        $order->guest_id     = session('guest_id');
        $order->package_id   = $package->id;
        
        // PENYESUAIAN NAMA KOLOM:
        $order->start_date   = $request->check_in; 
        $order->end_date     = $request->check_out;
        // ($order->special_request kita hapus karena tidak ada di database)
        
        $order->total_amount = $totalAmount;
        $order->status       = 'pending';
        $order->save();

        // 4. (Opsional) Jika Anda ingin menyimpan ekstra makanan ke relasi PackageOrderMeal
        if ($request->has('extra_menus')) {
            foreach ($request->extra_menus as $menuId) {
                PackageOrderMeal::create([
                    'package_order_id'   => $order->id,
                    'restaurant_menu_id' => $menuId,
                    'date'               => $request->check_in, // Makanan dikirim pada hari Check-in
                    'meal_time'          => 'dinner',           // Waktu makan default (misal: dinner/lunch/breakfast)
                    'quantity'           => 1,
                ]);
            }
        }

        // 5. Buatkan Tagihan Pembayaran
        $payment = new Payment();
        $payment->package_order_id = $order->id; 
        $payment->amount           = $totalAmount;
        $payment->payment_status   = 'pending';
        $payment->payment_method   = 'midtrans';
        $payment->save();

        // 6. Arahkan ke halaman pembayaran
        return redirect()->route('guest.payment.show', $payment->id)
                         ->with('success', 'Pesanan Paket berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function contact()
    {
        return view('users.pages.contact');
    }

    public function checkoutRoom(Request $request)
    {
        if (!session()->has('guest_id')) return redirect()->route('guest.login')->with('error', 'Silakan masuk terlebih dahulu.');
        if (!$request->check_in || !$request->check_out || !$request->adults || !$request->room_type_id) {
            return redirect()->route('rooms.index')->with('error', 'Data tidak lengkap.');
        }

        $roomType = RoomType::findOrFail($request->room_type_id);
        $guest = Guest::findOrFail(session('guest_id'));

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        
        $subtotal = $roomType->price * $nights;

        // 1. Cek Diskon Otomatis (Yang tidak punya kode voucher)
        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->whereIn('applicable_to', ['all', 'bookings'])->get();

        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $totalPrice = $subtotal - $autoDiscountAmount;

        return view('users.pages.checkout', compact(
            'roomType', 'guest', 'checkIn', 'checkOut', 'nights', 'subtotal', 'autoDiscountAmount', 'totalPrice', 'request'
        ));
    }

    public function checkoutRestaurant(Request $request)
    {
        $cart = session()->get('restaurant_cart', []);
        
        if(empty($cart)) {
            return redirect()->route('menus')->with('error', 'Keranjang Anda masih kosong.');
        }

        // Cek kamar aktif (untuk mengaktifkan/menonaktifkan opsi "Pesan ke Kamar")
        $activeBooking = Booking::with('room')->where('guest_id', session('guest_id'))
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();

        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['qty'];

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->whereIn('applicable_to', ['all', 'restaurant_orders'])->get();
            
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $totalPrice = max(0, $subtotal - $autoDiscountAmount);

        return view('users.pages.checkout_restaurant', compact('cart', 'activeBooking', 'subtotal', 'autoDiscountAmount', 'totalPrice'));
    }

    public function customizePackage(Package $package)
    {
        $menus = RestaurantMenu::where('is_available', true)->get();
        return view('users.pages.customize_package', compact('package', 'menus'));
    }

    public function invoice(Payment $payment)
    {
        $guestId = session('guest_id');
        $isOwner = false;

        if ($payment->booking && $payment->booking->guest_id == $guestId) $isOwner = true;
        if ($payment->restaurantOrder && $payment->restaurantOrder->guest_id == $guestId) $isOwner = true;
        if ($payment->packageOrder && $payment->packageOrder->guest_id == $guestId) $isOwner = true;

        if (!$isOwner) {
            abort(403, 'Akses ditolak.');
        }

        return view('users.pages.invoice', compact('payment'));
    }

    public function editProfile()
    {
        $guestId = session('guest_id');
        $guest = Guest::findOrFail($guestId);

        return view('users.pages.edit_profile', compact('guest'));
    }

    public function addToRestaurantCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:restaurant_menus,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $menu = RestaurantMenu::findOrFail($request->menu_id);
        $cart = session()->get('restaurant_cart', []);

        // Jika menu sudah ada di keranjang, tambahkan qty-nya
        if(isset($cart[$menu->id])) {
            $cart[$menu->id]['qty'] += $request->qty;
            if($request->notes) {
                $cart[$menu->id]['notes'] = $cart[$menu->id]['notes'] . ', ' . $request->notes;
            }
        } else {
            // Jika belum ada, buat item baru
            $cart[$menu->id] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'foto' => $menu->foto_url,
                'qty' => $request->qty,
                'notes' => $request->notes
            ];
        }

        session()->put('restaurant_cart', $cart);
        return back()->with('success', $menu->name . ' berhasil ditambahkan ke keranjang!');
    }

    // 2. HAPUS DARI KERANJANG
    public function removeFromRestaurantCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('restaurant_cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('restaurant_cart', $cart);
            }
        }
        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    // 3. TAMPILKAN HALAMAN CHECKOUT KERANJANG
    

    // 4. SIMPAN PESANAN RESTORAN KE DATABASE
    public function storeRestaurantOrder(Request $request)
    {
        $cart = session()->get('restaurant_cart', []);
        if(empty($cart)) return redirect()->route('menus');

        // Validasi Opsi Pengiriman
        $request->validate([
            'order_type' => 'required|in:room_service,dine_in,takeaway',
            'table_number' => 'required_if:order_type,dine_in',
        ], [
            'table_number.required_if' => 'Nomor meja wajib diisi jika Anda makan di tempat.'
        ]);

        $activeBooking = Booking::where('guest_id', session('guest_id'))
            ->whereIn('status', ['confirmed', 'checked_in'])->first();

        // Validasi ketat keamanan: Jangan izinkan room_service kalau tidak punya kamar
        if($request->order_type == 'room_service' && !$activeBooking) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki kamar aktif untuk layanan Room Service.');
        }

        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['qty'];

        // Diskon otomatis
        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'restaurant_orders'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }
        $finalTotal = $subtotal - $autoDiscountAmount;

        // Diskon Voucher
        if ($request->filled('voucher_code')) {
            $voucher = Discount::where('code', $request->voucher_code)->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'restaurant_orders'])->first();
            if ($voucher) {
                $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;
                $finalTotal = ($voucher->is_stackable) ? ($subtotal - $autoDiscountAmount - $voucherAmount) : ($subtotal - $voucherAmount);
            }
        }

        // SIMPAN KE DATABASE
        $order = new RestaurantOrder();
        $order->guest_id = session('guest_id');
        $order->booking_id = ($request->order_type == 'room_service') ? $activeBooking->id : null;
        $order->order_type = $request->order_type;
        $order->table_number = ($request->order_type == 'dine_in') ? $request->table_number : null;
        $order->total_amount = max(0, $finalTotal);
        $order->status = 'pending';
        $order->save();

        foreach($cart as $item) {
            RestaurantOrderDetail::create([
                'restaurant_order_id' => $order->id,
                'restaurant_menu_id'  => $item['id'],
                'quantity'            => $item['qty'],
                'unit_price'          => $item['price'],
                'subtotal'            => $item['qty'] * $item['price'],
                'notes'               => $item['notes'] ?? null
            ]);
        }

        $payment = new Payment();
        $payment->restaurant_order_id = $order->id; 
        $payment->amount = $order->total_amount;
        $payment->payment_status = 'pending';
        $payment->payment_method = 'midtrans';
        $payment->save();

        session()->forget('restaurant_cart');

        return redirect()->route('guest.payment.show', $payment->id)->with('success', 'Pesanan Makanan berhasil dibuat!');
    }

    
}