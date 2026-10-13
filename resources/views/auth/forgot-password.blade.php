<x-guest-layout wide>
<div x-data="{ loading: false }">

    <div class="mb-6">
        <h1 class="auth-title">Verify your identity</h1>
        <p class="auth-sub">To reset your password, confirm the three details tied to your account. We never send reset links by email.</p>
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
            <p class="alert-title">{{ $errors->has('email') && count($errors->get('email')) === 1 ? 'Verification failed' : 'Please fix the following:' }}</p>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.recover.verify') }}" class="auth-form" autocomplete="off"
        x-on:submit="loading = true">
        @csrf

        <x-floating-input name="email" label="Registered email" type="email" required autofocus autocomplete="email" />
        <x-input-error :messages="$errors->get('email')" />

        <div class="field-pair">
            <x-birthday-select />
            <x-floating-input name="cne" label="CNE (student number)" type="text" required autocomplete="off" />
        </div>
        <x-input-error :messages="$errors->get('cne')" />

        <div class="notice-zone notice-zone--info">
            <p class="text-sm">For your security, repeated failed attempts are rate-limited. If you cannot recover access, contact a club administrator for assisted recovery.</p>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
            <span x-text="loading ? 'Verifying…' : 'Verify identity'">Verify identity</span>
        </button>
    </form>

    <p class="auth-alt">
        Remembered it?
        <a href="{{ route('login') }}">Back to login</a>
    </p>

</div>
</x-guest-layout>
