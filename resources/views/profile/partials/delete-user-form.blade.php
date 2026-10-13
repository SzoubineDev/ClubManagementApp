<section class="card">
    <header class="card-header">
        <div>
            <h2 class="card-title">{{ __('Delete Account') }}</h2>
            <p class="card-subtitle">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>
        </div>
    </header>

    <div class="card-body flex items-center justify-between gap-4">
        <p class="text-sm text-muted">{{ __('This action cannot be undone.') }}</p>
        <x-danger-button class="btn-warn"
            x-on:click="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="dialog-body">
            @csrf
            @method('delete')

            <h2 class="dialog-title">{{ __('Are you sure you want to delete your account?') }}</h2>

            <p class="dialog-content">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="dialog-content" style="padding-top: 0;">
                <x-floating-input name="password" label="Password" type="password" class="mt-1" placeholder="" />
                <x-input-error class="mt-2" :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="dialog-actions">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="btn-warn ml-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
