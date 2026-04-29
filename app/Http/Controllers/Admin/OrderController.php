<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use App\Models\RestaurantMenu;
use App\Models\Guest;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = RestaurantOrder::with(['guest', 'details.menu', 'payment'])->latest()->get();
        $guests = Guest::all();
        $menus = RestaurantMenu::where('is_available', true)->get();

        return view('admin.order.index', compact('orders', 'guests', 'menus'));
    }

    public function store(Request $request)
    {
        // [BUG-03] FIX: Mengganti in_room menjadi room_service agar sesuai dengan enum di database
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'order_type' => 'required|in:dine_in,takeaway,room_service',
            'table_or_room' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'menu_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
        ]);

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->menu_id as $index => $menuId) {
            $menu = RestaurantMenu::findOrFail($menuId);
            $quantity = $request->qty[$index];
            $subtotal = $menu->price * $quantity;
            
            $totalAmount += $subtotal;

            $orderItems[] = [
                'restaurant_menu_id' => $menu->id,
                'quantity' => $quantity,
                'unit_price' => $menu->price,
                'subtotal' => $subtotal,
            ];
        }

        $order = RestaurantOrder::create([
            'guest_id' => $request->guest_id,
            'order_type' => $request->order_type,
            // [BUG-03] FIX: Kolom aslinya di database bernama table_number
            'table_number' => $request->table_or_room,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
            'status' => 'pending', 
        ]);

        foreach ($orderItems as $item) {
            $order->details()->create($item);
        }

        Payment::create([
            'restaurant_order_id' => $order->id,
            'amount' => $totalAmount,
            'payment_status' => 'pending',
            'payment_method' => 'cash', 
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan restoran berhasil dibuat dan diteruskan ke dapur!');
    }

    public function update(Request $request, RestaurantOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,served,completed'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan dapur diperbarui!');
    }

    // Menghapus pesanan dan seluruh detailnya
    public function destroy(RestaurantOrder $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan restoran dihapus!');
    }
}