<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Menggunakan logika penghitungan dari file lama Anda
        $widgets = [
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'total_revenue' => Booking::where('status', 'paid')->sum('total_price'), 
            'total_users' => User::count(),
        ];

        $recentBookings = Booking::with(['user', 'room'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact('widgets', 'recentBookings'));
    }
}