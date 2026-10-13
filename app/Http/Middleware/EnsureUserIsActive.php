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
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('suspended.guest')
                ->with('status', 'Your account has been suspended.');
        }

        return $next($request);
    }
}
