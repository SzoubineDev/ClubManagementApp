<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-bordeaux">Join the club</h1>
        <p class="text-sm text-mauve mt-1">Create your account in less than a minute.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" autocomplete="off">
        @csrf

        <x-floating-input name="name" label="Full name" type="text" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />

        <x-floating-input name="email" label="Email" type="email" required autocomplete="off" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />

        <x-floating-input name="cne" label="CNE" type="text" required autocomplete="off" />
        <x-input-error :messages="$errors->get('cne')" class="mt-1" />

        <x-birthday-select name="birthday" label="Birthday" />
        <x-input-error :messages="$errors->get('birthday')" class="mt-1" />

        <x-filiere-select name="filiere" label="Filière" />
        <x-input-error :messages="$errors->get('filiere')" class="mt-1" />

        <x-password-strength name="password" label="Password" />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />

        <x-floating-input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />

        <x-primary-button class="w-full justify-center">
            {{ __('Create account') }}
        </x-primary-button>

        <p class="text-center text-sm text-mauve mt-4">
            Already a member?
            <a href="{{ route('login') }}" class="text-bordeaux font-medium hover:underline">Log in</a>
        </p>
    </form>

</x-guest-layout>