<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RestaurantOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        
        // 1. Total Pendapatan Bulan Ini (Hanya yang sudah dibayar)
        $revenueThisMonth = Payment::where('payment_status', 'paid')
            ->whereMonth('paid_at', $thisMonth)
            ->whereYear('paid_at', $thisYear)
            ->sum('amount');

        $checkInsToday = Booking::whereDate('check_in_date', $today)->count();

        $activeFnbOrders = RestaurantOrder::whereIn('status', ['pending', 'preparing', 'served'])->count();

        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $cleaningRooms = Room::where('status', 'cleaning')->count();

        $last7Days = collect(range(6, 0))->map(function ($days) {
            return Carbon::today()->subDays($days)->format('Y-m-d');
        });

        $chartData = Payment::select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');

        $revenueChart = $last7Days->mapWithKeys(function ($date) use ($chartData) {
            return [$date => $chartData->get($date, 0)];
        });

        $recentBookings = Booking::with(['guest', 'room'])->latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'revenueThisMonth', 'checkInsToday', 'activeFnbOrders',
            'totalRooms', 'availableRooms', 'occupiedRooms', 'cleaningRooms',
            'revenueChart', 'recentBookings'
        ));
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        $payments = Payment::with(['booking', 'restaurantOrder', 'packageOrder'])
            ->where('payment_status', 'paid')
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->latest('paid_at')
            ->get();

        $roomRevenue = 0;
        $fnbRevenue = 0;
        $packageRevenue = 0;

        foreach ($payments as $payment) {
            if ($payment->booking_id) {
                $roomRevenue += $payment->amount;
            } elseif ($payment->restaurant_order_id) {
                $fnbRevenue += $payment->amount;
            } elseif ($payment->package_order_id) {
                $packageRevenue += $payment->amount;
            }
        }

        $totalRevenue = $roomRevenue + $fnbRevenue + $packageRevenue;

        return view('admin.dashboard.reports', compact(
            'payments', 'startDate', 'endDate', 
            'roomRevenue', 'fnbRevenue', 'packageRevenue', 'totalRevenue'
        ));
    }
}