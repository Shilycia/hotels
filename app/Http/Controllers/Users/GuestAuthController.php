<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuestAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-guest');
    }

    // 2. Memproses Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $guest = Guest::where('email', $request->email)->first();

        if ($guest && Hash::check($request->password, $guest->password)) {
            // Set session login tamu
            session(['guest_id' => $guest->id]);
            session(['guest_name' => $guest->name]);

            $intendedUrl = session('url.intended', route('home'));
            session()->forget('url.intended'); 

            return redirect()->to($intendedUrl)->with('success', 'Selamat datang kembali, ' . $guest->name . '!');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi tidak valid.'])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register-guest');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'identity_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $guest = Guest::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'identity_number' => $request->identity_number,
            'address' => $request->address,
        ]);

        session(['guest_id' => $guest->id]);
        session(['guest_name' => $guest->name]);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil! Akun Anda telah aktif.');
    }

    public function profile()
    {
        // Pastikan tamu sudah login
        if (!session()->has('guest_id')) {
            return redirect()->route('guest.login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses profil.');
        }

        $guestId = session('guest_id');

        // Ambil data Guest
        $guest = Guest::findOrFail($guestId);

        // Ambil riwayat pemesanan kamar (beserta relasi ke room dan roomType)
        $bookings = \App\Models\Booking::with(['room.roomType', 'payment'])
            ->where('guest_id', $guestId)
            ->latest()
            ->get();

        // Ambil riwayat pemesanan restoran (beserta detail menu dan payment)
        $restaurantOrders = \App\Models\RestaurantOrder::with(['details.menu', 'payment'])
            ->where('guest_id', $guestId)
            ->latest()
            ->get();

        return view('users.pages.profile', compact('guest', 'bookings', 'restaurantOrders'));
    }

    public function updateProfile(Request $request)
    {

        $guest = Guest::findOrFail(session('guest_id'));

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Nama input form tetap 'foto', ini tidak apa-apa
        ]);

        $data = $request->only(['name', 'email', 'phone', 'identity_number', 'address']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada (Cek photo_url)
            if ($guest->photo_url) {
                Storage::disk('public')->delete($guest->photo_url);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('guests', 'public');
            
            // Masukkan ke database dengan nama kolom 'photo_url'
            $data['photo_url'] = $path; 
        }

        $guest->update($data);

        session(['guest_name' => $guest->name]);

        return redirect()->route('guest.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['guest_id', 'guest_name', 'url.intended']);
        return redirect()->route('guest.login')->with('success', 'Anda telah berhasil logout.');
    }
}