<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    public function index()
    {
        $menus = RestaurantMenu::orderBy('name', 'asc')->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Jika ada file foto yang diunggah
        if ($request->hasFile('foto_url')) {
            $file = $request->file('foto_url');
            // Buat nama file unik menggunakan waktu saat ini
            $filename = time() . '_' . $file->getClientOriginalName();
            // Pindahkan file langsung ke folder public/images/menus
            $file->move(public_path('images/menus'), $filename);
            
            // Simpan path relatifnya ke database
            $validated['foto_url'] = 'images/menus/' . $filename;
        }

        RestaurantMenu::create($validated);

        return redirect()->route('admin.menus')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    public function update(Request $request, RestaurantMenu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Jika user mengunggah foto baru saat edit
        if ($request->hasFile('foto_url')) {
            // Hapus foto lama di folder public jika ada
            if ($menu->foto_url && File::exists(public_path($menu->foto_url))) {
                File::delete(public_path($menu->foto_url));
            }

            $file = $request->file('foto_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/menus'), $filename);
            
            $validated['foto_url'] = 'images/menus/' . $filename;
        }

        $menu->update($validated);

        return redirect()->route('admin.menus')->with('success', 'Data menu berhasil diperbarui!');
    }

    public function destroy(RestaurantMenu $menu)
    {
        // Hapus file fisik foto dari folder public sebelum data dihapus
        if ($menu->foto_url && File::exists(public_path($menu->foto_url))) {
            File::delete(public_path($menu->foto_url));
        }
        
        $menu->delete();
        
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil dihapus!');
    }
}