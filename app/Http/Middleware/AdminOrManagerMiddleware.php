<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has the role of "admin" or "manager"
        if ($request->user() && ($request->user()->role === 'admin' || $request->user()->role === 'manager')) {
            return $next($request);
        }

        // Redirect to unauthorized page or handle unauthorized access
        return redirect('/login');
    }
}
