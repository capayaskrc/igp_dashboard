<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (Auth::user()->active) {
            $request->session()->regenerate();

            // Get the authenticated user's role
            $role = Auth::user()->role;

            // Generate the dashboard route based on the role
            $dashboardRoute = Str::lower($role) . '.dashboard';

            return redirect()->intended(route($dashboardRoute, absolute: false));
        } else {
            // User is not active, logout and redirect back with an error message
            Auth::logout();
            return redirect()->back()->with('error', 'Your account is inactive.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
