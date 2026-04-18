<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function midtransCallback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                $payment = Payment::where('note', 'LIKE', '%' . $request->order_id . '%')->first();
                
                if ($payment) {
                    $payment->update(['payment_status' => 'paid']);
                    
                    // Jika ini pembayaran booking, otomatis ubah status booking juga
                    if ($payment->booking_id) {
                        $payment->booking()->update(['payment_status' => 'paid']);
                    }
                }
            }
        }

        return response()->json(['message' => 'Callback diterima']);
    }
}