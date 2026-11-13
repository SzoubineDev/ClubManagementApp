<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalize email and CNE
        $email = $request->input('email');
        $cne = $request->input('cne');

        $request->merge([
            'email' => is_string($email) ? strtolower(trim($email)) : $email,
            'cne' => is_string($cne) && trim($cne) !== '' ? strtoupper(trim($cne)) : null,
        ]);

        // If composed birthday is passed directly (Y-m-d), populate birthday parts if missing
        if ($request->filled('birthday') && (! $request->filled('birthday_day') || ! $request->filled('birthday_month') || ! $request->filled('birthday_year'))) {
            $parts = explode('-', (string) $request->input('birthday'));
            if (count($parts) === 3) {
                $request->merge([
                    'birthday_year' => (int) $parts[0],
                    'birthday_month' => (int) $parts[1],
                    'birthday_day' => (int) $parts[2],
                ]);
            }
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'cne' => ['nullable', 'string', 'max:20', 'unique:' . User::class],
            'filiere' => ['nullable', 'string', 'max:150'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        $hasBirthdayParts = $request->filled('birthday_day') || $request->filled('birthday_month') || $request->filled('birthday_year');

        if ($hasBirthdayParts) {
            $maxYear = (int) date('Y') - 14;
            $rules['birthday_day'] = ['required', 'integer', 'between:1,31'];
            $rules['birthday_month'] = ['required', 'integer', 'between:1,12'];
            $rules['birthday_year'] = ['required', 'integer', 'between:1900,' . $maxYear];
        }

        $validated = $request->validate($rules);

        $birthday = null;
        if ($hasBirthdayParts) {
            $day = (int) $request->birthday_day;
            $month = (int) $request->birthday_month;
            $year = (int) $request->birthday_year;

            if (! checkdate($month, $day, $year)) {
                throw ValidationException::withMessages([
                    'birthday_day' => __('The date you entered is not valid.'),
                ]);
            }

            $birthday = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cne' => $request->cne,
            'birthday' => $birthday,
            'filiere' => $request->filiere,
            'password' => Hash::make($request->password),
        ]);

        // Defensive: ensure member role exists before assigning
        Role::findOrCreate('member', 'web');
        $user->assignRole('member');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}

