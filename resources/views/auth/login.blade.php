<x-guest-layout>
{{-- Page-level Alpine scope --}}
<div x-data="{ loading: false }">

    <div class="mb-6">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-sub">Sign in to continue to {{ config('app.name', 'Uni Club') }}.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Validation summary --}}
    @if ($errors->count() > 0)
    <div class="alert alert-danger mb-4">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
        <div>
            <p class="alert-title">Please fix the following:</p>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" autocomplete="off"
        x-on:submit="loading = true">
        @csrf

        <x-floating-input name="email" label="Email" type="email" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" />

        <x-password-strength name="password" label="Password" autocomplete="current-password" :rules="false" />
        <x-input-error :messages="$errors->get('password')" />

        <div class="auth-row">
            <label class="flex items-center gap-2 text-sm text-muted cursor-pointer">
                <input type="checkbox" name="remember" id="remember" class="checkbox" @checked(old('remember'))>
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
            <span x-text="loading ? 'Signing in…' : 'Sign in'">Sign in</span>
        </button>
    </form>

    <p class="auth-alt">
        New here?
        <a href="{{ route('register') }}">Create an account</a>
    </p>

</div>

</x-guest-layout>
