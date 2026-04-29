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
use Illuminate\Support\Facades\DB; // Ditambahkan untuk fungsi Transaction & Locking

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

    public function roomDetail($id)
    {
        $roomType = RoomType::findOrFail($id);
        
        $activeDiscounts = Discount::where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->where(function($query) {
                $query->where('applicable_to', 'all')
                      ->orWhere('applicable_to', 'bookings'); 
            })
            ->get();

        return view('users.pages.room-detail', compact('roomType', 'activeDiscounts'));
    }

    public function applyVoucher(Request $request)
    {
        $subtotal = 0;
        $applicableTo = 'all';

        if ($request->has('room_type_id') && $request->has('check_in') && $request->has('check_out')) {
            $roomType = RoomType::find($request->room_type_id);
            if ($roomType) {
                $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));
                $subtotal = $roomType->price * $nights;
                $applicableTo = 'bookings';
            }
        } elseif (session()->has('restaurant_cart')) {
            $cart = session()->get('restaurant_cart', []);
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }
            $applicableTo = 'restaurant_orders';
        }

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->whereIn('applicable_to', ['all', $applicableTo])->get();
            
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $voucher = Discount::where('code', $request->code)->where('is_active', true)
            ->whereDate('valid_until', '>=', now())
            ->whereIn('applicable_to', ['all', $applicableTo])->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak valid atau kedaluwarsa untuk pesanan ini.']);
        }
        
        if ($voucher->max_uses && $voucher->used_count >= $voucher->max_uses) {
            return response()->json(['success' => false, 'message' => 'Voucher gagal diterapkan karena sudah kehabisan kuota.']);
        }
        
        if ($voucher->min_transaction_amount && $subtotal < $voucher->min_transaction_amount) {
            return response()->json(['success' => false, 'message' => 'Minimal transaksi belum terpenuhi.']);
        }

        $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;

        if ($voucher->is_stackable) {
            $finalTotal = $subtotal - $autoDiscountAmount - $voucherAmount;
            $msg = 'Voucher berhasil digabungkan dengan promo otomatis!';
        } else {
            $finalTotal = $subtotal - $voucherAmount;
            $msg = 'Voucher diterapkan! (Promo otomatis dibatalkan karena tidak dapat digabung).';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'voucher_amount' => $voucherAmount,
            'is_stackable' => $voucher->is_stackable,
            'final_total' => max(0, $finalTotal)
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
        if (!session()->has('guest_id')) return redirect()->route('guest.login')->with('error', 'Sesi habis. Silakan masuk kembali.');

        $request->validate([
            'room_type_id'    => 'required|exists:room_types,id',
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
            'special_request' => 'nullable|string|max:500',
        ]);

        $checkInDate = Carbon::parse($request->check_in);
        $checkOutDate = Carbon::parse($request->check_out);

        $availableRoom = Room::where('room_type_id', $request->room_type_id)
            ->whereDoesntHave('bookings', function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                      ->where('check_in_date', '<', $checkOutDate->format('Y-m-d'))
                      ->where('check_out_date', '>', $checkInDate->format('Y-m-d'));
            })
            ->first();

        if (!$availableRoom) {
            return back()->with('error', 'Mohon maaf, kamar tipe ini sudah penuh untuk tanggal yang Anda pilih.');
        }

        $totalNights = $checkInDate->diffInDays($checkOutDate);
        $subtotal = $availableRoom->roomType->price * $totalNights;

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        try {
            // [W-03] FIX: Mulai DB Transaction untuk mencegah Race Condition (Double-Claim)
            $payment = DB::transaction(function () use ($request, $availableRoom, $checkInDate, $checkOutDate, $totalNights, $subtotal, $autoDiscountAmount) {
                $voucherAmount = 0;
                $usedVoucher = null;

                if ($request->filled('voucher_code')) {
                    // lockForUpdate() akan mengunci baris diskon ini sampai transaksi selesai
                    $voucher = Discount::where('code', $request->voucher_code)
                                ->where('is_active', true)
                                ->whereDate('valid_until', '>=', now())
                                ->whereIn('applicable_to', ['all', 'bookings'])
                                ->lockForUpdate() 
                                ->first();

                    if ($voucher) {
                        if ($voucher->max_uses && $voucher->used_count >= $voucher->max_uses) {
                            throw new \Exception('Pesanan gagal: Voucher promo yang Anda gunakan sudah kehabisan kuota.');
                        }
                        $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;
                        $usedVoucher = $voucher;
                    }
                }

                $finalTotal = $subtotal - $autoDiscountAmount;
                $totalDiscountApplied = $autoDiscountAmount;

                if ($usedVoucher) {
                    $finalTotal = $usedVoucher->is_stackable ? ($subtotal - $autoDiscountAmount - $voucherAmount) : ($subtotal - $voucherAmount);
                    $totalDiscountApplied = $usedVoucher->is_stackable ? ($autoDiscountAmount + $voucherAmount) : $voucherAmount;
                }

                $booking = new Booking();
                $booking->guest_id = session('guest_id');
                $booking->room_id = $availableRoom->id;
                $booking->check_in_date = $checkInDate;
                $booking->check_out_date = $checkOutDate;
                $booking->total_nights = $totalNights;
                $booking->total_amount = max(0, $finalTotal); 
                $booking->status = 'pending'; 
                $booking->special_request = $request->special_request;
                $booking->save();

                if ($usedVoucher) $usedVoucher->increment('used_count');

                $payment = new Payment();
                $payment->booking_id = $booking->id;
                $payment->amount = $booking->total_amount;
                $payment->discount_applied = $totalDiscountApplied; // [W-02] FIX
                $payment->payment_status = 'pending';
                $payment->payment_method = 'midtrans';
                $payment->save();

                return $payment;
            });

            return redirect()->route('guest.payment.show', $payment->id)->with('success', 'Kamar berhasil diamankan! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePackageOrder(Request $request)
    {
        $request->validate([
            'package_id'      => 'required|exists:packages,id',
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
        ]);

        $package = Package::with('roomType')->findOrFail($request->package_id);

        $checkInDate = Carbon::parse($request->check_in);
        $checkOutDate = Carbon::parse($request->check_out);

        $availableRoom = Room::where('room_type_id', $package->room_type_id)
            ->whereDoesntHave('bookings', function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                      ->where('check_in_date', '<', $checkOutDate->format('Y-m-d'))
                      ->where('check_out_date', '>', $checkInDate->format('Y-m-d'));
            })
            ->first();

        if (!$availableRoom) return back()->with('error', 'Kuota kamar untuk paket ini sudah penuh pada tanggal tersebut.');

        $subtotal = $package->total_price;

        if ($request->has('extra_menus')) {
            $extraMenus = RestaurantMenu::whereIn('id', $request->extra_menus)->get();
            foreach ($extraMenus as $menu) {
                $subtotal += $menu->price; 
            }
        }

        // [W-01] FIX: Kalkulasi Diskon Otomatis untuk Paket
        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'package_orders'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        try {
            $payment = DB::transaction(function () use ($request, $package, $availableRoom, $checkInDate, $checkOutDate, $subtotal, $autoDiscountAmount) {
                $voucherAmount = 0;
                $usedVoucher = null;

                // [W-01] FIX: Fitur Voucher Manual untuk Paket
                if ($request->filled('voucher_code')) {
                    $voucher = Discount::where('code', $request->voucher_code)
                                ->where('is_active', true)
                                ->whereDate('valid_until', '>=', now())
                                ->whereIn('applicable_to', ['all', 'package_orders'])
                                ->lockForUpdate() 
                                ->first();

                    if ($voucher) {
                        if ($voucher->max_uses && $voucher->used_count >= $voucher->max_uses) {
                            throw new \Exception('Pesanan gagal: Voucher promo yang Anda gunakan sudah kehabisan kuota.');
                        }
                        $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;
                        $usedVoucher = $voucher;
                    }
                }

                $finalTotal = $subtotal - $autoDiscountAmount;
                $totalDiscountApplied = $autoDiscountAmount;

                if ($usedVoucher) {
                    $finalTotal = $usedVoucher->is_stackable ? ($subtotal - $autoDiscountAmount - $voucherAmount) : ($subtotal - $voucherAmount);
                    $totalDiscountApplied = $usedVoucher->is_stackable ? ($autoDiscountAmount + $voucherAmount) : $voucherAmount;
                }

                $order = new PackageOrder();
                $order->guest_id     = session('guest_id');
                $order->package_id   = $package->id;
                $order->start_date   = $checkInDate; 
                $order->end_date     = $checkOutDate;
                $order->total_amount = max(0, $finalTotal);
                $order->status       = 'pending';
                $order->save();

                $booking = new Booking();
                $booking->guest_id       = session('guest_id');
                $booking->room_id        = $availableRoom->id;
                $booking->check_in_date  = $checkInDate;
                $booking->check_out_date = $checkOutDate;
                $booking->total_nights   = $checkInDate->diffInDays($checkOutDate);
                $booking->total_amount   = 0;
                $booking->status         = 'pending';
                $booking->special_request= 'Tamu Pemesan Paket: ' . $package->name;
                $booking->save();

                if ($request->has('extra_menus')) {
                    foreach ($request->extra_menus as $menuId) {
                        PackageOrderMeal::create([
                            'package_order_id'   => $order->id,
                            'restaurant_menu_id' => $menuId,
                            'date'               => $checkInDate,
                            'meal_time'          => 'dinner', 
                            'quantity'           => 1,
                        ]);
                    }
                }

                if ($usedVoucher) $usedVoucher->increment('used_count');

                $payment = new Payment();
                $payment->package_order_id = $order->id; 
                $payment->amount           = $order->total_amount;
                $payment->discount_applied = $totalDiscountApplied; // [W-02] FIX
                $payment->payment_status   = 'pending';
                $payment->payment_method   = 'midtrans';
                $payment->save();

                return $payment;
            });

            return redirect()->route('guest.payment.show', $payment->id)->with('success', 'Pesanan Paket berhasil dibuat! Silakan selesaikan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function contact()
    {
        return view('users.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);


        return redirect()->route('contact')->with('success', 'Terima kasih, ' . $request->name . '. Pesan Anda telah kami terima. Tim kami akan segera menghubungi Anda kembali.');
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

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'bookings'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        $totalPrice = max(0, $subtotal - $autoDiscountAmount);

        return view('users.pages.checkout_room', compact(
            'roomType', 'guest', 'checkIn', 'checkOut', 'nights', 'subtotal', 'autoDiscountAmount', 'totalPrice', 'request'
        ));
    }

    public function checkoutRestaurant(Request $request)
    {
        $cart = session()->get('restaurant_cart', []);
        if(empty($cart)) return redirect()->route('menus')->with('error', 'Keranjang Anda masih kosong.');

        $activeBooking = Booking::with('room')->where('guest_id', session('guest_id'))
            ->whereIn('status', ['confirmed', 'checked_in'])->first();

        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['qty'];

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'restaurant_orders'])->get();
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
        $guestName = 'Guest';

        if ($payment->booking && $payment->booking->guest_id == $guestId) {
            $isOwner = true;
            $guestName = $payment->booking->guest->name ?? 'Guest';
        }
        if ($payment->restaurantOrder && $payment->restaurantOrder->guest_id == $guestId) {
            $isOwner = true;
            $guestName = $payment->restaurantOrder->guest->name ?? 'Guest';
        }
        if ($payment->packageOrder && $payment->packageOrder->guest_id == $guestId) {
            $isOwner = true;
            $guestName = $payment->packageOrder->guest->name ?? 'Guest';
        }

        if (!$isOwner) abort(403, 'Akses ditolak.');

        // [N-03] FIX: Meng-generate Snap Token agar tombol "Bayar Sekarang" berfungsi
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $snapToken = null;
        if ($payment->payment_status == 'pending') {
            // Gunakan order ID yang sudah di-generate sebelumnya
            $orderId = $payment->midtrans_order_id;
            
            if ($orderId) {
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $payment->amount,
                    ],
                    'customer_details' => [
                        'first_name' => $guestName,
                    ]
                ];
                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                } catch (\Exception $e) {
                    // Biarkan null jika gagal terhubung
                }
            }
        }

        return view('users.pages.invoice', compact('payment', 'snapToken'));
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

        if(isset($cart[$menu->id])) {
            $cart[$menu->id]['qty'] += $request->qty;
            if($request->notes) {
                $cart[$menu->id]['notes'] = $cart[$menu->id]['notes'] . ', ' . $request->notes;
            }
        } else {
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

    public function storeRestaurantOrder(Request $request)
    {
        $cart = session()->get('restaurant_cart', []);
        if(empty($cart)) return redirect()->route('menus');

        $request->validate([
            'order_type' => 'required|in:room_service,dine_in,takeaway',
            'table_number' => 'required_if:order_type,dine_in',
        ], [
            'table_number.required_if' => 'Nomor meja wajib diisi jika Anda makan di tempat.'
        ]);

        $activeBooking = Booking::where('guest_id', session('guest_id'))->whereIn('status', ['confirmed', 'checked_in'])->first();

        if($request->order_type == 'room_service' && !$activeBooking) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki kamar aktif untuk layanan Room Service.');
        }

        $subtotal = 0;
        $verifiedCart = [];
        
        foreach($cart as $item) {
            $menu = RestaurantMenu::find($item['id']);
            if ($menu) {
                $subtotal += $menu->price * $item['qty'];
                $item['price'] = $menu->price; 
                $verifiedCart[] = $item;
            }
        }
        $cart = $verifiedCart;

        $autoDiscounts = Discount::whereNull('code')->where('is_active', true)->whereDate('valid_until', '>=', now())->whereIn('applicable_to', ['all', 'restaurant_orders'])->get();
        $autoDiscountAmount = 0;
        foreach($autoDiscounts as $d) {
            $autoDiscountAmount += ($d->discount_type == 'percentage') ? ($subtotal * $d->discount_value / 100) : $d->discount_value;
        }

        try {
            $payment = DB::transaction(function () use ($request, $activeBooking, $cart, $subtotal, $autoDiscountAmount) {
                $voucherAmount = 0;
                $usedVoucher = null;

                if ($request->filled('voucher_code')) {
                    $voucher = Discount::where('code', $request->voucher_code)
                                ->where('is_active', true)
                                ->whereDate('valid_until', '>=', now())
                                ->whereIn('applicable_to', ['all', 'restaurant_orders'])
                                ->lockForUpdate()
                                ->first();

                    if ($voucher) {
                        if ($voucher->max_uses && $voucher->used_count >= $voucher->max_uses) {
                            throw new \Exception('Pesanan gagal: Voucher promo sudah kehabisan kuota.');
                        }
                        $voucherAmount = ($voucher->discount_type == 'percentage') ? ($subtotal * $voucher->discount_value / 100) : $voucher->discount_value;
                        $usedVoucher = $voucher;
                    }
                }

                $finalTotal = $subtotal - $autoDiscountAmount;
                $totalDiscountApplied = $autoDiscountAmount;

                if ($usedVoucher) {
                    $finalTotal = $usedVoucher->is_stackable ? ($subtotal - $autoDiscountAmount - $voucherAmount) : ($subtotal - $voucherAmount);
                    $totalDiscountApplied = $usedVoucher->is_stackable ? ($autoDiscountAmount + $voucherAmount) : $voucherAmount;
                }

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

                if ($usedVoucher) $usedVoucher->increment('used_count');

                $payment = new Payment();
                $payment->restaurant_order_id = $order->id; 
                $payment->amount = $order->total_amount;
                $payment->discount_applied = $totalDiscountApplied; // [W-02] FIX
                $payment->payment_status = 'pending';
                $payment->payment_method = 'midtrans';
                $payment->save();

                return $payment;
            });

            session()->forget('restaurant_cart');
            return redirect()->route('guest.payment.show', $payment->id)->with('success', 'Pesanan Makanan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}