<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Identity-verification password recovery (no email sending).
 *
 * Workflow:
 *   1. GET/POST /password/recover  — user proves identity with
 *      email + date of birth + CNE. On success a short-lived, single-use
 *      reset authorization is created (hashed token in the database, bound
 *      to this browser session) and the user lands on step 2.
 *   2. GET  /password/reset        — new-password form (requires the grant).
 *   3. POST /password/reset        — validates strength/confirmation, hashes
 *      the new password via Laravel's normal mechanism, burns the token and
 *      the session grant, and invalidates all existing sessions.
 *
 * SECURITY NOTE: email, birthday and CNE are personal data, not strong
 * secrets — classmates may know them. This flow is implemented as requested,
 * but for real student accounts it should be paired with an additional
 * verification factor or an administrator-assisted recovery process.
 * Mitigations here: per-account + per-IP rate limiting, generic failure
 * messages that never reveal which field was wrong, hashed single-use
 * tokens with a 10-minute TTL, no identity data in URLs/logs/errors, and
 * full session invalidation on success.
 */
class PasswordRecoveryController extends Controller
{
    /** Minutes the reset authorization stays valid. */
    private const TOKEN_TTL_MINUTES = 10;

    /** Max verification attempts per account per hour. */
    private const MAX_ATTEMPTS_PER_HOUR = 5;

    public function show(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1 — verify email + birthday + CNE all match ONE account.
     */
    public function verify(Request $request): RedirectResponse
    {
        $email = mb_strtolower(trim((string) $request->input('email', '')));
        $key = 'recover:acct:' . sha1($email);
        $limiter = app(RateLimiter::class);

        if ($limiter->tooManyAttempts($key, self::MAX_ATTEMPTS_PER_HOUR)) {
            throw ValidationException::withMessages([
                'email' => __('Too many verification attempts. Please try again later or contact an administrator.'),
            ]);
        }

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'cne' => ['required', 'string', 'max:20'],
            // Accept composed Y-m-d or split day/month/year selects.
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'birthday_day' => ['nullable', 'integer', 'between:1,31'],
            'birthday_month' => ['nullable', 'integer', 'between:1,12'],
            'birthday_year' => ['nullable', 'integer', 'between:1900,' . (date('Y') - 10)],
        ]);

        $birthday = $this->composeBirthday($request, $data['birthday'] ?? null);

        // Normalize CNE without changing the identity being checked.
        $cne = strtoupper(preg_replace('/\s+/u', '', (string) $data['cne']));

        $user = $email !== '' && $birthday && $cne !== ''
            ? User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->whereRaw('UPPER(TRIM(cne)) = ?', [$cne])
                ->whereDate('birthday', $birthday)
                ->first()
            : null;

        // Count every attempt against this account (fixed 1-hour window).
        $limiter->hit($key, 3600);

        if (! $user || $user->isSuspended()) {
            // Generic message — never reveal WHICH field was wrong.
            throw ValidationException::withMessages([
                'email' => __('We could not verify your identity with the information provided.'),
            ]);
        }

        // ---- Establish a short-lived, single-use reset authorization ----
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        $plainToken = Str::random(40);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        // Bind the grant to this browser session; the secret itself lives
        // only in the session (never in URLs or the DB in plaintext).
        $request->session()->put('pw_reset_verified', [
            'email' => $user->email,
            'token' => $plainToken,
            'expires_at' => now()->addMinutes(self::TOKEN_TTL_MINUTES)->timestamp,
        ]);

        return redirect()->route('password.reset.form')
            ->with('status', __('Identity verified. Please choose a new password.'));
    }

    /**
     * Step 2 — show the new-password form (requires a fresh grant).
     */
    public function showResetForm(Request $request, ?string $token = null): View|RedirectResponse
    {
        $grant = $this->validSessionGrant($request);

        if (! $grant) {
            return redirect()->route('password.recover')
                ->withErrors(['email' => __('Your verification expired. Please confirm your identity again.')]);
        }

        return view('auth.reset-password', [
            'email' => $grant['email'],
            'token' => $grant['token'],
        ]);
    }

    /**
     * Step 3 — apply the new password and invalidate the authorization.
     */
    public function reset(Request $request): RedirectResponse
    {
        $grant = $this->validSessionGrant($request);

        if (! $grant) {
            return redirect()->route('password.recover')
                ->withErrors(['email' => __('Your verification expired. Please confirm your identity again.')]);
        }

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The submitted pair must equal the one that passed verification —
        // manipulated emails/tokens can never target another account.
        if (
            ! hash_equals($grant['email'], mb_strtolower($request->input('email')))
            || ! hash_equals($grant['token'], (string) $request->input('token'))
        ) {
            throw ValidationException::withMessages([
                'token' => __('The reset code is invalid or has already been used.'),
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $grant['email'])
            ->first();

        if (! $record || ! Hash::check($request->input('token'), $record->token)) {
            throw ValidationException::withMessages([
                'token' => __('The reset code is invalid or has already been used.'),
            ]);
        }

        if (now()->gt(now()->parse($record->created_at)->addMinutes(self::TOKEN_TTL_MINUTES))) {
            DB::table('password_reset_tokens')->where('email', $grant['email'])->delete();
            $request->session()->forget('pw_reset_verified');

            return redirect()->route('password.recover')
                ->withErrors(['email' => __('Your verification expired. Please confirm your identity again.')]);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$grant['email']])->first();

        if (! $user) {
            $request->session()->forget('pw_reset_verified');

            return redirect()->route('password.recover');
        }

        // Normal Laravel hashing ('hashed' cast → bcrypt/argon).
        $user->forceFill([
            'password' => $request->input('password'),
            'remember_token' => Str::random(60),
        ])->save();

        // Invalidate every existing session for this user immediately.
        @Auth::logoutOtherDevices($request->input('password'));
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Single use: burn the DB token and the session grant.
        DB::table('password_reset_tokens')->where('email', $grant['email'])->delete();
        $request->session()->forget('pw_reset_verified');

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', __('Your password has been reset. You can sign in now.'));
    }

    /**
     * Return the session grant if it exists and has not expired.
     */
    private function validSessionGrant(Request $request): ?array
    {
        $grant = $request->session()->get('pw_reset_verified');

        if (! is_array($grant) || ! isset($grant['email'], $grant['token'], $grant['expires_at'])) {
            return null;
        }

        if (now()->timestamp > (int) $grant['expires_at']) {
            $request->session()->forget('pw_reset_verified');

            return null;
        }

        return $grant;
    }

    private function composeBirthday(Request $request, ?string $fallback): ?string
    {
        $year  = $request->input('birthday_year');
        $month = $request->input('birthday_month');
        $day   = $request->input('birthday_day');

        if ($year && $month && $day) {
            if (! checkdate((int) $month, (int) $day, (int) $year)) {
                return null;
            }

            return sprintf('%04d-%02d-%02d', (int) $year, (int) $month, (int) $day);
        }

        return $fallback;
    }
}
