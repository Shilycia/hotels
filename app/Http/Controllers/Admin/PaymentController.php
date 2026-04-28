<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with([
            'booking.guest', 
            'restaurantOrder.guest', 
            'packageOrder.guest'
        ])->latest()->get();
        
        return view('admin.payment.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'required|in:cash,transfer,credit_card,e_wallet',
        ]);

        $data = $request->only(['payment_status', 'payment_method']);
        
        if ($request->payment_status == 'paid' && $payment->payment_status != 'paid') {
            $data['paid_at'] = now();
        }

        $payment->update($data);
        if ($request->payment_status == 'paid') {
            if ($payment->booking_id) {
                $payment->booking->update(['status' => 'confirmed']);
            } elseif ($payment->restaurant_order_id) {
                // Makanan dibayar? Langsung suruh dapur masak
                $payment->restaurantOrder->update(['status' => 'preparing']); 
            } elseif ($payment->package_order_id) {
                $payment->packageOrder->update(['status' => 'confirmed']);
            }
        } elseif ($request->payment_status == 'failed') {
            if ($payment->booking_id) $payment->booking->update(['status' => 'cancelled']);
            // [K-04] FIX: Ganti 'completed' menjadi 'cancelled'
            elseif ($payment->restaurant_order_id) $payment->restaurantOrder->update(['status' => 'cancelled']); 
            elseif ($payment->package_order_id) $payment->packageOrder->update(['status' => 'cancelled']);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Status tagihan berhasil diperbarui!');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Data tagihan berhasil dihapus!');
    }
    
    public function webhookCallback(Request $request)
    {
        // [K-08] FIX: Gunakan config() alih-alih env() agar aman saat config:cache
        $serverKey = config('midtrans.server_key');
        
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            $payment = Payment::where('midtrans_order_id', $request->order_id)->first();
            
            if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

            $transactionStatus = $request->transaction_status;

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $payment->update([
                    'payment_status' => 'paid',
                    'midtrans_transaction_id' => $request->transaction_id,
                    'paid_at' => now()
                ]);

                if ($payment->booking_id) {
                    $payment->booking->update(['status' => 'confirmed']);
                } elseif ($payment->restaurant_order_id) {
                    $payment->restaurantOrder->update(['status' => 'preparing']);
                } elseif ($payment->package_order_id) {
                    $payment->packageOrder->update(['status' => 'confirmed']);
                }

            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                $payment->update(['payment_status' => 'failed']);
                
                if ($payment->booking_id) $payment->booking->update(['status' => 'cancelled']);
                // [K-04] FIX: Ganti 'completed' menjadi 'cancelled'
                elseif ($payment->restaurant_order_id) $payment->restaurantOrder->update(['status' => 'cancelled']);
                elseif ($payment->package_order_id) $payment->packageOrder->update(['status' => 'cancelled']);
            }

            return response()->json(['message' => 'Webhook success']);
        }

        return response()->json(['message' => 'Invalid signature'], 403);
    }
}