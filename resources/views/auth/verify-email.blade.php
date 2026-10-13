<div class="mb-6">
    <h1 class="auth-title">Verify your email</h1>
    <p class="auth-sub">Thanks for signing up! Before getting started, could you verify your email address by clicking
        on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
</div>

@if (session('status') == 'verification-link-sent')
<div class="alert alert-success mb-4">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <span>A new verification link has been sent to the email address you provided during registration.</span>
</div>
@endif

<form method="POST" action="{{ route('verification.resend') }}" class="auth-form">
    @csrf
    <x-primary-button class="btn-block">
        {{ __('Resend Verification Email') }}
    </x-primary-button>
</form>

<div class="border-t mt-6 pt-4 flex items-center justify-between">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-action">{{ __('Log Out') }}</button>
    </form>
</div>
