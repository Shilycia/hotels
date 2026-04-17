<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu;
use Illuminate\Http\Request;

class MenuAdminController extends Controller
{
    public function index()
    {
        return response()->json(RestaurantMenu::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string'
        ]);

        $menu = RestaurantMenu::create($validated);
        return response()->json(['message' => 'Menu berhasil ditambah', 'data' => $menu], 201);
    }
}
