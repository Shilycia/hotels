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
        // 1. Muat pembayaran beserta KETIGA relasi yang mungkin ada
        $payment = Payment::with(['booking.guest', 'restaurantOrder.guest', 'packageOrder.guest'])->findOrFail($id);

        // 2. Identifikasi Pemilik Pesanan (Kamar, Restoran, atau Paket)
        $guest = null;
        $isOwner = false;
        $guestIdInSession = session('guest_id');

        if ($payment->booking) {
            $guest = $payment->booking->guest;
            if ($payment->booking->guest_id == $guestIdInSession) $isOwner = true;
        } elseif ($payment->restaurantOrder) {
            $guest = $payment->restaurantOrder->guest;
            if ($payment->restaurantOrder->guest_id == $guestIdInSession) $isOwner = true;
        } elseif ($payment->packageOrder) { // <-- INI DIA KUNCI AGAR PAKET LOLOS
            $guest = $payment->packageOrder->guest;
            if ($payment->packageOrder->guest_id == $guestIdInSession) $isOwner = true;
        }

        // Keamanan: Jika bukan pemilik transaksi, usir.
        if (!$isOwner) {
            abort(403, 'Akses Ditangguhkan: Transaksi ini bukan milik Anda.');
        }

        // 3. Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Tentukan Label Prefix Order ID
        $orderPrefix = 'PAY-';
        if ($payment->booking) $orderPrefix = 'ROOM-';
        elseif ($payment->restaurantOrder) $orderPrefix = 'RESTO-';
        elseif ($payment->packageOrder) $orderPrefix = 'PKG-'; // Prefix khusus Paket

        // 4. Data transaksi dinamis
        $params = [
            'transaction_details' => [
                'order_id' => $orderPrefix . $payment->id . '-' . time(),
                'gross_amount' => (int) $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $guest->name ?? 'Guest',
                'email' => $guest->email ?? '',
                'phone' => $guest->phone ?? '',
            ],
        ];

        // 5. Minta Snap Token
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke penyedia pembayaran: ' . $e->getMessage());
        }

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