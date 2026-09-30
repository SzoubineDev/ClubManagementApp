<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4" autocomplete="off">
        @csrf

        <x-floating-input name="name" label="Full name" type="text" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />

        <x-floating-input name="email" label="Email" type="email" required autocomplete="off" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />

        <x-floating-input name="cne" label="CNE" type="text" required autocomplete="off" />
        <x-input-error :messages="$errors->get('cne')" class="mt-1" />

        <div>
            <x-input-label for="birthday" :value="__('Birthday')" />
            <x-text-input id="birthday" name="birthday" type="date" class="block mt-1 w-full" :value="old('birthday')" required />
            <x-input-error :messages="$errors->get('birthday')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="filiere" :value="__('Filière')" />
            <select id="filiere" name="filiere" required
                class="block mt-1 w-full border-blush focus:border-bordeaux focus:ring-bordeaux rounded-xl shadow-sm px-4 py-3 text-base">
                <option value="">Select your filière</option>
                @foreach (['DEUST', 'LICENSE', 'Cycle Ingénieur', 'Master', 'Doctorat'] as $f)
                <option value="{{ $f }}" @selected(old('filiere')===$f)>{{ $f }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('filiere')" class="mt-1" />
        </div>

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