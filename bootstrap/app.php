<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        $middleware->alias([
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'guest.auth'  => \App\Http\Middleware\GuestMiddleware::class,
            'guest.guest' => \App\Http\Middleware\RedirectIfGuestLoggedIn::class,
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/midtrans/callback', 
            'midtrans/callback',
            '/webhook/midtrans/callback', // Tambahan sesuai dokumen rancangan
            'webhook/midtrans/callback'
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();