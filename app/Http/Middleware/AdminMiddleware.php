<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has admin role.
     * Returns 403 JSON for API requests, or aborts with 403 for web requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
            }

            abort(403, 'Forbidden. Admin access required.');
        }

        return $next($request);
    }
}
