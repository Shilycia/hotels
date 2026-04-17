<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Mengambil user langsung dari request yang sudah melewati auth:sanctum
        $user = $request->user();

        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Anda tidak memiliki hak akses Admin.'
        ], 403);
    }
}
