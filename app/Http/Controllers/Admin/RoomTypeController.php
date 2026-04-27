<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->latest()->get();
        
        return view('admin.room_types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'adult_capacity' => 'required|integer|min:1',
            'child_capacity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'bed_type' => 'nullable|string|max:100',
            'bath_count' => 'required|integer|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('room_types', 'public');
        }

        RoomType::create($data);

        return redirect()->route('admin.room-types.index')->with('success', 'Tipe Kamar berhasil ditambahkan!');
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room_types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'adult_capacity' => 'required|integer|min:1',
            'child_capacity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'bed_type' => 'nullable|string|max:100',
            'bath_count' => 'required|integer|min:1',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($roomType->foto) {
                Storage::disk('public')->delete($roomType->foto);
            }
            $data['foto'] = $request->file('foto')->store('room_types', 'public');
        }

        $roomType->update($data);

        return redirect()->route('admin.room-types.index')->with('success', 'Data Tipe Kamar berhasil diperbarui!');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->foto) {
            Storage::disk('public')->delete($roomType->foto);
        }
        
        $roomType->delete();

        return redirect()->route('admin.room-types.index')->with('success', 'Tipe Kamar berhasil dihapus!');
    }
}