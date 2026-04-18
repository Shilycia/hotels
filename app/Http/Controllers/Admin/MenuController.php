<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = RestaurantMenu::orderBy('name', 'asc')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string'
        ]);

        RestaurantMenu::create($validated);
        
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil ditambah!');
    }

    public function update(Request $request, RestaurantMenu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string'
        ]);

        $menu->update($validated);
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(RestaurantMenu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus')->with('success', 'Menu berhasil dihapus!');
    }
}