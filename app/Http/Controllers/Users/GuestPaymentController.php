<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Discount;
use App\Models\Guest;
use App\Models\Booking; // Tambahkan ini
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
        $transactionType = ''; // [C-03] FIX: Ditentukan oleh server, bukan dari client

        if ($request->booking_id) {
            $totalAmount = \App\Models\Booking::where('id', $request->booking_id)->where('guest_id', $guestId)->firstOrFail()->total_amount;
            $transactionType = 'bookings';
        } elseif ($request->restaurant_order_id) {
            $totalAmount = \App\Models\RestaurantOrder::where('id', $request->restaurant_order_id)->where('guest_id', $guestId)->firstOrFail()->total_amount;
            $transactionType = 'restaurant_orders';
        } elseif ($request->package_order_id) {
            $totalAmount = \App\Models\PackageOrder::where('id', $request->package_order_id)->where('guest_id', $guestId)->firstOrFail()->total_amount;
            $transactionType = 'package_orders';
        } else {
            abort(400, 'Pesanan tidak ditemukan atau bukan milik Anda.');
        }

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

        if (!$isOwner) abort(403, 'Akses Ditangguhkan.');

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // [X-01 & X-02] FIX: Gunakan Order ID yang ada di DB. Jangan buat baru!
        $orderId = $payment->midtrans_order_id;
        
        // Fallback jika data lama belum punya midtrans_order_id
        if (!$orderId) {
            $orderPrefix = 'PAY-';
            if ($payment->booking) $orderPrefix = 'ROOM-';
            elseif ($payment->restaurantOrder) $orderPrefix = 'RESTO-';
            elseif ($payment->packageOrder) $orderPrefix = 'PKG-'; 
            
            $orderId = $orderPrefix . $payment->id . '-' . time();
            $payment->update(['midtrans_order_id' => $orderId]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
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
        
        $guestIdInSession = session('guest_id');
        $isOwner = false;

        if ($payment->booking && $payment->booking->guest_id == $guestIdInSession) $isOwner = true;
        elseif ($payment->restaurantOrder && $payment->restaurantOrder->guest_id == $guestIdInSession) $isOwner = true;
        elseif ($payment->packageOrder && $payment->packageOrder->guest_id == $guestIdInSession) $isOwner = true;

        if (!$isOwner) abort(403);

        $status = strtolower($request->status);

        if (in_array($status, ['paid', 'settlement', 'capture'])) {
            $payment->update(['payment_status' => 'paid']);
            if ($payment->booking) $payment->booking->update(['status' => 'confirmed']);
            if ($payment->restaurantOrder) $payment->restaurantOrder->update(['status' => 'preparing']);
            
            if ($payment->packageOrder) {
                $payment->packageOrder->update(['status' => 'confirmed']);
                // [N-01*] FIX: Konfirmasi juga Shadow Booking-nya
                $shadow = Booking::where('guest_id', $payment->packageOrder->guest_id)
                                 ->where('check_in_date', $payment->packageOrder->start_date)
                                 ->where('check_out_date', $payment->packageOrder->end_date)
                                 ->where('status', 'pending')
                                 ->first();
                if ($shadow) $shadow->update(['status' => 'confirmed']);
            }

        } elseif (in_array($status, ['failed', 'deny', 'cancel', 'expire'])) {
            $payment->update(['payment_status' => 'failed']);
            if ($payment->booking) $payment->booking->update(['status' => 'cancelled']);
            if ($payment->restaurantOrder) $payment->restaurantOrder->update(['status' => 'cancelled']);
            
            if ($payment->packageOrder) {
                $payment->packageOrder->update(['status' => 'cancelled']);
                // [N-01*] FIX: Batalkan juga Shadow Booking-nya agar kamar rilis
                $shadow = Booking::where('guest_id', $payment->packageOrder->guest_id)
                                 ->where('check_in_date', $payment->packageOrder->start_date)
                                 ->where('check_out_date', $payment->packageOrder->end_date)
                                 ->where('status', 'pending')
                                 ->first();
                if ($shadow) $shadow->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['success' => true]);
    }
}