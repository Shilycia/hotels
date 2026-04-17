<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room = Room::create($validated);
        return response()->json(['message' => 'Kamar berhasil ditambahkan', 'data' => $room], 201);
    }

    public function update(Request $request, Room $room) {
        $room->update($request->all());
        return response()->json(['message' => 'Kamar berhasil diperbarui', 'data' => $room]);
    }

    public function destroy(Room $room) {
        $room->delete();
        return response()->json(['message' => 'Kamar berhasil dihapus']);
    }
}
