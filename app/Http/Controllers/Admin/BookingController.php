<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // Menampilkan semua booking beserta relasinya
        $bookings = Booking::with(['user', 'room'])->orderBy('created_at', 'desc')->get();
        return view('admin.bookings.index', compact('bookings'));
    }

    // Admin mungkin butuh fitur untuk membatalkan atau mengubah status booking manual
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking->update(['status' => $request->status]);

        // Jika dibatalkan, kosongkan kembali status kamarnya
        if ($request->status == 'cancelled') {
            $booking->room()->update(['status' => 'available']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Status pesanan diperbarui.');
    }
}