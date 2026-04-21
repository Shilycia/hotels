<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\RestaurantOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Kalkulasi Pendapatan Akurat (Hanya yang berstatus 'paid' di tabel payments)
        $payments = Payment::where('payment_status', 'paid')->get();
        
        $totalBookingRevenue = $payments->whereNotNull('booking_id')->sum('amount');
        $totalRestoRevenue = $payments->whereNotNull('restaurant_order_id')->sum('amount');

        // 2. Data Widgets (Kartu Statistik Atas)
        $widgets = [
            'total_users' => User::count(),
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'total_revenue' => $totalBookingRevenue + $totalRestoRevenue,
            'total_resto_orders' => RestaurantOrder::count(),
        ];

        // 3. Data Tren Booking (Bar Chart - 6 Bulan Terakhir)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M y');
            // Menghitung jumlah booking yang dibuat pada bulan tersebut
            $chartData[] = Booking::whereMonth('created_at', $date->month)
                                  ->whereYear('created_at', $date->year)
                                  ->count();
        }

        // 4. Data Komposisi Pendapatan (Donut Chart)
        $donutData = [$totalBookingRevenue, $totalRestoRevenue];

        // 5. Tabel Booking Terbaru (Sinkron dengan data Guest)
        $recentBookings = Booking::with(['guest', 'room'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact(
            'widgets', 
            'recentBookings', 
            'chartLabels', 
            'chartData', 
            'donutData'
        ));
    }
}