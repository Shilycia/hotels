<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count(); 
        
        $totalRevenue = Booking::where('status', 'paid')->sum('total_price');

        $recentBookings = Booking::with(['user', 'room'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact(
            'totalRooms', 
            'occupiedRooms', 
            'totalRevenue', 
            'recentBookings'
        ));
    }
}