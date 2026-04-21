<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->where('status', 'available')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar kamar tersedia berhasil diambil',
            'data' => $rooms
        ], 200);
    }
}