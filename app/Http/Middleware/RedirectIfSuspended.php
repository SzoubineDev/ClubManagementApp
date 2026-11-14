<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guest-side guard: if the currently authenticated user is suspended,
 * log them out (invalidating the session) and show the suspension notice.
 */
class RedirectIfSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSuspended()) {
            return redirect()->route('suspended');
        }

        return $next($request);
    }
}
