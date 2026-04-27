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

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak. Anda tidak memiliki wewenang untuk membuka halaman ini.');
    }
}