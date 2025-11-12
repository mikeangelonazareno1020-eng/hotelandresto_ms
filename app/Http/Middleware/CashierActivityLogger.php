<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogsAdmin;

class CashierActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log authenticated admin cashier GET/HEAD requests to avoid duplicating controller logs
        if (in_array($request->method(), ['GET', 'HEAD'])) {
            $admin = Auth::user();
            if ($admin) {
                try {
                    $routeName = optional($request->route())->getName();
                    $action = 'View ' . ($routeName ?: $request->path());
                    $query = http_build_query($request->query());
                    $ua = (string) $request->header('User-Agent');

                    LogsAdmin::create([
                        'admin_id' => optional($admin)->admin_id ?? (optional($admin)->id ? ('ADM-' . optional($admin)->id) : null),
                        'admin_name' => optional($admin)->name ?? 'Unknown',
                        'role' => (string) ($admin->role ?? 'Administrator'),
                        'type' => 'Restaurant',
                        'action_type' => $action,
                        'reference_id' => null,
                        'description' => strtoupper($request->method()) . ' ' . $request->path() . ($query ? ('?' . $query) : ''),
                        'log_type' => 'Activity',
                        'ip_address' => $request->ip(),
                        'device' => 'Web',
                        'browser' => substr($ua, 0, 255),
                        'logged_at' => now('Asia/Manila'),
                    ]);
                } catch (\Throwable $e) {
                    // never block the request due to logging
                }
            }
        }

        return $response;
    }
}
