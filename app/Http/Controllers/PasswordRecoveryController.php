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
        $data = $request->validate([
            'email' => ['required', 'email'],
            'cne' => ['required', 'string'],
            'birthday' => ['required', 'date'],
            'filiere' => ['required', 'in:DEUST,LICENSE,Cycle Ingénieur,Master,Doctorat'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('cne', $data['cne'])
            ->whereDate('birthday', $data['birthday'])
            ->where('filiere', $data['filiere'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('We could not verify your identity with the information provided.'),
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
}
