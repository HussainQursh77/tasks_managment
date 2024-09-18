<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminOrOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized: No authenticated user found.'], 401);
        }

        $user = Auth::user(); // Get the authenticated user

        // For the index method, only admins are allowed
        if ($request->route()->getName() === 'users.index') {
            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized: Only admins can access this resource.'], 403);
            }
            // If the user is an admin, allow them to proceed
            return $next($request);
        }

        // For show, update, and delete methods, allow either admin or the owner of the record
        $routeUser = $request->route('user');

        if ($routeUser) {
            // Check if the authenticated user is either an admin or the owner of the record
            if ($user->role !== 'admin' && $user->id !== $routeUser->id) {
                return response()->json(['message' => 'Unauthorized: You do not have permission to perform this action.'], 403);
            }
        }

        return $next($request);
    }
}
