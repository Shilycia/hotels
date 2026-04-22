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
            'category' => 'required|string', 
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean', 
            'description' => 'nullable|string',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('foto_url')) {
            $file = $request->file('foto_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/menus'), $filename);
            
            $validated['foto_url'] = 'images/menus/' . $filename;
        }

        RestaurantMenu::create($validated);

        return redirect()->route('admin.menus')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    public function update(Request $request, RestaurantMenu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'description' => 'nullable|string',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto_url')) {
            // Hapus file lama jika ada
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
        if ($menu->foto_url && File::exists(public_path($menu->foto_url))) {
            File::delete(public_path($menu->foto_url));
        }
        
        $menu->delete();
        
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil dihapus!');
    }
}