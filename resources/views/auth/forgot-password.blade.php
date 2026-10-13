<div class="mb-6">
    <h1 class="auth-title">Forgot password?</h1>
    <p class="auth-sub">Enter your email and we'll send you a reset link.</p>
</div>

<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('password.email') }}" class="auth-form" autocomplete="off">
    @csrf

    <x-floating-input name="email" label="Email" type="email" required autofocus />
    <x-input-error :messages="$errors->get('email')" />

    <x-primary-button class="btn-block">
        {{ __('Email password reset link') }}
    </x-primary-button>

    <p class="auth-alt">
        <a href="{{ route('login') }}">Back to login</a>
    </p>
</form>
