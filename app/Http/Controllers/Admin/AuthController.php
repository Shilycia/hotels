<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login admin.
     */
    public function showLoginForm()
    {
        // Pastikan kamu memiliki file view di resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Memproses data yang dikirim dari form login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input dari user
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Opsional: Cek apakah user mencentang fitur "Remember Me"
        $remember = $request->has('remember');

        // 2. Coba melakukan autentikasi
        if (Auth::attempt($credentials, $remember)) {
            // Jika sukses, buat ulang session untuk mencegah serangan Session Fixation
            $request->session()->regenerate();

            // Arahkan ke rute dashboard admin
            return redirect()->intended(route('admin.dashboard'));
        }

        // 3. Jika gagal (email/password salah), kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Tetap pertahankan isian email agar user tidak perlu mengetik ulang
    }

    /**
     * Memproses aksi logout admin.
     */
    public function logout(Request $request)
    {
        // 1. Lakukan proses logout
        Auth::logout();

        // 2. Hapus seluruh data session yang tersimpan
        $request->session()->invalidate();

        // 3. Buat ulang token CSRF demi keamanan
        $request->session()->regenerateToken();

        // 4. Arahkan kembali ke halaman login
        return redirect()->route('login');
    }
}