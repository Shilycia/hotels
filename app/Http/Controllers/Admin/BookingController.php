<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['guest', 'room.roomType'])->latest()->get();
        
        $guests = Guest::all();
        $rooms = Room::with('roomType')->get();
        
        return view('admin.booking.index', compact('bookings', 'guests', 'rooms'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        // Kalkulasi Total Malam dan Total Harga
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $totalNights = $checkIn->diffInDays($checkOut);

        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalAmount = $room->roomType->price * $totalNights;

        Booking::create([
            'guest_id' => $request->guest_id,
            'room_id' => $request->room_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_nights' => $totalNights,
            'total_amount' => $totalAmount,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Reservasi berhasil ditambahkan!');
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $totalNights = $checkIn->diffInDays($checkOut);

        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalAmount = $room->roomType->price * $totalNights;

        $booking->update([
            'guest_id' => $request->guest_id,
            'room_id' => $request->room_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_nights' => $totalNights,
            'total_amount' => $totalAmount,
            'status' => $request->status,
        ]);

        if ($request->status == 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Reservasi berhasil diperbarui!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Reservasi berhasil dihapus!');
    }
}