<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-bordeaux">Recover your password</h1>
        <p class="text-sm text-mauve mt-1">
            Enter the information you provided at registration to verify your identity.
        </p>
    </div>

    @if (session('new_password'))
    <div class="mb-6 p-5 bg-green-50 border border-green-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-900">Your account was verified</p>
                <p class="text-xs text-green-700 mt-1">
                    A new password has been generated for <strong>{{ session('recovered_email') }}</strong>.
                </p>

                <div class="mt-3 p-3 bg-white border border-green-200 rounded-lg">
                    <p class="text-xs uppercase tracking-wide text-green-700 font-medium">New password</p>
                    <p class="mt-1 font-mono text-lg text-green-900 break-all">{{ session('new_password') }}</p>
                </div>

                <p class="text-xs text-green-700 mt-3">
                    Save this now — it won't be shown again. You can change it in your profile after logging in.
                </p>

                <a href="{{ route('login') }}"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-bordeaux text-white text-sm font-medium rounded-lg hover:bg-bordeaux-light">
                    Go to login
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.recover.verify') }}" class="space-y-4" autocomplete="off">
        @csrf

        <x-floating-input name="email" label="Email" type="email" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />

        <x-floating-input name="cne" label="CNE" type="text" required />
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

        <x-primary-button class="w-full justify-center">
            Verify & generate new password
        </x-primary-button>

        <p class="text-center text-sm text-mauve mt-4">
            <a href="{{ route('login') }}" class="text-bordeaux font-medium hover:underline">Back to login</a>
        </p>
    </form>

</x-guest-layout>