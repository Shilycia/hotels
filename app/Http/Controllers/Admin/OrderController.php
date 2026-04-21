<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderDetail;
use App\Models\RestaurantMenu;
use App\Models\Guest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = RestaurantOrder::with(['guest', 'details.menu'])
                                 ->orderBy('created_at', 'desc')
                                 ->get(); 
        
        // Ambil data tamu dan menu untuk ditampilkan di form Tambah Order
        $guests = Guest::orderBy('name', 'asc')->get();
        $menus = RestaurantMenu::orderBy('name', 'asc')->get();

        return view('admin.order.index', compact('orders', 'guests', 'menus'));
    }

    // 🟢 FUNGSI BARU UNTUK MENAMBAH ORDER MANUAL 🟢
    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'menu_id' => 'required|array',
            'menu_id.*' => 'required|exists:restaurant_menus,id',
            'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat Order Utama
            $order = RestaurantOrder::create([
                'guest_id'    => $request->guest_id,
                'total_price' => 0, 
                'status'      => 'ordered',
            ]);

            $totalPrice = 0;

            // 2. Looping item yang dipesan
            foreach ($request->menu_id as $index => $menuId) {
                $qty = $request->qty[$index];
                $menu = RestaurantMenu::find($menuId);
                $subtotal = $menu->price * $qty;
                
                RestaurantOrderDetail::create([
                    'restaurant_order_id' => $order->id,
                    'restaurant_menu_id'  => $menu->id,
                    'quantity'            => $qty,
                    'price'               => $menu->price, // Kunci harga saat ini
                ]);

                $totalPrice += $subtotal;
            }

            // 3. Update Total Harga
            $order->update(['total_price' => $totalPrice]);

            // 4. Buat Tagihan (Payment) Otomatis
            Payment::create([
                'restaurant_order_id' => $order->id,
                'amount'              => $totalPrice,
                'payment_status'      => 'pending',
                'payment_method'      => 'transfer', 
            ]);
        });

        return redirect()->route('admin.orders')->with('success', 'Orderan restoran baru berhasil ditambahkan dan tagihan telah dibuat!');
    }

    public function update(Request $request, RestaurantOrder $order)
    {
        $request->validate(['status' => 'required|in:ordered,paid']);
        $order->update(['status' => $request->status]);

        $payment = Payment::where('restaurant_order_id', $order->id)->first();
        if ($payment) {
            if ($request->status === 'paid') {
                $payment->update(['payment_status' => 'paid']);
            } elseif ($request->status === 'ordered') {
                $payment->update(['payment_status' => 'pending']);
            }
        }
        return redirect()->route('admin.orders')->with('success', 'Status pesanan restoran dan tagihannya berhasil diperbarui!');
    }

    public function destroy(RestaurantOrder $order)
    {
        Payment::where('restaurant_order_id', $order->id)->delete();
        $order->delete();
        return redirect()->route('admin.orders')->with('success', 'Data pesanan dan riwayat tagihannya berhasil dihapus permanen.');
    }
}