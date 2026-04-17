<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Http\Resources\RoomResource;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar kamar berhasil diambil',
            'data' => RoomResource::collection($rooms)
        ], 200);
    }
}
