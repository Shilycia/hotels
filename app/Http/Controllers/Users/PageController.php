<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RestaurantMenu;          // 🟢 Import Model Restoran
use App\Models\RestaurantOrder;         // 🟢 Import Model Restoran
use App\Models\RestaurantOrderDetail;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function home()
    {
        $rooms = Room::with('roomType')->get();
        $staffs = User::with('role')->get();
        return view('users.pages.home', compact('rooms', 'staffs'));
    }

    public function about() { return view('users.pages.about'); }
    public function services() { return view('users.pages.services'); }
    public function team() { return view('users.pages.team'); }
    public function testimonial() { return view('users.pages.testimonial'); }
    public function contact() { return view('users.pages.contact'); }

    public function rooms(Request $request)
    {
        // 1. Mulai Query
        $query = Room::with('roomType')->where('status', 'available');

        // 2. Filter Kapasitas
        if ($request->filled('adult')) {
            $query->whereHas('roomType', function($q) use ($request) {
                $q->where('adult_capacity', '>=', $request->adult);
            });
        }
        if ($request->filled('child')) {
            $query->whereHas('roomType', function($q) use ($request) {
                $q->where('child_capacity', '>=', $request->child);
            });
        }

        // 3. Filter Ketersediaan Tanggal
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = Carbon::parse($request->check_in)->format('Y-m-d H:i:s');
            $checkOut = Carbon::parse($request->check_out)->format('Y-m-d H:i:s');

            $query->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn);
            });
        }

        // 4. Fitur Sorting (Pengurutan)
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    // Join ke room_types untuk mengurutkan berdasarkan harga
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                        ->orderBy('room_types.price', 'asc')
                        ->select('rooms.*');
                    break;
                case 'price_high':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                        ->orderBy('room_types.price', 'desc')
                        ->select('rooms.*');
                    break;
                case 'rating':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                        ->orderBy('room_types.rating', 'desc')
                        ->select('rooms.*');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 5. Pagination dengan menjaga Query String (agar filter tidak hilang saat pindah page)
        $rooms = $query->paginate(6)->withQueryString();

        return view('users.pages.rooms', compact('rooms'));
    }

    public function roomDetail($id)
    {
        $room = Room::with('roomType')->findOrFail($id);
        $room->name = $room->roomType->name ?? 'Tipe Kamar Dihapus';
        $room->price = $room->roomType->price ?? 0;
        $room->bed_type = $room->roomType->bed_type ?? '-';
        $room->bath_count = $room->roomType->bath_count ?? 0;
        $room->rating = $room->roomType->rating ?? 5;
        $room->description = $room->roomType->description ?? 'Deskripsi belum tersedia.';
        $room->image = $room->roomType->foto ? $room->roomType->foto : 'img/room-1.jpg';

        return view('users.pages.room-detail', compact('room'));
    }

    public function booking(Request $request)
    {
        $roomsRaw = Room::with('roomType')->where('status', 'available')->get();
        
        $rooms = $roomsRaw->map(function($room) {
            $room->name = $room->roomType->name ?? 'Kamar Standard';
            $room->price = $room->roomType->price ?? 0;
            return $room;
        });

        return view('users.pages.booking', compact('rooms'));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'room_id' => 'required|exists:rooms,id',
            'adult' => 'required|integer|min:1',
            'child' => 'required|integer|min:0',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // 3. 🛡️ Proteksi Double Booking
        $isBooked = Booking::where('room_id', $request->room_id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in']) 
            ->where('check_in', '<', $checkOut->format('Y-m-d H:i:s'))
            ->where('check_out', '>', $checkIn->format('Y-m-d H:i:s'))
            ->exists();

        if ($isBooked) {
            return back()->withInput()->withErrors([
                'room_id' => 'Maaf, kamar ini sudah dipesan pada tanggal tersebut. Silakan pilih tanggal atau kamar lain.'
            ]);
        }

        $guestId = session('guest_id');
        if (!$guestId) {
            $guest = Guest::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name, 
                    'phone' => '-', 
                    'password' => bcrypt('password123') 
                ]
            );
            $guestId = $guest->id;
            session(['guest_id' => $guest->id, 'guest_name' => $guest->name]);
        }

        $room = Room::with('roomType')->findOrFail($request->room_id);

        $adultCapacity = $room->roomType->adult_capacity ?? 2;
        $childCapacity = $room->roomType->child_capacity ?? 1;

        if ($request->adult > $adultCapacity) {
            return back()->withInput()->withErrors([
                'adult' => 'Kamar ini hanya muat untuk maksimal ' . $adultCapacity . ' orang dewasa.'
            ]);
        }
        
        if ($request->child > $childCapacity) {
            return back()->withInput()->withErrors([
                'child' => 'Kamar ini hanya muat untuk maksimal ' . $childCapacity . ' anak-anak.'
            ]);
        }
        
        $days = max(1, $checkIn->diffInDays($checkOut));
        $totalPrice = ($room->roomType->price ?? 0) * $days;

        $paymentId = DB::transaction(function () use ($request, $guestId, $totalPrice, $checkIn, $checkOut) {
            $booking = Booking::create([
                'guest_id'        => $guestId,
                'room_id'         => $request->room_id,
                'check_in'        => $checkIn->format('Y-m-d H:i:s'),
                'check_out'       => $checkOut->format('Y-m-d H:i:s'),
                'status'          => 'pending',
                'total_price'     => $totalPrice,
                'special_request' => $request->special_request,
            ]);

            $payment = Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $totalPrice,
                'payment_status' => 'pending',
                'payment_method' => 'transfer',
            ]);

            return $payment->id;
        });

        return redirect()->route('guest.pay', $paymentId)
                        ->with('success', 'Booking berhasil! Silakan selesaikan pembayaran Anda.');
    }

    public function restaurant(Request $request)
    {
        $query = RestaurantMenu::query();
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search')) $query->where('name', 'like', '%' . $request->search . '%');

        $menus = $query->where('is_available', true)->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // 🟢 TAMBAHAN: Kirim data kamar agar Dropdown Room ID di Modal tidak kosong
        $rooms = Room::where('status', 'occupied')->get(); 
        $activeBooking = null;
        if (session('guest_id')) {
            $activeBooking = Booking::with('room')->where('guest_id', session('guest_id'))
                                    ->whereIn('status', ['confirmed', 'checked_in'])->first();
        }

        return view('users.pages.restaurant', compact('menus', 'rooms', 'activeBooking'));
    }

    // 🟢 1. Menampilkan Detail Menu
    public function menuDetail($id)
    {
        $menu = RestaurantMenu::findOrFail($id);
        $relatedMenus = RestaurantMenu::where('category', $menu->category)->where('id', '!=', $id)->take(3)->get();
        $rooms = Room::where('status', 'occupied')->get(); 
        
        $activeBooking = null;
        if (session('guest_id')) {
            $activeBooking = Booking::with('room')->where('guest_id', session('guest_id'))
                                    ->whereIn('status', ['confirmed', 'checked_in'])->first();
        }

        return view('users.pages.menu-detail', compact('menu', 'relatedMenus', 'rooms', 'activeBooking'));
    }

    // 🟢 2. Memproses Order (Charge to Room)
    // 🟢 2. Memproses Order (Charge to Room)
    // 🟢 2. Memproses Order (Pembayaran Terpisah)
    public function storeRestaurantOrder(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:restaurant_menus,id',
            'booking_id' => 'required|exists:bookings,id', // 🟢 Validasi wajib ada booking_id
            'qty' => 'required|integer|min:1',
        ]);

        $guestId = session('guest_id');
        if (!$guestId) return redirect()->route('guest.login')->with('error', 'Silakan login sebagai tamu.');

        // 🟢 Pastikan booking valid dan aktif milik tamu ini
        $booking = Booking::where('id', $request->booking_id)
                          ->where('guest_id', $guestId)
                          ->whereIn('status', ['confirmed', 'checked_in'])
                          ->firstOrFail();

        $paymentId = DB::transaction(function () use ($request, $guestId, $booking) {
            $menu = RestaurantMenu::findOrFail($request->menu_id);
            $subtotal = $menu->price * $request->qty;
            $totalPrice = $subtotal + round($subtotal * 0.05); // Total + Service Charge 5%

            $order = RestaurantOrder::create([
                'guest_id' => $guestId,
                'room_id' => $booking->room_id, // 🟢 Ambil otomatis dari data kamar yang dipesan
                'total_price' => $totalPrice,
                'status' => 'placed', 
                'notes' => $request->notes 
            ]);

            RestaurantOrderDetail::create([
                'restaurant_order_id' => $order->id,
                'restaurant_menu_id' => $menu->id,
                'quantity' => $request->qty, 
                'price' => $menu->price,
            ]);

            // 🟢 PEMBAYARAN TERPISAH: Set jadi 'transfer' / 'pending' (bukan lagi charge_to_room)
            $payment = Payment::create([
                'restaurant_order_id' => $order->id,
                'amount' => $totalPrice,
                'payment_status' => 'pending',
                'payment_method' => 'transfer', 
            ]);

            return $payment->id;
        });

        // 🟢 Arahkan langsung ke halaman pembayaran (seperti saat booking kamar)
        return redirect()->route('guest.pay', $paymentId)
                         ->with('success', 'Pesanan restoran berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    // 🟢 3. Menampilkan Halaman Konfirmasi Order
    public function orderConfirmation($id)
    {
        $order = RestaurantOrder::with(['guest', 'room', 'details.menu'])->findOrFail($id);
        return view('users.pages.order-confirmation', compact('order'));
    }
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return response()->json([
            'message' => 'Terima kasih telah berlangganan! Kami akan mengirimkan info terbaru ke email Anda.'
        ]);
    }

    public function sendContact(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $emailBody = "Anda mendapatkan pesan baru dari form Contact Us Hotel Neo:\n\n";
        $emailBody .= "Nama: " . $request->name . "\n";
        $emailBody .= "Email: " . $request->email . "\n";
        $emailBody .= "Subjek: " . $request->subject . "\n\n";
        $emailBody .= "Isi Pesan:\n" . $request->message . "\n";

        \Illuminate\Support\Facades\Mail::raw($emailBody, function ($mail) use ($request) {
            $adminEmail = config('hotel.email', 'info@hotelier.com'); 
            
            $mail->to($adminEmail)
                 ->subject('Pesan Web Hotel Neo: ' . $request->subject)
                 ->replyTo($request->email, $request->name);
        });

        // 4. Kembalikan ke halaman form dengan pesan sukses
        return back()->with('success', 'Terima kasih, ' . $request->name . '! Pesan Anda telah kami terima dan terkirim ke tim kami.');
    }

    // 🟢 Menampilkan Profil & Riwayat Transaksi Tamu
    public function guestProfile()
    {
        // Pastikan tamu sudah login (punya session)
        $guestId = session('guest_id');
        if (!$guestId) {
            return redirect()->route('guest.login')->with('error', 'Silakan login untuk melihat profil dan riwayat transaksi Anda.');
        }

        // Ambil data tamu
        $guest = Guest::findOrFail($guestId);

        // Ambil riwayat booking kamar beserta tagihannya
        $bookings = Booking::with(['room.roomType', 'payment'])
                            ->where('guest_id', $guestId)
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Ambil riwayat pesanan restoran beserta detail dan tagihannya
        $restaurantOrders = RestaurantOrder::with(['details.menu', 'payment'])
                                           ->where('guest_id', $guestId)
                                           ->orderBy('created_at', 'desc')
                                           ->get();

        return view('users.pages.profile', compact('guest', 'bookings', 'restaurantOrders'));
    }
}