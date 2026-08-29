<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Flatten roles if passed as variadic or comma-separated string
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $subRole) {
                $allowedRoles[] = trim($subRole);
            }
        }

        // Admin always has access, or if user's role is explicitly allowed
        if ($user->role === 'admin' || in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, "Unauthorized action. Your role ('" . ucfirst($user->role ?? 'User') . "') does not have permission to access this page.");
    }
}