<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has the role of 'superadmin'
        if (auth()->check() && auth()->user()->role === 'superadmin') {
            return $next($request);
        }

        // If not, abort with a 403 error
        abort(403, 'Unauthorized access.');
    }
}
