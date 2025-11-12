<?php

namespace App\Http;

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * ----------------------------------------------------------
     * 🌐 Global HTTP Middleware Stack
     * ----------------------------------------------------------
     * These middleware are run during every request to your app.
     */
    protected $middleware = [
        // Keep minimal to avoid Bootstrap conflicts
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * ----------------------------------------------------------
     * 🧩 Route Middleware Groups
     * ----------------------------------------------------------
     * Define middleware groups used by web and API routes.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Optional: enable throttling if needed
            // \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * ----------------------------------------------------------
     * 🪶 Middleware Aliases (Route Middleware)
     * ----------------------------------------------------------
     * These can be assigned to routes or groups individually.
     */
    protected $middlewareAliases = [
        // Core Laravel middleware
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => RedirectIfAuthenticated::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // 🔒 Custom role and security middlewares
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        'cashier.activity' => \App\Http\Middleware\CashierActivityLogger::class,
        'device.auth' => \App\Http\Middleware\VerifyDeviceApiKey::class,
    ];
}
