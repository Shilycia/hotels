<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderDetail;
use App\Models\Payment; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'items'    => 'required|array',
            'items.*.restaurant_menu_id' => 'required|exists:restaurant_menus,id',
            'items.*.quantity'           => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $order = RestaurantOrder::create([
                'guest_id'    => $request->guest_id,
                'total_price' => 0, 
                'status'      => 'ordered',
            ]);

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $menu = \App\Models\RestaurantMenu::find($item['restaurant_menu_id']);
                $subtotal = $menu->price * $item['quantity'];
                
                RestaurantOrderDetail::create([
                    'restaurant_order_id' => $order->id,
                    'restaurant_menu_id'  => $menu->id,
                    'quantity'            => $item['quantity'],
                    'price'               => $menu->price,
                ]);

                $totalPrice += $subtotal;
            }

            // Update total harga orderan
            $order->update(['total_price' => $totalPrice]);

            Payment::create([
                'restaurant_order_id' => $order->id,
                'amount'              => $totalPrice, 
                'payment_status'      => 'pending',   
                'payment_method'      => 'transfer', 
            ]);

            return response()->json(['message' => 'Pesanan restoran berhasil dibuat beserta tagihannya.', 'data' => $order->load('details')], 201);
        });
    }
}