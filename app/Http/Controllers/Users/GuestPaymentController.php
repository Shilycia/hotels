<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class GuestPaymentController extends Controller
{
    /**
     * Menampilkan halaman pop-up Midtrans untuk Tamu (Front-End)
     */
    public function show(Payment $payment)
    {
        // 1. Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $guestName = 'Tamu Hotel Neo';
        if ($payment->booking && $payment->booking->guest) {
            $guestName = $payment->booking->guest->name;
        } elseif ($payment->restaurantOrder && $payment->restaurantOrder->guest) {
            $guestName = $payment->restaurantOrder->guest->name;
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'PAY-' . $payment->id . '-' . time(), 
                'gross_amount' => (int) round($payment->amount),
            ],
            'customer_details' => [
                'first_name' => $guestName, 
            ],
        ];

        // Minta Snap Token dari server Midtrans
        $snapToken = Snap::getSnapToken($params);

        return view('users.payment.index', compact('payment', 'snapToken'));
    }

    /**
     * Mengupdate status otomatis dari klik JavaScript (Front-End Tamu)
     */
    public function updateFrontendStatus(Request $request, Payment $payment)
    {
        $status = $request->status;

        if ($status === 'paid' || $status === 'settlement' || $status === 'capture') {
            
            $payment->update(['payment_status' => 'paid']);

            if ($payment->booking) {
                $payment->booking()->update(['status' => 'confirmed']);
                if ($payment->booking->room) {
                    $payment->booking->room()->update(['status' => 'occupied']);
                }
            }
            
            if ($payment->restaurant_order_id) {
                $payment->restaurantOrder()->update(['status' => 'paid']);
            }

        } elseif (in_array($status, ['failed', 'cancel', 'deny', 'expire'])) {
            
            $payment->update(['payment_status' => 'failed']);

            if ($payment->booking) {
                $payment->booking()->update(['status' => 'cancelled']);
                if ($payment->booking->room) {
                    $payment->booking->room()->update(['status' => 'available']);
                }
            }
            
        } else {
            $payment->update(['payment_status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran dan ketersediaan kamar berhasil disinkronkan!'
        ]);
    }
}