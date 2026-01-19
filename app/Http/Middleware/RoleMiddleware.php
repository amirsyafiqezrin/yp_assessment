<?php

namespace App\Http\Middleware;

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
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        $roleValue = match ($role) {
            'lecturer' => \App\Models\User::ROLE_LECTURER,
            'student' => \App\Models\User::ROLE_STUDENT,
            default => null,
        };

        if ($roleValue === null || $request->user()->role !== $roleValue) {
            abort(403, 'Unauthorized. Access denied for this role.');
        }

        return $next($request);
    }
}
