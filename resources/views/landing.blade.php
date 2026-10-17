<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Great Debaters Club — Think. Speak. Change.</title>
    <meta name="description" content="The Great Debaters Club (TGD) at FST Errachidia — a student community built around public speaking, critical thinking, argumentation, and intellectual exchange.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto+Condensed:wght@400;700;900&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
</head>

<body class="nb" x-data="{ navOpen: false }">

    {{-- ======================= NAVIGATION ======================= --}}
    <nav class="nb-nav" aria-label="Main navigation">
        <div class="nb-nav-inner">
            <a href="{{ route('landing') }}" class="nb-wordmark" aria-label="The Great Debaters Club — home">
                <span class="nb-wordmark-badge">TGD</span>
                THE GREAT<br>DEBATERS
            </a>

            <ul class="nb-links">
                <li><a href="#top" class="is-active">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#events">Events</a></li>
                <li><a href="#join">Contact</a></li>
            </ul>

            <div class="nb-nav-actions">
                @auth
                <a href="{{ route('dashboard') }}" class="nb-btn nb-btn--gold">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="nb-btn nb-btn--outline">Log in</a>
                @endauth
                <a href="{{ route('register') }}" class="nb-btn nb-btn--primary">Join the club</a>
            </div>

            <button type="button" class="nb-burger" @click="navOpen = !navOpen"
                :aria-expanded="navOpen ? 'true' : 'false'" aria-controls="mobile-menu" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>

        <div id="mobile-menu"
            x-show="navOpen"
            style="background: #f5f0e6; color: #65151b; padding: 20px; position: absolute; top: 100%; left: 0; right: 0; z-index: 9999;">
            <ul>
                <li><a href="#top" @click="navOpen = false">Home</a></li>
                <li><a href="#about" @click="navOpen = false">About</a></li>
                <li><a href="#events" @click="navOpen = false">Events</a></li>
                <li><a href="#join" @click="navOpen = false">Contact</a></li>
                <li><a href="{{ route('login') }}" @click="navOpen = false">Log in</a></li>
                <li><a href="{{ route('register') }}" class="nb-btn nb-btn--secondary" style="width:auto;" @click="navOpen = false">Join the club</a></li>
            </ul>
        </div>
    </nav>

    {{-- ======================= HERO ======================= --}}
    <header class="nb-hero" id="top">
        <div class="nb-container">
            <div class="nb-hero-grid">
                <div>
                    <div class="nb-hero-kicker">
                        <span class="nb-label nb-label--invert">FST Errachidia · Issue Nº01</span>
                        <span class="nb-label nb-label--gold">Your voice matters</span>
                    </div>

                    <h1 class="nb-hero-title">
                        THINK<span class="dot">.</span><br>
                        <span class="line-burg">SPEAK<span class="dot">.</span></span><br>
                        CHANGE<span class="dot">.</span>
                    </h1>

                    <p class="nb-hero-sub nb-body">
                        The Great Debaters Club is a student community built around public
                        speaking, critical thinking, argumentation, and intellectual
                        exchange. We turn quiet opinions into powerful arguments.
                    </p>

                    <div class="nb-hero-actions">
                        <a href="#about" class="nb-btn nb-btn--primary">Discover the club ↓</a>
                        <a href="#events" class="nb-btn">Explore events →</a>
                    </div>
                </div>

                {{-- CSS-driven typographic composition (no photos invented) --}}
                <div class="nb-hero-art" aria-hidden="true">
                    <div class="nb-art-card nb-art-a">
                        <div class="big">Words<br>win<br>debates.</div>
                        <div class="small">Est. student union · TGD</div>
                    </div>
                    <div class="nb-art-card nb-art-c"></div>
                    <div class="nb-art-stamp">YOUR<br>VOICE<br>MATTERS</div>
                    <div class="nb-art-card nb-art-b">Admit one · Free speech</div>
                    <div class="nb-art-card nb-art-d"></div>
                </div>
            </div>
        </div>
    </header>

    {{-- ======================= ABOUT ======================= --}}
    <section class="nb-about" id="about" aria-labelledby="about-title">
        <div class="nb-container">
            <div class="nb-about-grid">
                <div class="nb-section-num" aria-hidden="true">01</div>
                <div class="nb-about-copy">
                    <span class="nb-label nb-label--flat">Who we are</span>
                    <h2 class="nb-h2" id="about-title" style="margin-top:18px;">More than<br><em>words.</em></h2>
                    <p class="nb-body">
                        TGD is not a lecture hall. It's a training ground. Every week our
                        members step up, take a side — often the one they disagree with —
                        and defend it under pressure. We build confidence through public
                        speaking, sharpen judgment through critical thinking, learn to
                        argue as a team, and trade ideas that survive contact with
                        opposition.
                    </p>
                    <blockquote class="nb-quote">
                        "A debate club is a gymnasium for the mind."
                        <footer>— The TGD charter</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= PILLARS ======================= --}}
    <section class="nb-pillars" aria-labelledby="pillars-title">
        <div class="nb-container">
            <div class="nb-pillars-head">
                <div>
                    <span class="nb-label nb-label--invert">What you train</span>
                    <h2 class="nb-h2" id="pillars-title" style="margin-top:16px;">Three <em>pillars.</em></h2>
                </div>
                <span class="nb-label nb-label--flat">Nº02 — Core program</span>
            </div>

            <div class="nb-pillar-grid">
                <article class="nb-pillar">
                    <div class="nb-pillar-num">01</div>
                    <h3>Public Speaking</h3>
                    <p class="nb-body">Structured rounds, improvisation drills and mic
                        sessions that turn stage-fright into stage-presence. You leave able
                        to hold any room.</p>
                    <span class="nb-pillar-tag">Voice</span>
                </article>

                <article class="nb-pillar nb-pillar--dark">
                    <div class="nb-pillar-num">02</div>
                    <h3>Critical Thinking</h3>
                    <p class="nb-body">Every claim gets interrogated. Learn to dismantle an
                        argument, spot fallacies, and rebuild your own position on stronger
                        evidence.</p>
                    <span class="nb-pillar-tag">Logic</span>
                </article>

                <article class="nb-pillar">
                    <div class="nb-pillar-num">03</div>
                    <h3>Powerful Ideas</h3>
                    <p class="nb-body">Cross-disciplinary motions — tech, society, ethics,
                        campus life. Ideas collide, get stress-tested, and come out sharper
                        on both sides of the house.</p>
                    <span class="nb-pillar-tag">Substance</span>
                </article>
            </div>
        </div>
    </section>

    {{-- ======================= EVENTS ======================= --}}
    @php
    $upcomingEvents = \App\Models\Event::whereNotNull('published_at')
    ->where('published_at', '<=', now())
        ->where('starts_at', '>=', now())
        ->orderBy('starts_at')
        ->limit(3)
        ->get();
        @endphp
        <section class="nb-events" id="events" aria-labelledby="events-title">
            <div class="nb-container">
                <div class="nb-events-head">
                    <div>
                        <span class="nb-label nb-label--gold">Nº03 — Calendar</span>
                        <h2 class="nb-h2" id="events-title" style="margin-top:16px;">What's <em>next?</em></h2>
                        <p class="nb-body">Weekly practice rounds, inter-faculty exhibitions and
                            end-of-term showdowns. The full programme lives inside the club
                            app — log in or join to see dates, venues and seats left.</p>
                    </div>
                    <a href="{{ route('login') }}" class="nb-btn nb-btn--gold">View all events →</a>
                </div>

                @if ($upcomingEvents->count())
                <div class="nb-pillar-grid">
                    @foreach ($upcomingEvents as $event)
                    <article class="nb-pillar">
                        <div class="nb-pillar-num">{{ $loop->iteration }}</div>
                        <h3>{{ $event->title }}</h3>
                        <p class="nb-body">
                            {{ $event->starts_at->format('D d M Y · H:i') }}<br>
                            {{ $event->location ?: 'Venue TBA' }}
                        </p>
                        <span class="nb-pillar-tag">{{ $event->team ? Str::upper($event->team) : 'Open round' }}</span>
                    </article>
                    @endforeach
                </div>
                @else
                <div class="nb-events-empty">
                    <strong>The floor is open.</strong>
                    <span>No published events right now — new rounds are being scheduled.
                        Join the club and be first to know when the mic turns on.</span>
                    <div style="margin-top:24px;">
                        <a href="{{ route('register') }}" class="nb-btn nb-btn--gold">Get notified when I join</a>
                    </div>
                </div>
                @endif
            </div>
        </section>

        {{-- ======================= FINAL CTA ======================= --}}
        <section class="nb-cta" id="join" aria-labelledby="cta-title">
            <div class="nb-container">
                <div class="nb-cta-box">
                    <div>
                        <span class="nb-label nb-label--flat" style="background:var(--nb-cream);">Nº04 — Last word</span>
                        <h2 id="cta-title" style="margin-top:16px;">Your voice<br><em>belongs here.</em></h2>
                        <p class="nb-body">One membership. Three languages. Zero excuses.
                            Pull up a chair, take the opposing side, and argue like it
                            matters — because it does.</p>
                    </div>
                    <div style="display:grid;gap:14px;">
                        <a href="{{ route('register') }}" class="nb-btn nb-btn--gold">Join the club</a>
                        <a href="{{ route('login') }}" class="nb-btn" style="background:transparent;color:var(--nb-cream);border-color:var(--nb-cream);box-shadow:4px 4px 0 var(--nb-cream);">Member log in</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================= FOOTER ======================= --}}
        <footer class="nb-footer">
            <div class="nb-container">
                <div class="nb-footer-grid">
                    <div>
                        <span class="nb-footer-brand">TGD<span style="color:var(--nb-gold);">.</span></span>
                        <p>The Great Debaters Club — a student community at FST Errachidia
                            built around public speaking, critical thinking and
                            intellectual exchange.</p>
                    </div>
                    <div>
                        <h4>Navigate</h4>
                        <ul>
                            <li><a href="#top">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#events">Events</a></li>
                            <li><a href="#join">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4>Members</h4>
                        <ul>
                            <li><a href="{{ route('login') }}">Log in</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                            <li><a href="{{ route('password.recover') }}">Forgot password?</a></li>
                        </ul>
                    </div>
                </div>
                <div class="nb-footer-bottom">
                    <span>© {{ date('Y') }} The Great Debaters Club · FST Errachidia</span>
                    <span>Think. Speak. Change.</span>
                </div>
            </div>
        </footer>

</body>

</html>