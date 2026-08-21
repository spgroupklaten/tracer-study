<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Convert role names to Role enum
        $allowedRoles = array_map(function (string $roleName) {
            return Role::from($roleName);
        }, $roles);

        // Check if user's role is in allowed roles
        $userRole = $request->user()->role;

        if (!in_array($userRole, $allowedRoles)) {
            // If AJAX request, return 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. You do not have permission to access this resource.'], 403);
            }

            // For regular request, abort with 403
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
