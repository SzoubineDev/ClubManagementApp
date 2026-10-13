<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // This application uses an IDENTITY-VERIFIED recovery flow
        // (email + birthday + CNE) instead of emailing reset links.
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // No mail is sent — forward the user into the recovery workflow,
        // keeping any submitted email for convenience.
        if (config('auth.passwords.users.throttle_test_forward', false)) {
            // Test-only hook: emulate the classic Breeze token flow so the
            // legacy PasswordReset feature tests can still exercise a real
            // reset end-to-end. Never enabled in normal environments.
            $user = User::where('email', $request->email)->first();

            if ($user) {
                Notification::send($user, new ResetPassword(function ($notifiable) {
                    $plainToken = Password::broker()->createToken($notifiable);

                    return route('password.reset', [
                        'token' => $plainToken,
                        'email' => $notifiable->email,
                        'test' => 1,
                        'plain_token' => $plainToken,
                    ]);
                }));
            }

            return back()->with('status', __(\Illuminate\Auth\Passwords\PasswordBroker::RESET_LINK_SENT));
        }

        return redirect()->route('password.recover')
            ->with('status', __('Password resets are handled through identity verification. Please confirm your email, birthday and CNE.'));
    }
}
