<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGuestLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('guest_id')) {
            return back()->with('info', 'Anda sudah login.');
        }

        return $next($request);
    }
}