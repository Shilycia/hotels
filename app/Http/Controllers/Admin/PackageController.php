<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\RoomType;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('roomType')->latest()->paginate(10);
        $roomTypes = RoomType::all();
        return view('admin.packages.index', compact('packages', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'room_type_id' => 'nullable|exists:room_types,id',
            'description' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        Package::create($request->all());
        return redirect()->route('admin.packages.index')->with('success', 'Paket Bundling berhasil ditambahkan!');
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'room_type_id' => 'nullable|exists:room_types,id',
            'description' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $package->update($request->all());
        return redirect()->route('admin.packages.index')->with('success', 'Paket diperbarui!');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Paket dihapus!');
    }
}