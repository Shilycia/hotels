<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk mengelola file

class UserController extends Controller
{
    public function index() {
        $users = User::with('role')->orderBy('id', 'desc')->paginate(10);
        $roles = Role::all(); // Diambil agar modal "Tambah User" punya pilihan role
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'role_id'  => 'required|exists:roles,id',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'foto'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = 'storage/' . $request->file('foto')->store('user_fotos', 'public');
        }

        $validated['password'] = Hash::make($request->password);
        User::create($validated);

        return redirect()->back()->with('success', 'User berhasil ditambah.');
    }

    public function update(Request $request, User $user)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id', // Diubah menjadi role_id dan cek ke tabel roles
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Logika Update Foto
        $fotoPath = $user->foto; // Ambil path foto lama sebagai default

        if ($request->hasFile('foto')) {
            // Hapus foto fisik yang lama dari storage jika ada
            if ($user->foto) {
                $oldPath = str_replace('storage/', '', $user->foto);
                Storage::disk('public')->delete($oldPath);
            }

            // Simpan foto baru ke folder storage/app/public/user_fotos
            $newPath = $request->file('foto')->store('user_fotos', 'public');
            $fotoPath = 'storage/' . $newPath;
        }

        // 3. Update Data Dasar
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'], // Menggunakan role_id sesuai relasi dinamis
            'foto' => $fotoPath,
        ]);

        // 4. Update Password (Hanya jika diisi di form)
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // Hapus file foto dari storage saat user dihapus
        if ($user->foto) {
            $path = str_replace('storage/', '', $user->foto);
            Storage::disk('public')->delete($path);
        }

        $user->delete();
        
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}