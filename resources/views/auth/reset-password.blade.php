<div class="mb-6">
    <h1 class="auth-title">Set a new password</h1>
    <p class="auth-sub">Choose a strong password for your account.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="auth-form" autocomplete="off">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <x-floating-input name="email" label="Email" type="email" required :value="$email" autofocus />
    <x-input-error :messages="$errors->get('email')" />

    <x-password-strength name="password" label="New password" />
    <x-input-error :messages="$errors->get('password')" />

    <x-floating-input name="password_confirmation" label="Confirm password" type="password" required
        autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password_confirmation')" />

    <x-primary-button class="btn-block">
        {{ __('Reset Password') }}
    </x-primary-button>
</form>
