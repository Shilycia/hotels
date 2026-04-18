<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        // Ambil semua tipe kamar beserta jumlah kamar yang menggunakan tipe ini
        $roomTypes = RoomType::withCount('rooms')->orderBy('price', 'asc')->get();
        return view('admin.room_types.index', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        RoomType::create($validated);

        return redirect()->route('admin.room-types')->with('success', 'Tipe kamar baru berhasil ditambahkan!');
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name,' . $roomType->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $roomType->update($validated);

        return redirect()->route('admin.room-types')->with('success', 'Data tipe kamar berhasil diperbarui!');
    }

    public function destroy(RoomType $roomType)
    {
        // PROTEKSI: Jangan biarkan dihapus jika masih ada kamar yang terkait
        if ($roomType->rooms()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Tipe kamar ini masih digunakan oleh beberapa nomor kamar.');
        }

        $roomType->delete();
        
        return redirect()->back()->with('success', 'Tipe kamar berhasil dihapus!');
    }
}