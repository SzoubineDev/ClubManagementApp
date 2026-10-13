<section class="card">
    <header class="card-header">
        <div>
            <h2 class="card-title">{{ __('Update Password') }}</h2>
            <p class="card-subtitle">{{ __('Use a long, random password to stay secure.') }}</p>
        </div>
    </header>

    <div x-data="{ loading: false }">
        <form method="POST" action="{{ route('password.update') }}" class="card-body space-y-6"
            x-on:submit="loading = true">
            @csrf
            @method('put')

            <div>
                <x-floating-input name="current_password" label="Current password" type="password"
                    autocomplete="current-password" />
                <x-input-error class="mt-2" :messages="$errors->updatePassword->get('current_password')" />
            </div>

            <x-password-strength name="password" label="New password" />
            <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password')" />

            <div>
                <x-floating-input name="password_confirmation" label="Confirm new password" type="password"
                    autocomplete="new-password" />
                <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password_confirmation')" />
            </div>

            <div class="flex items-center justify-between border-t pt-4">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span class="spinner" x-show="loading" x-cloak style="display:none;"></span>
                    <span>{{ __('Save') }}</span>
                </button>

                @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-muted">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
