<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth-side guard: suspended accounts lose access to protected areas
 * immediately — even if they still hold a valid session cookie. The
 * session is destroyed so the account cannot keep operating.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSuspended()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been suspended.'], 403);
            }

            return redirect()->route('suspended');
        }

        return $next($request);
    }
}
