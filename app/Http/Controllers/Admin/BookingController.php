<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest; 
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['guest', 'room.roomType'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        $guests = Guest::orderBy('name', 'asc')->get(); 
        $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();

        return view('admin.booking.index', compact('bookings', 'guests', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id', 
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled', 
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $days = max(1, $checkIn->diffInDays($checkOut));

        $room = Room::with('roomType')->findOrFail($request->room_id);
        $totalPrice = ($room->roomType->price ?? 0) * $days;

        $booking = Booking::create([
            'guest_id' => $request->guest_id, 
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'total_price' => $totalPrice,
        ]);

        if ($request->status !== 'cancelled') {
            Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $totalPrice, 
                'payment_status' => in_array($request->status, ['confirmed', 'checked_in']) ? 'paid' : 'pending',
                'payment_method' => 'transfer', 
            ]);
        }

        // 3. Update status kamar jika perlu
        if (in_array($request->status, ['confirmed', 'checked_in'])) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Reservasi baru berhasil ditambahkan dan tagihan telah dibuat!');
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

        $payment = \App\Models\Payment::where('booking_id', $booking->id)->first();
        if ($payment) {
            $payment->amount = $totalPrice;

            if (in_array($request->status, ['confirmed', 'checked_in', 'checked_out'])) {
                $payment->payment_status = 'paid';
            } elseif ($request->status === 'cancelled') {
                $payment->payment_status = 'failed';
            } elseif ($request->status === 'pending') {
                $payment->payment_status = 'pending';
            }
            
            $payment->save(); 
        }

        // 3. Update Status Fisik Kamar
        if (in_array($request->status, ['cancelled', 'checked_out'])) {
            $room->update(['status' => 'available']);
        } elseif (in_array($request->status, ['confirmed', 'checked_in'])) {
            $room->update(['status' => 'occupied']);
        }

        return redirect()->route('admin.bookings')->with('success', 'Data reservasi dan tagihan berhasil diperbarui!');
    }

    public function destroy(Booking $booking)
    {
        $booking->room()->update(['status' => 'available']);
        
        Payment::where('booking_id', $booking->id)->delete(); 
        
        $booking->delete();
        return redirect()->route('admin.bookings')->with('success', 'Data reservasi beserta tagihannya berhasil dihapus.');
    }
}