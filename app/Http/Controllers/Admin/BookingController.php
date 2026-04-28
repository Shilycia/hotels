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

        // [N-03] FIX: Anti Double Booking untuk Admin
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);

        $isBooked = Booking::where('room_id', $request->room_id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $checkOut->format('Y-m-d'))
            ->where('check_out_date', '>', $checkIn->format('Y-m-d'))
            ->exists();

        if ($isBooked) {
            return back()->with('error', 'Kamar sudah dipesan orang lain pada tanggal tersebut!')->withInput();
        }

        $totalNights = $checkIn->diffInDays($checkOut);
        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalAmount = $room->roomType->price * $totalNights;

        $booking = Booking::create([
            'guest_id' => $request->guest_id,
            'room_id' => $request->room_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_nights' => $totalNights,
            'total_amount' => $totalAmount,
            'status' => $request->status,
        ]);

        // Update status kamar jika langsung check-in
        if ($request->status == 'checked_in') {
            $room->update(['status' => 'occupied']);
        }

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

        // [N-03] FIX: Anti Double Booking (Kecuali booking ini sendiri)
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);

        $isBooked = Booking::where('room_id', $request->room_id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $checkOut->format('Y-m-d'))
            ->where('check_out_date', '>', $checkIn->format('Y-m-d'))
            ->exists();

        if ($isBooked) {
            return back()->with('error', 'Perubahan gagal: Kamar sudah dipesan orang lain pada tanggal tersebut!')->withInput();
        }

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

        // [N-02] FIX: Manajemen Status Kamar Terintegrasi
        if ($request->status == 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif (in_array($request->status, ['checked_out', 'cancelled'])) {
            $booking->room->update(['status' => 'available']);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Reservasi berhasil diperbarui!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Reservasi berhasil dihapus!');
    }
}