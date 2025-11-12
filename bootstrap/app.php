<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register route middleware aliases
        $middleware->alias([
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'device.auth' => \App\Http\Middleware\VerifyDeviceApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return a Page Expired (419) view when unauthenticated hits protected routes
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsHtml()) {
                return response()->view('errors.419', [], 419);
            }
        });
    })->create();
