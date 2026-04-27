<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    // Menyimpan Role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
        ]);

        Role::create($request->only(['name', 'slug']));

        return redirect()->route('admin.roles.index')->with('success', 'Role baru berhasil ditambahkan!');
    }

    // Mengubah data Role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
        ]);

        $role->update($request->only(['name', 'slug']));

        return redirect()->route('admin.roles.index')->with('success', 'Data Role berhasil diperbarui!');
    }

    // Menghapus Role
    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Gagal dihapus! Role ini sedang digunakan oleh staf/admin aktif.');
        }

        $role->delete();
        
        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus!');
    }
}