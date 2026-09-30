<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'cne' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'birthday_day' => ['required', 'integer', 'between:1,31'],
            'birthday_month' => ['required', 'integer', 'between:1,12'],
            'birthday_year' => ['required', 'integer', 'between:1900,' . (date('Y') - 15)],
            'filiere' => ['required', 'in:DEUST,LICENSE,Cycle Ingénieur,Master,Doctorat'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        // Combine into a real date and verify it's valid (e.g. Feb 30 won't pass)
        $day = (int) $request->birthday_day;
        $month = (int) $request->birthday_month;
        $year = (int) $request->birthday_year;

        if (! checkdate($month, $day, $year)) {
            throw ValidationException::withMessages([
                'birthday_day' => __('The date you entered is not valid.'),
            ]);
        }

        $birthday = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cne' => $request->cne,
            'birthday' => $birthday,
            'filiere' => $request->filiere,
            'password' => $request->password,
        ]);

        $user->assignRole('member');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('events.index', absolute: false));
    }
}
