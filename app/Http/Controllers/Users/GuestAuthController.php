<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class GuestAuthController extends Controller
{
    // Menampilkan form login tamu
    public function showLogin()
    {
        return view('auth.login-guest'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6',
        ]);

        $guest = Guest::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        session(['guest_id' => $guest->id, 'guest_name' => $guest->name]);

        return redirect()->route('booking')->with('success', 'Registrasi berhasil! Silakan lanjutkan pesanan Anda.');
    }

    public function showRegister()
    {
        return view('.auth.register-guest'); 
    }

    public function showForgot()
    {
        return view('auth.forgot-password-guest');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:guests,email',
        ], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.'
        ]);

        // Buat token unik
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        $resetLink = route('guest.password.reset', ['token' => $token, 'email' => $request->email]);

        Mail::send('auth.email-reset', ['link' => $resetLink], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password - Hotelier');
        });

        return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda!');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('users.pages.form-reset-password', [
            'token' => $token, 
            'email' => $request->email
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:guests,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kedaluwarsa.']);
        }

        $guest = Guest::where('email', $request->email)->first();
        $guest->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('guest.login')->with('status', 'Password Anda berhasil diubah! Silakan login.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $guest = Guest::where('email', $request->email)->first();

        if ($guest && Hash::check($request->password, $guest->password)) {
            session(['guest_id' => $guest->id, 'guest_name' => $guest->name]);
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $guest->name);
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout()
    {
        session()->forget(['guest_id', 'guest_name']);
        return redirect()->route('home');
    }
}