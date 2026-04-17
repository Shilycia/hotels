<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomAdminController extends Controller
{
    public function index()
    {
        return response()->json(Room::with('roomType')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|unique:rooms,room_number',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room = Room::create($validated);
        return response()->json(['message' => 'Kamar berhasil dibuat', 'data' => $room], 201);
    }

    public function update(Request $request, Room $room)
    {
        $room->update($request->all());
        return response()->json(['message' => 'Data kamar diperbarui', 'data' => $room]);
    }
}
