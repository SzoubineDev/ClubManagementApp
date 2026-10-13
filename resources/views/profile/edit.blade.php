<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-header-title">{{ __('Profile') }}</h1>
            <p class="page-header-sub">Manage your account information, password and photo.</p>
        </div>
    </x-slot>

    <div class="content-wrap content-wrap--narrow stack">
        @if (session('status'))
        <div class="alert alert-success animate-fade-in-down">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <div class="stack stagger">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
