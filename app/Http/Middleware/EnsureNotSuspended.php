<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuspended()) {
            return $next($request);
        }

        // Allowed routes for suspended users
        $allowed = [
            'suspended',
            'logout',
            'profile.edit',
            'profile.update',
            'profile.destroy',
        ];

        if ($request->routeIs($allowed)) {
            return $next($request);
        }

        return redirect()->route('suspended');
    }
}
