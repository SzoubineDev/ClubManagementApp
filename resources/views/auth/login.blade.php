<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-bordeaux">Welcome back</h1>
        <p class="text-sm text-mauve mt-1">Log in to continue to your club.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" autocomplete="off">
        @csrf

        <x-floating-input
            name="email"
            label="Email"
            type="email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />

        <x-floating-input
            name="password"
            label="Password"
            type="password"
            required
            autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />

        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-blush text-bordeaux shadow-sm focus:ring-bordeaux" name="remember">
                <span class="ms-2 text-sm text-mauve">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
            <a class="text-sm text-mauve hover:text-bordeaux" href="{{ route('password.recover') }}">
                {{ __('Forgot password?') }}
            </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Log in') }}
        </x-primary-button>

        <p class="text-center text-sm text-mauve mt-4">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-bordeaux font-medium hover:underline">Join us</a>
        </p>
    </form>

</x-guest-layout>