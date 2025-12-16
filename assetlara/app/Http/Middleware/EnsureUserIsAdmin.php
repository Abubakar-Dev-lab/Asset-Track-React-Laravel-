<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is authenticated at all. (Auth middleware does this too, but for safety)
        if (! $request->user()) {
            // If not logged in, force them to the login page (should be caught by 'auth' middleware)
            return redirect()->route('login');
        }

        // 2. The Core Check: Does the role equal 'admin'?
        if ($request->user()->role !== 'admin') {
            // 🛑 If NOT an admin, throw a 403 Forbidden error.
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access: You must be an administrator.');
        }

        // 3. If everything is OK, let the request pass to the controller
        return $next($request);
    }
}
