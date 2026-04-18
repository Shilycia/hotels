<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType; // Pastikan ini di-import
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        // Ambil data kamar beserta relasi tipe kamarnya
        $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();
        
        // Ambil data tipe kamar untuk form dropdown
        $roomTypes = RoomType::all(); 
        
        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        Room::create($validated);
        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil ditambahkan');
    }

    public function update(Request $request, Room $room) 
    {
        // Pengecualian unique room_number untuk ID kamar yang sedang diedit
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room->update($validated);
        return redirect()->route('admin.rooms')->with('success', 'Data kamar berhasil diperbarui');
    }

    public function destroy(Room $room) 
    {
        $room->delete();
        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil dihapus');
    }
}