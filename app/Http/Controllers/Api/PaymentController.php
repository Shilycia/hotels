<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Payment; 

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createMidtransToken(Request $request) 
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
        ]);

        $orderId = 'HOTEL-' . uniqid();
        $user = Auth::user();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Guest',
                'email'      => $user->email ?? 'guest@hotelneo.com',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            Payment::create([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'note' => 'Order ID: ' . $orderId
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}