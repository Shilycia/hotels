<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('guest_id')) {
            session(['url.intended' => $request->url()]);
            
            return redirect()->route('guest.login')
                             ->with('error', 'Silakan login terlebih dahulu untuk melanjutkan pesanan.');
        }

        return $next($request);
    }
}