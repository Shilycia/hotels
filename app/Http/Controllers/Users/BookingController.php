<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $rooms    = Room::with('roomType')->where('status', 'available')->orderBy('floor')->orderBy('room_number')->get();
        $authUser = Auth::user();

        return view('users.pages.booking', compact('rooms', 'authUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'check_in'        => 'required|date|after_or_equal:today',
            'check_out'       => 'required|date|after:check_in',
            'adult'           => 'required|integer|min:1|max:10',
            'child'           => 'required|integer|min:0|max:10',
            'room_id'         => 'required|exists:rooms,id',
            'special_request' => 'nullable|string|max:1000',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        if ($room->status !== 'available') {
            return back()->withInput()->withErrors([
                'room_id' => 'Kamar yang dipilih sudah tidak tersedia. Silakan pilih kamar lain.',
            ]);
        }

        $checkIn    = Carbon::parse($validated['check_in']);
        $checkOut   = Carbon::parse($validated['check_out']);
        $nights     = $checkIn->diffInDays($checkOut);
        $totalPrice = ($room->roomType->price ?? 0) * $nights;

        $guest = Guest::updateOrCreate(
            ['email' => $validated['email']],
            ['name'  => $validated['name']]
        );

        Booking::create([
            'guest_id'    => $guest->id,
            'room_id'     => $validated['room_id'],
            'check_in'    => $validated['check_in'],
            'check_out'   => $validated['check_out'],
            'total_price' => $totalPrice,
            'status'      => 'pending',
        ]);

        return redirect()->route('booking')
            ->with('success', 'Booking berhasil! Kami akan menghubungi Anda di ' . $validated['email'] . ' untuk konfirmasi.');
    }
}