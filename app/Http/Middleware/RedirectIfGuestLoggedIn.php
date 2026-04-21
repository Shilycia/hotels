<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGuestLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika tamu sudah punya session login
        if (session()->has('guest_id')) {
            // Lempar ke halaman profil
            return redirect()->route('guest.profile')->with('success', 'Anda sudah masuk/login.');
        }

        // Jika belum login, izinkan akses ke halaman (login/register)
        return $next($request);
    }
}