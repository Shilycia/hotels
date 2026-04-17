<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        return response()->json(Guest::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:50',
            'email'           => 'required|email|unique:guests,email',
            'phone'           => 'required|string|max:15',
            'identity_number' => 'required|string|unique:guests,identity_number',
        ]);

        $guest = Guest::create($validated);

        return response()->json(['message' => 'Data tamu berhasil disimpan', 'data' => $guest], 201);
    }
}