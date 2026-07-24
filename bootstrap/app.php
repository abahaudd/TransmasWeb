<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Guests hitting auth-protected non-panel routes (e.g. /health) sign in via the panel
        $middleware->redirectGuestsTo(fn () => '/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
