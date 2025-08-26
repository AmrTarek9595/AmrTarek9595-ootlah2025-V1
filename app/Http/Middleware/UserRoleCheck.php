<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class UserRoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        // Check if     user has any of the required roles. Assumes a 'slug' field on the Role model.
        if (!$user->roles()->whereIn('slug', $roles)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        // If the user has the required role, proceed with the request
        return $next($request);
    }
}
