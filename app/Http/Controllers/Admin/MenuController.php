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
        // Panggil menu beserta relasi isi paketnya
        $menus = RestaurantMenu::with('paketItems')->latest()->get();
        
        // Panggil daftar makanan/minuman mentah untuk dijadikan pilihan di dalam form pembuatan Paket
        $foodItems = RestaurantMenu::whereIn('category', ['food', 'drink', 'dessert', 'snack'])
            ->where('is_available', true)
            ->get();

        return view('admin.menu.index', compact('menus', 'foodItems'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category'             => 'required|in:food,drink,dessert,snack,paket', 
            'price'                => 'required|numeric|min:0',
            'description'          => 'nullable|string',
            'prep_time'            => 'nullable|integer|min:0',
            'calories'             => 'nullable|integer|min:0',
            'allergens'            => 'nullable|string|max:255',
            'serving'              => 'nullable|string|max:100',
            'rating'               => 'nullable|numeric|min:0|max:5',
            'is_available'         => 'required|boolean',
            'can_bundle_with_room' => 'required|boolean', // Aturan baru
            'paket_items'          => 'nullable|array',   // Aturan baru
            'paket_items.*'        => 'exists:restaurant_menus,id',
            'foto_url'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['foto_url', 'paket_items']);

        if ($request->hasFile('foto_url')) {
            $data['foto_url'] = $request->file('foto_url')->store('menus', 'public');
        }

        $menu = RestaurantMenu::create($data);

        // Jika kategori paket, simpan daftar isi menunya ke tabel pivot
        if ($menu->category === 'paket' && $request->has('paket_items')) {
            $menu->paketItems()->sync($request->paket_items);
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(RestaurantMenu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, RestaurantMenu $menu)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category'             => 'required|in:food,drink,dessert,snack,paket',
            'price'                => 'required|numeric|min:0',
            'description'          => 'nullable|string',
            'prep_time'            => 'nullable|integer|min:0',
            'calories'             => 'nullable|integer|min:0',
            'allergens'            => 'nullable|string|max:255',
            'serving'              => 'nullable|string|max:100',
            'rating'               => 'nullable|numeric|min:0|max:5',
            'is_available'         => 'required|boolean',
            'can_bundle_with_room' => 'required|boolean', // Aturan baru
            'paket_items'          => 'nullable|array',   // Aturan baru
            'paket_items.*'        => 'exists:restaurant_menus,id',
            'foto_url'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['foto_url', 'paket_items']);

        if ($request->hasFile('foto_url')) {
            if ($menu->foto_url) {
                Storage::disk('public')->delete($menu->foto_url);
            }
            $data['foto_url'] = $request->file('foto_url')->store('menus', 'public');
        }

        $menu->update($data);

        // Update isi paket (jika kategorinya masih paket, jika bukan, hapus isi pivotnya)
        if ($menu->category === 'paket' && $request->has('paket_items')) {
            $menu->paketItems()->sync($request->paket_items);
        } else {
            $menu->paketItems()->detach();
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(RestaurantMenu $menu)
    {
        if ($menu->foto_url) {
            Storage::disk('public')->delete($menu->foto_url);
        }
        
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus!');
    }
}