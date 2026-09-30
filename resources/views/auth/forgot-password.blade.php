<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-bordeaux">Forgot password?</h1>
        <p class="text-sm text-mauve mt-1">
            Enter your email and we'll send you a reset link.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4" autocomplete="off">
        @csrf

        <x-floating-input
            name="email"
            label="Email"
            type="email"
            required
            autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />

        <x-primary-button class="w-full justify-center">
            {{ __('Email password reset link') }}
        </x-primary-button>

        <p class="text-center text-sm text-mauve mt-4">
            <a href="{{ route('login') }}" class="text-bordeaux font-medium hover:underline">Back to login</a>
        </p>
    </form>
</x-guest-layout>