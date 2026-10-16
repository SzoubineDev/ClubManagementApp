@props(['bodyClass' => 'auth-body', 'wide' => false])

<x-base-layout body-class="auth-body nb-auth" :extra-css="['resources/css/auth.css']">
    <div class="auth-split {{ $wide ? 'auth-split--wide' : '' }}">
        {{-- Brand panel — preserves the club photo (public/images/auth-bg.jpg) --}}
        <aside class="auth-side">
            <img class="bg"
                src="{{ asset('images/auth-bg.jpg') }}"
                alt="The Great Debaters Club">
            <div class="scrim"></div>
            <div class="auth-side-inner">
                <a href="/" class="auth-side-brand">
                    <span class="toolbar-logo-badge">TGD</span>
                    The Great<br>Debaters
                </a>
                <div>
                    <p class="auth-side-quote">
                        Think. Speak.<br><em>Change.</em>
                    </p>
                    <p class="auth-side-note">
                        A student community at FST Errachidia built around public speaking,
                        critical thinking and intellectual exchange. FST Errachidia · Issue Nº01
                    </p>
                </div>
                <p class="auth-side-foot">© {{ date('Y') }} The Great Debaters Club</p>
            </div>
        </aside>

        {{-- Form panel --}}
        <main class="auth-main">
            <div class="auth-card {{ $wide ? 'auth-card--form' : '' }}">
                <a href="/" class="auth-mobile-brand">TGD<span style="color:var(--nb-gold);">.</span></a>
                {{ $slot }}
            </div>
        </main>
    </div>
</x-base-layout>
