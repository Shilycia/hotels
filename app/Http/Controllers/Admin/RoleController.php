<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id', 'desc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) 
        ]);

        return redirect()->route('admin.roles')->with('success', 'Role baru berhasil ditambahkan!');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'])
        ]);

        return redirect()->route('admin.roles')->with('success', 'Data Role berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal! Role ini tidak bisa dihapus karena masih digunakan oleh ' . $role->users()->count() . ' pengguna.');
        }

        if ($role->slug === 'admin') {
            return redirect()->back()->with('error', 'Gagal! Role Admin utama tidak boleh dihapus.');
        }

        $role->delete();
        return redirect()->back()->with('success', 'Role berhasil dihapus!');
    }
}