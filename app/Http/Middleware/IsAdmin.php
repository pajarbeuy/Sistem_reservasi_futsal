<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthorized access. Admin only.',
            ], 403);
        }

        abort(403, 'Unauthorized access. Admin only.');
    }
}
