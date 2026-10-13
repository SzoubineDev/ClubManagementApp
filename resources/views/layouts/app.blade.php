<x-base-layout x-data="{ mobileOpen: false }">
    <header class="toolbar">
        <div class="toolbar-inner">
            <a href="{{ route('dashboard') }}" class="toolbar-brand">
                <span class="toolbar-logo-badge">UC</span>
                {{ config('app.name', 'Uni Club') }}
            </a>

            <nav class="toolbar-nav">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>

                <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                    {{ __('Events') }}
                </x-nav-link>

                @can('viewAny', \App\Models\User::class)
                <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                    {{ __('Members') }}
                </x-nav-link>
                @endcan
            </nav>

            <div class="toolbar-spacer"></div>

            {{-- Mobile drawer toggle --}}
            <button type="button" class="drawer-toggle" @click="mobileOpen = !mobileOpen" aria-label="Toggle navigation">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>

            {{-- User menu (mat-menu style) --}}
            <div class="menu-relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="user-trigger" @click="open = !open" :aria-expanded="open">
                    <span class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    <span>{{ Auth::user()->name }}</span>
                    <svg class="chevron" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="menu-panel" x-show="open" x-cloak style="display: none;">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <div class="menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile nav sheet --}}
    <div class="mobile-nav" x-show="mobileOpen" x-cloak style="display: none;">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
            {{ __('Events') }}
        </x-responsive-nav-link>
        @can('viewAny', \App\Models\User::class)
        <x-responsive-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
            {{ __('Members') }}
        </x-responsive-nav-link>
        @endcan
        <div class="mobile-nav-section">
            <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>

    @if (trim($__env->yieldContent('header')) !== '')
        <div class="page-header">
            <div class="content-wrap">
                {!! $__env->yieldContent('header') !!}
            </div>
        </div>
    @endif

    <main class="page-content">
        {{ $slot }}
    </main>
</x-base-layout>
