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
            $payment = Payment::where('note', 'LIKE', '%' . $request->order_id . '%')->first();
            
            if ($payment) {
                if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                    $payment->update(['payment_status' => 'paid']);
                    
                    if ($payment->booking) {
                        $payment->booking()->update(['status' => 'confirmed']);
                        if ($payment->booking->room) {
                            $payment->booking->room()->update(['status' => 'occupied']);
                        }
                    }
                } elseif (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                    $payment->update(['payment_status' => 'failed']);
                    
                    if ($payment->booking) {
                        $payment->booking()->update(['status' => 'cancelled']);
                        if ($payment->booking->room) {
                            $payment->booking->room()->update(['status' => 'available']);
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Callback diterima']);
    }
}