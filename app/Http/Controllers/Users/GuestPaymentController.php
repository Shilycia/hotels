<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Discount;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestPaymentController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function processPayment(Request $request)
    {
        $guestId = session('guest_id');
        $totalAmount = 0;

        // [B-01] FIX: Verifikasi ketat bahwa pesanan ini benar-benar milik tamu yang sedang login!
        if ($request->booking_id) {
            $totalAmount = \App\Models\Booking::where('id', $request->booking_id)
                                              ->where('guest_id', $guestId)
                                              ->firstOrFail()->total_amount;
        } elseif ($request->restaurant_order_id) {
            $totalAmount = \App\Models\RestaurantOrder::where('id', $request->restaurant_order_id)
                                                      ->where('guest_id', $guestId)
                                                      ->firstOrFail()->total_amount;
        } elseif ($request->package_order_id) {
            $totalAmount = \App\Models\PackageOrder::where('id', $request->package_order_id)
                                                   ->where('guest_id', $guestId)
                                                   ->firstOrFail()->total_amount;
        } else {
            abort(400, 'Pesanan tidak ditemukan atau bukan milik Anda.');
        }

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

        $finalAmount = max(0, $totalAmount - $discountApplied);
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

        $guest = Guest::find($guestId);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $finalAmount,
            ],
            'customer_details' => [
                'first_name' => $guest->name ?? 'Tamu',
                'email' => $guest->email ?? 'tamu@hotelneo.com',
                'phone' => $guest->phone ?? '',
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
        $payment = Payment::with(['booking.guest', 'restaurantOrder.guest', 'packageOrder.guest'])->findOrFail($id);

        $guest = null;
        $isOwner = false;
        $guestIdInSession = session('guest_id');

        if ($payment->booking) {
            $guest = $payment->booking->guest;
            if ($payment->booking->guest_id == $guestIdInSession) $isOwner = true;
        } elseif ($payment->restaurantOrder) {
            $guest = $payment->restaurantOrder->guest;
            if ($payment->restaurantOrder->guest_id == $guestIdInSession) $isOwner = true;
        } elseif ($payment->packageOrder) { 
            $guest = $payment->packageOrder->guest;
            if ($payment->packageOrder->guest_id == $guestIdInSession) $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'Akses Ditangguhkan: Transaksi ini bukan milik Anda.');
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderPrefix = 'PAY-';
        if ($payment->booking) $orderPrefix = 'ROOM-';
        elseif ($payment->restaurantOrder) $orderPrefix = 'RESTO-';
        elseif ($payment->packageOrder) $orderPrefix = 'PKG-'; 

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

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke penyedia pembayaran: ' . $e->getMessage());
        }

        return view('users.payment.index', compact('payment', 'snapToken'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::with(['booking', 'restaurantOrder', 'packageOrder'])->findOrFail($id);
        
        // [B-02] FIX: Verifikasi kepemilikan sebelum mengizinkan update status
        $guestIdInSession = session('guest_id');
        $isOwner = false;

        if ($payment->booking && $payment->booking->guest_id == $guestIdInSession) $isOwner = true;
        elseif ($payment->restaurantOrder && $payment->restaurantOrder->guest_id == $guestIdInSession) $isOwner = true;
        elseif ($payment->packageOrder && $payment->packageOrder->guest_id == $guestIdInSession) $isOwner = true;

        if (!$isOwner) {
            abort(403, 'Akses Ditangguhkan: Anda tidak berhak mengubah status transaksi ini.');
        }

        $status = strtolower($request->status);

        if (in_array($status, ['paid', 'settlement', 'capture'])) {
            $payment->payment_status = 'paid';
            $payment->save();

            if ($payment->booking) $payment->booking->update(['status' => 'confirmed']);
            // [B-03] FIX: Ganti 'placed' menjadi 'preparing' agar sesuai dengan enum database
            if ($payment->restaurantOrder) $payment->restaurantOrder->update(['status' => 'preparing']);
            if ($payment->packageOrder) $payment->packageOrder->update(['status' => 'confirmed']);
        } 
        elseif (in_array($status, ['failed', 'deny', 'cancel', 'expire'])) {
            $payment->payment_status = 'failed';
            $payment->save();

            if ($payment->booking) $payment->booking->update(['status' => 'cancelled']);
            if ($payment->restaurantOrder) $payment->restaurantOrder->update(['status' => 'cancelled']);
            if ($payment->packageOrder) $payment->packageOrder->update(['status' => 'cancelled']);
        }

        return response()->json(['success' => true]);
    }
}