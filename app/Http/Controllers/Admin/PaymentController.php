<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.guest', 'restaurantOrder.guest'])
                           ->orderBy('created_at', 'desc')
                           ->get();

        return view('admin.payment.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet',
        ]);

        $payment->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        if ($request->payment_status === 'paid') {
            
            if ($payment->restaurant_order_id) {
                $payment->restaurantOrder()->update(['status' => 'paid']);
            }
            
            if ($payment->booking) {
                $payment->booking()->update(['status' => 'confirmed']); 
                if ($payment->booking->room) {
                    $payment->booking->room()->update(['status' => 'occupied']);
                }
            }

        } elseif ($request->payment_status === 'failed') {
            
            if ($payment->booking) {
                $payment->booking()->update(['status' => 'cancelled']); 
                if ($payment->booking->room) {
                    $payment->booking->room()->update(['status' => 'available']);
                }
            }

        } elseif ($request->payment_status === 'pending') {
            
            if ($payment->booking) {
                $payment->booking()->update(['status' => 'pending']);
                if ($payment->booking->room) {
                    $payment->booking->room()->update(['status' => 'available']);
                }
            }
        }

        return redirect()->route('admin.payments')->with('success', 'Detail pembayaran dan status kamar berhasil disinkronkan!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments')->with('success', 'Data pembayaran dihapus.');
    }

    public function midtransCallback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signatureKey != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderIdParts = explode('-', $request->order_id);
        $paymentId = $orderIdParts[1]; 

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $paymentMethod = $request->payment_type;

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $payment->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod == 'bank_transfer' ? 'transfer' : 'e_wallet', 
            ]);
            
            if($payment->booking_id) {
                $payment->booking()->update(['status' => 'confirmed']);
            }
            if($payment->restaurant_order_id) {
                $payment->restaurantOrder()->update(['status' => 'paid']);
            }

        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $payment->update(['payment_status' => 'failed']);
            
            if($payment->booking_id) {
                $payment->booking()->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['message' => 'Callback received successfully']);
    }
}