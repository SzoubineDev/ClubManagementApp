@php
$user = $member ?? \App\Models\User::firstWhere('email', old('email'));
@endphp

{{-- Page-level Alpine scope so `loading` is shared with the submit button --}}
<div x-data="{ loading: false }">

    <div class="mb-6">
        <h1 class="auth-title">Recover account</h1>
        <p class="auth-sub">Enter your CNE or email — we'll generate a temporary password so you can sign in right away
            and change it later.</p>
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

    <form method="POST" action="{{ route('password.recover.store') }}" class="auth-form" autocomplete="off"
        x-on:submit="loading = true">
        @csrf

        <x-floating-input name="identifier" label="CNE or email" type="text" required autofocus />

        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="loading">
            <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
            <span x-text="loading ? 'Generating…' : 'Generate temporary password'">Generate temporary password</span>
        </button>
    </form>

    @if ($user && session('generated_password'))
    <div class="alert alert-neutral mt-4" style="flex-direction:column; align-items:flex-start;">
        <p class="text-sm">Temporary password generated for <strong>{{ $user->email }}</strong>. Sign in with it now,
            then set a new one from your profile:</p>
        <code class="code-box w-full text-center">{{ session('generated_password') }}</code>
    </div>
    @endif

    <p class="auth-alt">
        Remembered it?
        <a href="{{ route('login') }}">Back to login</a>
    </p>

</div>
