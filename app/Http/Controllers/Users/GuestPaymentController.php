<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestPaymentController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function processPayment(Request $request)
    {
        $totalAmount = $request->total_amount;
        $transactionType = $request->transaction_type;
        $discountApplied = 0;
        $activeDiscount = Discount::where('is_active', true)
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->where(function($query) use ($transactionType) {
                $query->where('applicable_to', 'all')
                      ->orWhere('applicable_to', $transactionType);
            })
            ->where('min_transaction_amount', '<=', $totalAmount)
            ->first();

        if ($activeDiscount) {
            if ($activeDiscount->discount_type == 'percentage') {
                $discountApplied = $totalAmount * ($activeDiscount->discount_value / 100);
            } else {
                $discountApplied = $activeDiscount->discount_value;
            }
        }

        $finalAmount = $totalAmount - $discountApplied;
        $orderId = 'NEO-' . time() . '-' . Str::random(5);
        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'restaurant_order_id' => $request->restaurant_order_id,
            'package_order_id' => $request->package_order_id,
            'amount' => $finalAmount,
            'discount_applied' => $discountApplied,
            'payment_status' => 'pending',
            'midtrans_order_id' => $orderId,
        ]);
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $finalAmount,
            ],
            'customer_details' => [
                'first_name' => auth('guest')->user()->name ?? 'Tamu',
                'email' => auth('guest')->user()->email ?? 'tamu@hotelneo.com',
            ]
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return view('users.pages.invoice', compact('payment', 'snapToken'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat pembayaran: ' . $e->getMessage());
        }
    }

    public function showPayment($id)
    {
        $payment = Payment::with('booking.guest')->findOrFail($id);

        // Keamanan: Pastikan yang buka halaman ini adalah yang punya pesanan
        if ($payment->booking->guest_id != session('guest_id')) {
            abort(403, 'Akses Ditolak.');
        }

        // Konfigurasi Midtrans (Pastikan Anda sudah menginstall package midtrans/midtrans-php)
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Data transaksi untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'PAY-' . $payment->id . '-' . time(), // Format Order ID unik
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $payment->booking->guest->name ?? 'Tamu',
                'email' => $payment->booking->guest->email ?? '',
                'phone' => $payment->booking->guest->phone ?? '',
            ],
        ];

        // Minta Snap Token ke Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Lempar ke view pembayaran yang Anda buat (misal namanya payment.blade.php)
        return view('users.payment.index', compact('payment', 'snapToken'));
    }

    // Fungsi untuk mengupdate status setelah popup Midtrans ditutup/berhasil
    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($request->status === 'paid') {
            $payment->payment_status = 'paid';
            $payment->save();

            // Ubah status booking menjadi confirmed
            if ($payment->booking) {
                $payment->booking->status = 'confirmed';
                $payment->booking->save();
            }
        }

        return response()->json(['success' => true]);
    }
}