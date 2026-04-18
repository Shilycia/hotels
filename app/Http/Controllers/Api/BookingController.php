<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $isBooked = Booking::where('room_id', $request->room_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                    ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
            })->exists();

        if ($isBooked) {
            return response()->json(['message' => 'Kamar sudah dipesan pada tanggal tersebut.'], 422);
        }

        return DB::transaction(function () use ($request) {
            $room = Room::lockForUpdate()->find($request->room_id);
            if ($room->status !== 'available') {
                return response()->json(['message' => 'Kamar tidak tersedia'], 422);
            }

            // Asumsi relasi roomType ada
            $totalPrice = $room->roomType->price ?? 0; 

            $booking = Booking::create([
                'guest_id'    => $request->guest_id ?? Auth::id(), // Menggunakan auth()->id() jika dari app
                'room_id'     => $request->room_id,
                'check_in'    => $request->check_in,
                'check_out'   => $request->check_out,
                'total_price' => $totalPrice,
                'status'      => 'confirmed',
            ]);

            $room->update(['status' => 'occupied']);

            return response()->json([
                'message' => 'Booking berhasil dibuat',
                'data'    => $booking
            ], 201);
        });
    }
}