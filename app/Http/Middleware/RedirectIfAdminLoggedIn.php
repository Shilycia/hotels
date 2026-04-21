<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdminLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada sesi login dari sistem Auth bawaan Laravel (untuk Admin/Staf)
        if (Auth::check()) {
            // Lempar ke halaman dashboard admin
            return redirect()->route('admin.dashboard')->with('success', 'Anda sudah login sebagai Admin/Staf.');
        }

        // Jika belum login, izinkan akses ke halaman login admin
        return $next($request);
    }
}