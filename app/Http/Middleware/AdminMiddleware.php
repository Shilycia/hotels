<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Menangani permintaan yang masuk.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Anda harus login sebagai admin untuk mengakses halaman ini.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            abort(403, 'Akses ditolak. Hanya admin/super-admin yang diizinkan.');
        }

        return $next($request);
    }
}