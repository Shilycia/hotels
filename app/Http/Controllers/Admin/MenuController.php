<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = RestaurantMenu::latest()->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:food,drink,dessert,snack,paket', 
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'allergens' => 'nullable|string|max:255',
            'serving' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('foto_url');
        
        $data['is_available'] = $request->has('is_available');

        // Logika Upload Foto
        if ($request->hasFile('foto_url')) {
            $data['foto_url'] = $request->file('foto_url')->store('menus', 'public');
        }

        RestaurantMenu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu Restoran berhasil ditambahkan!');
    }

    public function edit(RestaurantMenu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, RestaurantMenu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:food,drink,dessert,snack,paket',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'allergens' => 'nullable|string|max:255',
            'serving' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'foto_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('foto_url');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('foto_url')) {
            if ($menu->foto_url) {
                Storage::disk('public')->delete($menu->foto_url);
            }
            $data['foto_url'] = $request->file('foto_url')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu Restoran berhasil diperbarui!');
    }

    public function destroy(RestaurantMenu $menu)
    {
        if ($menu->foto_url) {
            Storage::disk('public')->delete($menu->foto_url);
        }
        
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu Restoran berhasil dihapus!');
    }
}