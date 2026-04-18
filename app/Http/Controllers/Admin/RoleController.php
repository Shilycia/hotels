<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id', 'desc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        // Tambahkan validasi untuk slug
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug' // Slug harus unik
        ]);

        Role::create($validated);

        return redirect()->route('admin.roles')->with('success', 'Role baru berhasil ditambahkan!');
    }

    public function update(Request $request, Role $role)
    {
        // Pengecualian unique slug untuk ID role yang sedang diedit
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id 
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles')->with('success', 'Data Role berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Role ini tidak bisa dihapus karena masih digunakan.');
        }

        $role->delete();
        return redirect()->back()->with('success', 'Role berhasil dihapus!');
    }
}