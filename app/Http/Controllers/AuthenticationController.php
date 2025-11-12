<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use App\Models\LogsAdmin;

class AuthenticationController extends Controller
{
    // Show login page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        // Validate inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Basic rate limiting: 5 attempts per minute per email+IP
        $key = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Too many login attempts. Try again in ' . $seconds . ' seconds.')
                ->withErrors(['email' => 'Too many login attempts.']);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, false)) { // ensure admin guard
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();
            $target = $this->redirectPathForRole($user);

            // Log to logs_admin (login)
            try {
                LogsAdmin::create([
                    'admin_id' => optional($user)->admin_id ?? (optional($user)->id ? ('ADM-' . optional($user)->id) : null),
                    'admin_name' => optional($user)->name ?? 'Unknown',
                    'role' => (string) ($user->role ?? 'Administrator'),
                    'type' => 'Account',
                    'action_type' => 'Login',
                    'reference_id' => null,
                    'description' => 'Admin logged in',
                    'log_type' => 'Activity',
                    'ip_address' => $request->ip(),
                    'device' => 'Web',
                    'browser' => (string) $request->header('User-Agent'),
                    'logged_at' => now('Asia/Manila'),
                ]);
            } catch (\Throwable $e) {
                // Do not block login if logging fails
            }

            return redirect()->intended($target)
                ->with('success', 'Logged in successfully.');
        }

        RateLimiter::hit($key, 60);

        return redirect()->back()
            ->with('error', 'Incorrect Email or Password.');
    }

    // Logout
    public function logout(Request $request)
    {
        // Log to logs_admin (logout) before session is cleared
        try {
            $user = Auth::user();
            LogsAdmin::create([
                'admin_id' => optional($user)->admin_id ?? (optional($user)->id ? ('ADM-' . optional($user)->id) : null),
                'admin_name' => optional($user)->name ?? 'Unknown',
                'role' => (string) ($user->role ?? 'Administrator'),
                'type' => 'Account',
                'action_type' => 'Logout',
                'reference_id' => null,
                'description' => 'Admin logged out',
                'log_type' => 'Activity',
                'ip_address' => $request->ip(),
                'device' => 'Web',
                'browser' => (string) $request->header('User-Agent'),
                'logged_at' => now('Asia/Manila'),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    private function redirectPathForRole($user)
    {
        $role = Str::lower((string) ($user->role ?? $user->user_type ?? 'admin'));

        $map = [
            'super admin' => 'super.dashboard',
            'super administrator' => 'super.dashboard',
            'administrator' => 'admin.dashboard',
            'hotel manager' => 'hotelmanager.dashboard',
            'hotel frontdesk' => 'frontdesk.booking',
            'restaurant manager' => 'restomanager.dashboard',
            'restaurant cashier' => 'cashier.menu',
        ];

        $routeName = $map[$role] ?? 'admin.dashboard';

        if (RouteFacade::has($routeName)) {
            return route($routeName);
        }
        if (RouteFacade::has('admin.dashboard')) {
            return route('admin.dashboard');
        }
        return url('/admin');
    }
}
