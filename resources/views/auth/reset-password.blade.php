<x-guest-layout wide>
<div x-data="{ loading: false }">

    <div class="mb-6">
        <h1 class="auth-title">Set a new password</h1>
        <p class="auth-sub">Identity verified for <strong>{{ $email }}</strong>. Choose a strong password — this authorization expires in 10 minutes and works once.</p>
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

    <form method="POST" action="{{ route('password.reset.apply') }}" class="auth-form" autocomplete="off"
        x-on:submit="loading = true">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <input type="hidden" name="email" value="{{ $email }}">

        <x-password-strength name="password" label="New password" autofocus />
        <x-input-error :messages="$errors->get('password')" />

        <x-floating-input name="password_confirmation" label="Confirm password" type="password" required
            autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" />

        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
            <span x-text="loading ? 'Saving…' : 'Change password'">Change password</span>
        </button>
    </form>

    <p class="auth-alt">
        <a href="{{ route('login') }}">Cancel and go back to login</a>
    </p>

</div>
</x-guest-layout>
