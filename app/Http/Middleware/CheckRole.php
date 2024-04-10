<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user has the required role
            if ($request->user()->role === $role) {
                return $next($request);
            }
        }

        // If the user is not authenticated or doesn't have the required role, redirect them
        return redirect('/login');
    }
}
