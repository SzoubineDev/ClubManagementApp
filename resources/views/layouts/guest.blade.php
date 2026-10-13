@props(['bodyClass' => 'auth-body', 'wide' => false])

<x-base-layout :body-class="$bodyClass">
    <div class="auth-split {{ $wide ? 'auth-split--wide' : '' }}">
        {{-- Brand panel (Angular-style split login) --}}
        <aside class="auth-side">
            <img class="bg"
                src="/images/hero.jpg"
                alt="">
            <div class="scrim"></div>
            <div class="auth-side-inner">
                <a href="/" class="auth-side-brand toolbar-brand">
                    <span class="toolbar-logo-badge">UC</span>
                    {{ config('app.name', 'Uni Club') }}
                </a>
                <div>
                    <p class="auth-side-quote">
                        Where students <em>become community.</em>
                    </p>
                    <p class="auth-side-note">
                        Debates, workshops, and three language teams — your campus home since 2024.
                    </p>
                </div>
                <p class="auth-side-foot">© {{ date('Y') }} {{ config('app.name', 'Uni Club') }}</p>
            </div>
        </aside>

        {{-- Form panel --}}
        <main class="auth-main">
            <div class="auth-card {{ $wide ? 'auth-card--form' : '' }}">
                <a href="/" class="auth-mobile-brand">{{ config('app.name', 'Uni Club') }}</a>
                {{ $slot }}
            </div>
        </main>
    </div>
</x-base-layout>
