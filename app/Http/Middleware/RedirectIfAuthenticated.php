<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            // Check if the user is trying to access the /register route
            if ($request->is('register') && auth()->user()->role === 'superadmin') {
                // Allow superadmins to access the /register route
                return $next($request);
            }

            // Redirect authenticated users (except superadmins accessing /register)
            return redirect(RouteServiceProvider::HOME);
        }
    }

    return $next($request);
}
}
