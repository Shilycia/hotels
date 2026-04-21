<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')
            ->orderBy('floor', 'asc')
            ->orderBy('room_number', 'asc')
            ->paginate(10);
        
        $roomTypes = RoomType::all(); 
        
        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'room_number'  => 'required|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'required|integer|min:1', 
            'status'       => 'required|in:available,occupied,maintenance'
        ]);

        Room::create($validated);
        
        return redirect()->route('admin.rooms')->with('success', 'Kamar berhasil ditambahkan');
    }

    public function update(Request $request, Room $room) 
    {
        $validated = $request->validate([
            'room_number'  => 'required|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'required|integer|min:1', 
            'status'       => 'required|in:available,occupied,maintenance'
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