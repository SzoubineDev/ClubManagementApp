<div class="mb-6">
    <h1 class="auth-title">Confirm password</h1>
    <p class="auth-sub">This is a secure area of the application. Please confirm your password before continuing.</p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="auth-form" autocomplete="off">
    @csrf

    <x-floating-input name="password" label="Password" type="password" required autocomplete="current-password" autofocus />
    <x-input-error :messages="$errors->get('password')" />

    <x-primary-button class="btn-block">
        {{ __('Confirm') }}
    </x-primary-button>
</form>
