<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // validasi login
        $request->authenticate();

        // keamanan session
        $request->session()->regenerate();

        // update login metadata
        $request->user()->updateLastLogin();

        // simpan activity log langsung
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'activity' => 'login',
            'description' => 'User berhasil login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // ambil role user
        $role = $request->user()->role;

        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'hrd'      => redirect()->route('hrd.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default    => $this->logoutAndRedirect(),
        };
    }

    private function logoutAndRedirect(): RedirectResponse
    {
        Auth::logout();

        return redirect('/login')->withErrors([
            'email' => 'Role not recognized.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->markAsOffline();

            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'logout',
                'description' => 'User logout dari sistem',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
