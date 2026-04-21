<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderDetail;
use App\Models\Guest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = RestaurantMenu::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menus = $query->orderBy('name', 'asc')->get();
        return view('users.pages.restaurant', compact('menus'));
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'table_number' => 'nullable|string', 
            'menu_id' => 'required|exists:restaurant_menus,id',
            'qty' => 'required|integer|min:1',
        ]);

        $guestId = session('guest_id');
        if (!$guestId) {
            $guest = Guest::firstOrCreate(
                ['email' => $request->email],
                ['name' => $request->name, 'phone' => '-', 'password' => bcrypt('password123')]
            );
            $guestId = $guest->id;
            session(['guest_id' => $guest->id, 'guest_name' => $guest->name]);
        }

        $paymentId = DB::transaction(function () use ($request, $guestId) {
            
            $menu = RestaurantMenu::findOrFail($request->menu_id);
            $totalPrice = $menu->price * $request->qty;

            $order = RestaurantOrder::create([
                'guest_id' => $guestId,
                'total_price' => $totalPrice,
                'status' => 'ordered',
            ]);

            RestaurantOrderDetail::create([
                'restaurant_order_id' => $order->id,
                'restaurant_menu_id' => $menu->id,
                'quantity' => $request->qty,
                'price' => $menu->price,
            ]);

            $payment = Payment::create([
                'restaurant_order_id' => $order->id,
                'amount' => $totalPrice,
                'payment_status' => 'pending',
                'payment_method' => 'transfer',
            ]);

            return $payment->id;
        });

        return redirect()->route('guest.pay', $paymentId)
                         ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
    }
}