<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    public function show(): View
    {
        return view('auth.recover-password');
    }

    public function verify(Request $request): RedirectResponse
    {
        // Accept either three separate birthday selects (day/month/year) or a
        // single composed Y-m-d value, so the form component and API clients
        // both work.
        $request->merge([
            'birthday' => $this->composeBirthday($request),
        ]);

        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $user = User::where(function ($q) use ($data) {
                $q->where('email', $data['identifier'])
                    ->orWhere('cne', $data['identifier']);
            })
            ->when($data['birthday'] ?? null, fn ($q, $b) => $q->whereDate('birthday', $b))
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => __('We could not verify your identity with the information provided.'),
            ]);
        }

        $newPassword = Str::password(12);

        $user->password = $newPassword;
        $user->save();

        return redirect()
            ->route('password.recover')
            ->with('new_password', $newPassword)
            ->with('recovered_email', $user->email);
    }

    /**
     * Build a Y-m-d string from birthday_year/month/day inputs, falling back to
     * a raw `birthday` value when the split fields are absent.
     */
    private function composeBirthday(Request $request): ?string
    {
        $year  = $request->input('birthday_year');
        $month = $request->input('birthday_month');
        $day   = $request->input('birthday_day');

        if ($year && $month && $day) {
            return sprintf('%04d-%02d-%02d', (int) $year, (int) $month, (int) $day);
        }

        return $request->input('birthday');
    }
}
