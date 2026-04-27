<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Guest;
use App\Models\Package;
use App\Models\Payment;
use App\Models\RestaurantMenu;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;

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

    public function menuCatalog()
    {
        $menus = RestaurantMenu::where('is_available', true)->get()->groupBy('category');
        return view('users.pages.restaurant', compact('menus'));
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
        $checkInDate = \Carbon\Carbon::parse($request->check_in);
        $checkOutDate = \Carbon\Carbon::parse($request->check_out);
        $totalNights = $checkInDate->diffInDays($checkOutDate);
        $subtotal = $availableRoom->roomType->price * $totalNights;

        // 1. Hitung Diskon Otomatis
        $autoDiscounts = \App\Models\Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $finalTotal = $subtotal - $autoDiscountAmount;

        // 2. Diskon Voucher Manual
        if ($request->filled('voucher_code')) {
            $voucher = \App\Models\Discount::where('code', $request->voucher_code)->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->first();
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
        return view('users.pages.checkout_restaurant');
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

    
}