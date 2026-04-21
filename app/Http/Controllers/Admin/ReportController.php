<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Transaction;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function incomeReport(Request $request)
    {
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        $payments = Payment::whereBetween('created_at', [$start, $end])
                            ->where('payment_status', 'paid')
                            ->get();

        $bookingIncome = $payments->whereNotNull('booking_id')->sum('amount');
        $restoIncome = $payments->whereNotNull('restaurant_order_id')->sum('amount');

        return response()->json([
            'success' => true,
            'period' => [
                'from' => $start->toDateTimeString(),
                'to' => $end->toDateTimeString()
            ],
            'summary' => [
                'total_booking' => (float) $bookingIncome,
                'total_restaurant' => (float) $restoIncome,
                'total_combined' => (float) ($bookingIncome + $restoIncome),
            ],
            'transaction_count' => $payments->count()
        ]);
    }

    public function checkMidtransStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return response()->json([
                'success' => true,
                'midtrans_raw_status' => $status['transaction_status'],
                'payment_type'        => $status['payment_type'],
                'gross_amount'        => $status['gross_amount'],
                'order_id'            => $status['order_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }
}