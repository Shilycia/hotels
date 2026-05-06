<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $userRole = Auth::user()->role->slug ?? '';

        // Normalisasi: bandingkan slug tanpa peduli dash vs underscore
        $normalizedUserRole = str_replace('-', '_', $userRole);
        $normalizedRoles    = array_map(fn($r) => str_replace('-', '_', $r), $roles);

        if (in_array($normalizedUserRole, $normalizedRoles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak. Anda tidak memiliki wewenang untuk membuka halaman ini.');
    }
}