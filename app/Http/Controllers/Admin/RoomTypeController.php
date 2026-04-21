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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'bed_type' => 'required|string',
            'bath_count' => 'required|integer',
            'description' => 'nullable|string',
            // 👇 Validasi gambar: maksimal 2MB dan formatnya harus gambar
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // LOGIKA UPLOAD GAMBAR
        if ($request->hasFile('image')) {
            // Simpan gambar ke folder 'public/storage/room_images'
            $imagePath = $request->file('image')->store('room_images', 'public');
            // Simpan path-nya ke database dengan awalan 'storage/' agar langsung bisa dibaca oleh asset()
            $validated['image'] = 'storage/' . $imagePath;
        }

        RoomType::create($validated);

        return redirect()->route('admin.room-types')->with('success', 'Tipe Kamar berhasil ditambahkan!');
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'bed_type' => 'required|string',
            'bath_count' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // LOGIKA UPDATE GAMBAR
        if ($request->hasFile('image')) {
            // (Opsional) Kamu bisa menambahkan logika untuk menghapus foto lama di sini
            
            $imagePath = $request->file('image')->store('room_images', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $roomType->update($validated);

        return redirect()->route('admin.room-types')->with('success', 'Tipe Kamar berhasil diperbarui!');
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