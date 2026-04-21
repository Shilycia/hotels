<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room_images', 'public');
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

        if ($request->hasFile('image')) {
            
            $imagePath = $request->file('image')->store('room_images', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $roomType->update($validated);

        return redirect()->route('admin.room-types')->with('success', 'Tipe Kamar berhasil diperbarui!');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Tipe kamar ini masih digunakan oleh beberapa nomor kamar.');
        }

        $roomType->delete();
        
        return redirect()->back()->with('success', 'Tipe kamar berhasil dihapus!');
    }
}