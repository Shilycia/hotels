<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest; 
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        // Ubah relasi dari 'user' menjadi 'guest'
        $bookings = Booking::with(['guest', 'room.roomType'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        $guests = Guest::orderBy('name', 'asc')->get(); // Ambil data dari tabel guests
        $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();

        return view('admin.booking.index', compact('bookings', 'guests', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id', // Ubah validasi ke guest_id
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled', // Sesuaikan enum migration
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $days = max(1, $checkIn->diffInDays($checkOut));

        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalPrice = ($room->roomType->price ?? 0) * $days;

        Booking::create([
            'guest_id' => $request->guest_id, // Masukkan guest_id
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'total_price' => $totalPrice,
            // payment_status dihapus
        ]);

        if (in_array($request->status, ['confirmed', 'checked_in'])) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Reservasi baru berhasil ditambahkan!');
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $days = max(1, $checkIn->diffInDays($checkOut));
        
        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalPrice = ($room->roomType->price ?? 0) * $days;

        if ($booking->room_id != $request->room_id) {
            Room::where('id', $booking->room_id)->update(['status' => 'available']);
        }

        $booking->update([
            'guest_id' => $request->guest_id,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'total_price' => $totalPrice,
        ]);

        if (in_array($request->status, ['cancelled', 'checked_out'])) {
            $room->update(['status' => 'available']);
        } elseif (in_array($request->status, ['confirmed', 'checked_in'])) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Data reservasi berhasil diperbarui!');
    }

    public function destroy(Booking $booking)
    {
        $booking->room()->update(['status' => 'available']);
        $booking->delete();
        return redirect()->route('admin.bookings')->with('success', 'Data reservasi berhasil dihapus.');
    }
}