<x-guest-layout>
{{-- Page-level Alpine scope so `loading` is shared with the submit button --}}
<div x-data="{ loading: false }">

    <div class="mb-6">
        <h1 class="auth-title">Create your account</h1>
        <p class="auth-sub">Join {{ config('app.name', 'Uni Club') }} in less than a minute.</p>
    </div>

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

    <form method="POST" action="{{ route('register') }}" class="auth-form" autocomplete="off"
        x-on:submit="loading = true">
        @csrf

        <x-floating-input name="name" label="Full name" type="text" required autofocus autocomplete="name" />

        <x-floating-input name="email" label="Email" type="email" required autocomplete="username" />

        <x-floating-input name="cne" label="CNE" type="text" required autocomplete="off" />

        <x-birthday-select />

        <x-filiere-select required />

        <x-password-strength name="password" label="Password" />

        <x-floating-input name="password_confirmation" label="Confirm password" type="password" required
            autocomplete="new-password" />

        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
            <span x-text="loading ? 'Creating account…' : 'Create account'">Create account</span>
        </button>
    </form>

    <p class="auth-alt">
        Already a member?
        <a href="{{ route('login') }}">Log in</a>
    </p>

</div>

</x-guest-layout>
