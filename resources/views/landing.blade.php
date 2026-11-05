<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Great Debaters Club — Think. Speak. Change. · FST Errachidia</title>
    <meta name="description" content="The Great Debaters Club (TGD) at Faculté des Sciences et Techniques Errachidia (Université Moulay Ismaïl) — a student community dedicated to public speaking, critical reasoning, and trilingual debate.">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Roboto+Condensed:wght@400;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/landing.css','resources/css/recrute.css', 'resources/js/app.js'])
</head>

<body class="nb" x-data="{ navOpen: false }">

    {{-- ======================= NAVIGATION BAR ======================= --}}
    <header class="nb-nav" aria-label="Main navigation">
        <div class="nb-nav-inner">
            {{-- Brand --}}
            <a href="{{ route('landing') }}" class="nb-wordmark" aria-label="The Great Debaters Club — Home">
                <div class="nb-wordmark-crest">
                    <img src="{{ asset('images/logo.jpg') }}" alt="TGD Crest">
                </div>
                <div class="nb-wordmark-text">
                    <span class="nb-wordmark-title">The Great Debaters</span>
                    <span class="nb-wordmark-sub">FST Errachidia · UMI</span>
                </div>
            </a>

            {{-- 5 Curated Navigation Links --}}
            <nav class="nb-links" aria-label="Desktop Navigation">
                <a href="#about">About</a>
                <a href="#pillars">Pillars</a>
                <a href="#activities">Activities</a>
                <a href="#events">Calendar</a>
                <a href="#team">The Bureau</a>
            </nav>

            {{-- Right Action Buttons --}}
            <div class="nb-nav-actions">
                @auth
                <a href="{{ route('dashboard') }}" class="nb-btn nb-btn--gold nb-btn--sm">Dashboard →</a>
                @else
                <a href="{{ route('login') }}" class="nb-login-link">Log in</a>
                <a href="{{ route('recrute') }}" class="nb-btn nb-btn--primary nb-btn--sm">Join the club</a>
                @endauth
            </div>

            {{-- Mobile Toggle Button --}}
            <button type="button" class="nb-burger" @click="navOpen = !navOpen"
                :aria-expanded="navOpen ? 'true' : 'false'" aria-controls="mobile-menu" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>

        {{-- Mobile Drawer --}}
        <div id="mobile-menu" class="nb-mobile-drawer" x-show="navOpen" x-cloak style="display: none;"
            @click.outside="navOpen = false">
            <ul class="nb-mobile-links">
                <li><a href="#about" @click="navOpen = false">About the Club</a></li>
                <li><a href="#pillars" @click="navOpen = false">Three Pillars</a></li>
                <li><a href="#activities" @click="navOpen = false">Weekly Activities</a></li>
                <li><a href="#events" @click="navOpen = false">Events Calendar</a></li>
                <li><a href="#team" @click="navOpen = false">The Bureau</a></li>
                <li><a href="#memories" @click="navOpen = false">Photo Memories</a></li>
            </ul>
            <div class="nb-mobile-actions">
                @auth
                <a href="{{ route('dashboard') }}" class="nb-btn nb-btn--gold" @click="navOpen = false">Open Dashboard →</a>
                @else
                <a href="{{ route('recrute') }}" class="nb-btn nb-btn--primary" @click="navOpen = false">Join the Club — Free</a>
                <a href="{{ route('login') }}" class="nb-btn nb-btn--outline" @click="navOpen = false">Member Log In</a>
                @endauth
            </div>
        </div>
    </header>
    {{-- ======================= RECRUITMENT ALERT BANNER ======================= --}}
    <div class="nb-recruit-banner" role="status" aria-label="Recruitment announcement">
        <div class="nb-container">
            <div class="nb-recruit-banner-inner">

                {{-- English row --}}
                <div class="nb-recruit-row" dir="ltr" lang="en">
                    <span class="nb-recruit-dot" aria-hidden="true"></span>
                    <p class="nb-recruit-text">
                        <strong>Recruitment is open</strong> — applications for the 2026/2027 season are now live.
                    </p>
                    <a href="{{ route('recrute') }}" class="nb-recruit-btn">
                        Register Now <span aria-hidden="true">→</span>
                    </a>
                </div>

                {{-- Arabic row --}}
                <div class="nb-recruit-row nb-recruit-row--ar" dir="rtl" lang="ar">
                    <span class="nb-recruit-dot" aria-hidden="true"></span>
                    <p class="nb-recruit-text">
                        <strong>التسجيلات مفتوحة</strong> — باب الترشح لموسم 2026/2027 مفتوح الآن.
                    </p>
                    <a href="{{ route('recrute') }}" class="nb-recruit-btn">
                        سجّل الآن <span aria-hidden="true">←</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
    {{-- ======================= HERO SECTION ======================= --}}
    <section class="nb-hero" id="top">
        <div class="nb-container">
            <div class="nb-hero-grid">
                {{-- Narrative Column --}}
                <div>
                    <div class="nb-hero-kicker">
                        <span class="nb-badge nb-badge--burgundy nb-badge--dot">2026/2027 Season</span>
                        <span class="nb-badge nb-badge--gold">FST Errachidia</span>
                    </div>

                    <h1 class="nb-hero-title">
                        THINK. SPEAK.<br>
                        <span class="line-burg">CHANGE<span class="dot">.</span></span>
                    </h1>

                    <p class="nb-hero-sub">
                        The premier student debate society at Faculté des Sciences et Techniques Errachidia. We train students to master public speaking, dismantle logical fallacies, and argue with conviction across English, French, and Arabic.
                    </p>

                    <div class="nb-hero-actions">
                        <a href="{{ route('recrute') }}" class="nb-btn nb-btn--primary">
                            Join the Club — Free →
                        </a>
                        <a href="#events" class="nb-btn nb-btn--outline">
                            Upcoming Events ↓
                        </a>
                    </div>

                    <div class="nb-hero-proof">
                        <div class="nb-proof-item">
                            <span class="nb-proof-num">150+</span>
                            <span class="nb-proof-label">Active Debaters</span>
                        </div>
                        <div class="nb-proof-item">
                            <span class="nb-proof-num">3</span>
                            <span class="nb-proof-label">Languages (AR · FR · EN)</span>
                        </div>
                        <div class="nb-proof-item">
                            <span class="nb-proof-num">Weekly</span>
                            <span class="nb-proof-label">Open Rounds</span>
                        </div>
                    </div>
                </div>

                {{-- Structured Visual Showcase Column --}}
                <div class="nb-hero-showcase">
                    <div class="nb-showcase-frame">
                        <img src="{{ asset('images/memories/debate-round-01.jpg') }}" alt="Student debating at FST podium">
                        <div class="nb-showcase-scrim"></div>
                        <div class="nb-showcase-bar">
                            <div class="nb-showcase-caption">
                                <span class="nb-showcase-title">Amphithéâtre A · FST Errachidia</span>
                                <span class="nb-showcase-sub">Weekly Parliamentary Sessions</span>
                            </div>
                            <span class="nb-badge nb-badge--gold" style="font-size:11px;">● Wednesdays 15:00</span>
                        </div>
                    </div>

                    {{-- Docked Motion Card --}}
                    <div class="nb-showcase-motion">
                        <div class="nb-motion-top">
                            <span class="nb-motion-tag">● Motion of the Week</span>
                            <span class="nb-badge nb-badge--burgundy" style="padding:2px 8px; font-size:10px;">BP Round</span>
                        </div>
                        <p class="nb-motion-quote">
                            "This House Would Mandate Ethics Training for AI Engineers in Moroccan Universities."
                        </p>
                        <div class="nb-motion-footer">
                            <span>Gov: Cycle Ingénieur</span>
                            <span>vs</span>
                            <span>Opp: DEUST Team</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= RUNNING MARQUEE ======================= --}}
    <div class="nb-marquee" aria-hidden="true">
        <div class="nb-marquee-track">
            <span class="nb-marquee-item">THINK CRITICALLY <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">SPEAK WITH CONVICTION <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">3 LANGUAGES: ARABIC · FRENCH · ENGLISH <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">FST ERRACHIDIA <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">BRITISH PARLIAMENTARY DEBATE <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">ZERO PRE-REQUISITES <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">YOUR VOICE MATTERS <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">WEEKLY ORATORY CLINICS <span class="nb-marquee-dot"></span></span>
            {{-- Duplication for continuous scroll --}}
            <span class="nb-marquee-item">THINK CRITICALLY <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">SPEAK WITH CONVICTION <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">3 LANGUAGES: ARABIC · FRENCH · ENGLISH <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">FST ERRACHIDIA <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">BRITISH PARLIAMENTARY DEBATE <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">ZERO PRE-REQUISITES <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">YOUR VOICE MATTERS <span class="nb-marquee-dot"></span></span>
            <span class="nb-marquee-item">WEEKLY ORATORY CLINICS <span class="nb-marquee-dot"></span></span>
        </div>
    </div>

    {{-- ======================= ABOUT SECTION ======================= --}}
    <section class="nb-about" id="about" aria-labelledby="about-title">
        <div class="nb-container">
            <div class="nb-about-grid">
                {{-- Narrative --}}
                <div class="nb-about-narrative">
                    <span class="nb-badge nb-badge--burgundy">Nº01 — Who We Are</span>
                    <h2 class="nb-h2" id="about-title" style="margin-top:16px;">
                        More than speeches.<br><em>A gymnasium for the mind.</em>
                    </h2>
                    <p>
                        TGD is not a passive lecture hall. It is a live arena. Every week, university students from all faculties step up to the podium, take an unexpected side — often the one they instinctively disagree with — and defend it under strict timing and rigorous counter-arguments.
                    </p>
                    <p>
                        We build unwavering confidence, dismantle rhetorical fallacies, debate as collaborative parliamentary teams, and trade ideas that stand the test of scrutiny.
                    </p>

                    <blockquote class="nb-charter-quote">
                        <p>"A debate club is where you learn to love truth more than winning, and respect your opponent as your greatest teacher."</p>
                        <footer>— The TGD Founding Charter · Est. 2023</footer>
                    </blockquote>
                </div>

                {{-- Values Stack --}}
                <div class="nb-about-stack">
                    <div class="nb-value-card">
                        <div class="nb-value-num">01</div>
                        <div>
                            <h3>Radical Inclusivity</h3>
                            <p>From first-year DEUST students terrified of the microphone to seasoned engineering debaters. You are welcomed, mentored, and given the floor on day one.</p>
                        </div>
                    </div>

                    <div class="nb-value-card">
                        <div class="nb-value-num">02</div>
                        <div>
                            <h3>Courage Under Fire</h3>
                            <p>Overcome stage-fright by confronting it in a supportive environment. Learn to breathe, smile, and dismantle interruptions with poise.</p>
                        </div>
                    </div>

                    <div class="nb-value-card">
                        <div class="nb-value-num">03</div>
                        <div>
                            <h3>The Debater’s Code</h3>
                            <p>Fierce in argumentation, fraternal in spirit. We challenge each other's assumptions on stage, then share mint tea together as teammates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= THREE PILLARS ======================= --}}
    <section class="nb-pillars" id="pillars" aria-labelledby="pillars-title">
        <div class="nb-container">
            <div class="nb-section-head">
                <div>
                    <span class="nb-badge nb-badge--gold">Nº02 — The Methodology</span>
                    <h2 class="nb-h2" id="pillars-title" style="margin-top:14px;">
                        Three <em>Pillars</em> of Mastery.
                    </h2>
                    <p class="nb-section-sub">
                        Our structured training curriculum turns raw enthusiasm into eloquent, articulate persuasion that benefits your academic, professional, and personal life.
                    </p>
                </div>
                <a href="{{ route('recrute') }}" class="nb-btn nb-btn--primary nb-btn--sm">Join This Season →</a>
            </div>

            <div class="nb-pillar-grid">
                {{-- Pillar 1 --}}
                <article class="nb-pillar-card">
                    <div>
                        <div class="nb-pillar-top">
                            <span class="nb-badge nb-badge--burgundy">01 / Voice</span>
                            <span class="nb-pillar-num">01</span>
                        </div>
                        <h3>Public Speaking & Presence</h3>
                        <p>Improvisation drills, vocal modulation, cadence, and non-verbal mastery that convert anxiety into authentic commanding presence.</p>
                    </div>
                    <div class="nb-pillar-tags">
                        <span class="nb-skill-chip">Vocal Projection</span>
                        <span class="nb-skill-chip">Body Language</span>
                        <span class="nb-skill-chip">Impromptu</span>
                    </div>
                </article>

                {{-- Pillar 2 (Featured Card) --}}
                <article class="nb-pillar-card nb-pillar-card--dark">
                    <div>
                        <div class="nb-pillar-top">
                            <span class="nb-badge nb-badge--gold">02 / Mind</span>
                            <span class="nb-pillar-num">02</span>
                        </div>
                        <h3>Critical Thinking & Logic</h3>
                        <p>Every claim must withstand scrutiny. Master structural syllogisms, identify cognitive biases, and deconstruct fallacies under tight pressure.</p>
                    </div>
                    <div class="nb-pillar-tags">
                        <span class="nb-skill-chip" style="background:#FFF9EF; color:#171411;">Fallacy Detection</span>
                        <span class="nb-skill-chip" style="background:#FFF9EF; color:#171411;">Rebuttals</span>
                        <span class="nb-skill-chip" style="background:#FFF9EF; color:#171411;">Synthesis</span>
                    </div>
                </article>

                {{-- Pillar 3 --}}
                <article class="nb-pillar-card">
                    <div>
                        <div class="nb-pillar-top">
                            <span class="nb-badge nb-badge--burgundy">03 / Culture</span>
                            <span class="nb-pillar-num">03</span>
                        </div>
                        <h3>Trilingual Fluency</h3>
                        <p>TGD is proudly trilingual. Hone your argumentation in Arabic (فصاحة وتعبير), French (rigueur et éloquence), and English (parliamentary debate).</p>
                    </div>
                    <div class="nb-pillar-tags">
                        <span class="nb-skill-chip">English BP</span>
                        <span class="nb-skill-chip">French Eloquence</span>
                        <span class="nb-skill-chip">Arabic Rhetoric</span>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ======================= ACTIVITIES SHOWCASE ======================= --}}
    <section class="nb-activities" id="activities" aria-labelledby="activities-title">
        <div class="nb-container">
            <div class="nb-section-head">
                <div>
                    <span class="nb-badge nb-badge--burgundy">Nº03 — Student Life</span>
                    <h2 class="nb-h2" id="activities-title" style="margin-top:14px;">
                        What We <em>Do</em> Each Week.
                    </h2>
                    <p class="nb-section-sub">
                        Debate is just the beginning. Our calendar is packed with hands-on formats, masterclasses, and campus showdowns that bring students together across all disciplines.
                    </p>
                </div>
                <span class="nb-badge nb-badge--gold">Open to All Faculties</span>
            </div>

            <div class="nb-activities-grid">
                {{-- Activity 1 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--burgundy">
                        <span class="nb-badge nb-badge--gold">Weekly Round</span>
                        <span style="font-family:'Roboto Condensed'; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Wednesdays · 15:00</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>British Parliamentary Rounds</h3>
                            <p>Four teams of two. 15 minutes of preparation after the motion is announced. 7 minutes per speaker. High adrenaline, strategic alliances, and intense points of information.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>📍 Amphithéâtre A</li>
                            <li>👥 8 Debaters + Jury</li>
                            <li>⏱ 15m Prep · 7m Speeches</li>
                            <li>🏆 Speaker Point Awards</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 2 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--gold">
                        <span class="nb-badge nb-badge--burgundy">Skill Workshop</span>
                        <span style="font-family:'Roboto Condensed'; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Bi-Weekly Sessions</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>Rhetoric & Speech Clinics</h3>
                            <p>Interactive coaching sessions led by professors, guest debaters, and alumni. We analyze historical speeches, train breath and body language, and run spontaneous table topics.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>📍 Library Conference Hall</li>
                            <li>🎙 Mic Techniques</li>
                            <li>🎭 Body Language</li>
                            <li>📝 Speech Structure</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 3 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--wine">
                        <span class="nb-badge nb-badge--gold">Multilingual</span>
                        <span style="font-family:'Roboto Condensed'; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">3 Active Tracks</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>Trilingual Division Cups</h3>
                            <p>Rotate between our English, French, and Arabic tracks. Gain conversational fluency, expand your technical vocabulary, and tailor arguments across distinct cultural perspectives.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>🌐 English · FR · العربية</li>
                            <li>🏅 Section Badges</li>
                            <li>🤝 Cultural Exchange</li>
                            <li>📈 Language Fluency</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 4 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--dark">
                        <span class="nb-badge nb-badge--gold">Flagship Event</span>
                        <span style="font-family:'Roboto Condensed'; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Spring Term</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>The Grand FST Debate Gala</h3>
                            <p>The crown jewel of our campus year. The finest debaters compete before an open auditorium of over 300 students, faculty deans, and external guests, culminating in an awards ceremony.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>📍 Grand Auditorium</li>
                            <li>👥 300+ Attendees</li>
                            <li>🥇 TGD Championship Cup</li>
                            <li>✨ Formal Ceremony</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= EVENTS CALENDAR ======================= --}}
    @php
    if (!isset($upcomingEvents)) {
    try {
    $upcomingEvents = \App\Models\Event::whereNotNull('published_at')
    ->where('published_at', '<=', now())
        ->where('starts_at', '>=', now())
        ->orderBy('starts_at')
        ->limit(3)
        ->get();
        } catch (\Throwable $e) {
        $upcomingEvents = collect();
        }
        }
        @endphp

        <section class="nb-events" id="events" aria-labelledby="events-title">
            <div class="nb-container">
                <div class="nb-section-head">
                    <div>
                        <span class="nb-badge nb-badge--gold">Nº04 — Live Calendar</span>
                        <h2 class="nb-h2" id="events-title" style="margin-top:14px;">
                            What's <em>Next</em> on the Floor?
                        </h2>
                        <p class="nb-section-sub">
                            Check upcoming rounds, clinics, and championship dates. Club members can RSVP, view attendance sheets, and download motion briefings directly in the app.
                        </p>
                    </div>

                    @auth
                    <a href="{{ route('events.index') }}" class="nb-btn nb-btn--gold nb-btn--sm">Open Full Calendar →</a>
                    @else
                    <a href="{{ route('login') }}" class="nb-btn nb-btn--gold nb-btn--sm">Log in to RSVP →</a>
                    @endauth
                </div>

                @if ($upcomingEvents->count())
                <div class="nb-events-list">
                    @foreach ($upcomingEvents as $event)
                    <article class="nb-event-card">
                        <div>
                            <div class="nb-event-date-block">
                                <div class="nb-event-calendar-tile">
                                    <span class="nb-event-tile-month">{{ $event->starts_at->format('M') }}</span>
                                    <span class="nb-event-tile-day">{{ $event->starts_at->format('j') }}</span>
                                </div>
                                <div>
                                    <span class="nb-event-time">{{ $event->starts_at->translatedFormat('l · H:i') }}</span>
                                    <div class="nb-event-loc">📍 {{ $event->location ?: 'Campus Hall TBA' }}</div>
                                </div>
                            </div>

                            <h3 class="nb-event-title">{{ $event->title }}</h3>
                        </div>

                        <div class="nb-event-footer">
                            <span class="nb-badge nb-badge--burgundy" style="padding:3px 8px; font-size:10px;">
                                {{ $event->team ? Str::upper($event->team) : 'Open Round' }}
                            </span>

                            @auth
                            <a href="{{ route('events.show', $event) }}" class="nb-btn nb-btn--primary nb-btn--sm">View Details →</a>
                            @else
                            <a href="{{ route('register') }}" class="nb-btn nb-btn--primary nb-btn--sm">Join to Attend →</a>
                            @endauth
                        </div>
                    </article>
                    @endforeach
                </div>
                @else
                <div class="nb-events-empty">
                    <h3>The floor is being prepared.</h3>
                    <p>
                        New debates and clinics are currently being scheduled by the Bureau. Join the club today to receive motion briefings and dates the moment they are announced.
                    </p>
                    <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
                        <a href="{{ route('recrute') }}" class="nb-btn nb-btn--gold nb-btn--sm">Apply for Recruitment</a>
                        @auth
                        <a href="{{ route('events.index') }}" class="nb-btn nb-btn--outline-light nb-btn--sm">Browse Past Rounds</a>
                        @endauth
                    </div>
                </div>
                @endif
            </div>
        </section>

        {{-- ======================= THE BUREAU (TEAM) ======================= --}}
        @php
        $teamMembers = [
        ['name' => 'Zoubine Soulaimane', 'role' => 'President', 'mark' => 'P', 'dept' => 'Executive Office'],
        ['name' => 'Khaoula Meggouri', 'role' => 'Vice President', 'mark' => 'VP', 'dept' => 'Coordination'],
        ['name' => 'Fanta Ba', 'role' => 'Secretary General', 'mark' => 'SG', 'dept' => 'Administration'],
        ['name' => 'Ghezlane Herraf', 'role' => 'Media Manager', 'mark' => 'MM', 'dept' => 'Public Relations'],
        ['name' => 'Hammali Imane', 'role' => 'Design Manager', 'mark' => 'DM', 'dept' => 'Brand & Creative'],
        ['name' => 'Aymane Fassali', 'role' => 'English Section Lead', 'mark' => 'EN', 'dept' => 'English BP Track'],
        ['name' => 'Wissal Jamal', 'role' => 'French Section Lead', 'mark' => 'FR', 'dept' => 'French Eloquence'],
        ['name' => 'Fatima Ezahrae Oubahou', 'role' => 'Arabic Section Lead', 'mark' => 'AR', 'dept' => 'Arabic Rhetoric'],
        ];

        $initials = fn (string $n) => collect(explode(' ', trim($n)))
        ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
        @endphp

        <section class="nb-team" id="team" aria-labelledby="team-title">
            <div class="nb-container">
                <div class="nb-section-head">
                    <div>
                        <span class="nb-badge nb-badge--burgundy">Nº05 — Leadership</span>
                        <h2 class="nb-h2" id="team-title" style="margin-top:14px;">
                            The Bureau Behind <em>the Mic.</em>
                        </h2>
                        <p class="nb-section-sub">
                            Eight desks, one shared mission. Our student-elected bureau coordinates rounds, manages language tracks, books campus venues, and ensures every voice gets heard.
                        </p>
                    </div>
                    <span class="nb-badge nb-badge--gold">Elected 2026/2027</span>
                </div>

                <div class="nb-team-grid">
                    @foreach ($teamMembers as $member)
                    @php
                    $slug = Str::slug($member['name']);
                    $hasPhoto = file_exists(public_path('images/team/' . $slug . '.jpg'));
                    $photoUrl = asset('images/team/' . $slug . '.jpg');
                    @endphp
                    <article class="nb-team-card">
                        <div class="nb-team-photo-wrapper">
                            @if ($hasPhoto)
                            <img src="{{ $photoUrl }}" alt="{{ $member['name'] }} — {{ $member['role'] }}" loading="lazy">
                            @else
                            <span class="nb-team-initials" aria-hidden="true">{{ $initials($member['name']) }}</span>
                            @endif
                            <span class="nb-team-mark" aria-hidden="true">{{ $member['mark'] }}</span>
                        </div>

                        <div class="nb-team-info">
                            <h3>{{ $member['name'] }}</h3>
                            <span class="nb-team-role">{{ $member['role'] }}</span>
                            <span style="font-family:'Roboto Condensed'; font-size:11px; color:var(--nb-ink-muted); letter-spacing:.04em; text-transform:uppercase; margin-top:3px; display:block;">
                                {{ $member['dept'] }}
                            </span>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================= PHOTO MEMORIES ======================= --}}
        @php
        $memoriesDir = public_path('images/memories');
        $memoryImages = [];
        if (is_dir($memoriesDir)) {
        foreach (scandir($memoriesDir) ?: [] as $file) {
        if (preg_match('/\.(jpe?g|png|webp|avif)$/i', $file)) {
        $memoryImages[] = $file;
        }
        }
        }
        sort($memoryImages);

        $captions = [
        'Annual Debate Showdown in Amphithéâtre A',
        'Intensive Strategy & Rebuttal Preparation Session',
        'Grand Tournament Finale — Mutual Respect & Handshake',
        ];
        @endphp

        <section class="nb-memories" id="memories" aria-labelledby="memories-title"
            x-data="{
            i: 0,
            slides: {{ count($memoryImages) }},
            perView() { return window.innerWidth <= 640 ? 1 : (window.innerWidth <= 1024 ? 2 : 3); },
            maxIndex() { return Math.max(0, this.slides - this.perView()); },
            next() { this.i = Math.min(this.maxIndex(), this.i + 1); },
            prev() { this.i = Math.max(0, this.i - 1); },
            trackX() { const w = this.$refs.track ? this.$refs.track.clientWidth / this.perView() : 0; return -(this.i * w); }
        }"
            @keydown.arrow.right.window="next()" @keydown.arrow.left.window="prev()">
            <div class="nb-container">
                <div class="nb-section-head">
                    <div>
                        <span class="nb-badge nb-badge--gold">Nº06 — Photo Archive</span>
                        <h2 class="nb-h2" id="memories-title" style="margin-top:14px;">
                            Snapshots from <em>the Floor.</em>
                        </h2>
                        <p class="nb-section-sub">
                            Authentic memories from our tournaments, late-night argument jams, and award celebrations at FST Errachidia.
                        </p>
                    </div>
                    <div class="nb-memories-controls">
                        <button type="button" class="nb-mem-btn" @click="prev()" :disabled="i === 0" aria-label="Previous photo">←</button>
                        <button type="button" class="nb-mem-btn" @click="next()" :disabled="i >= maxIndex()" aria-label="Next photo">→</button>
                    </div>
                </div>

                @if (count($memoryImages))
                <div class="nb-memories-viewport">
                    <div class="nb-memories-track" x-ref="track" :style="'transform: translateX(' + trackX() + 'px)'">
                        @foreach ($memoryImages as $index => $img)
                        <figure class="nb-memory-slide" role="group" aria-label="Memory {{ $index + 1 }} of {{ count($memoryImages) }}">
                            <img src="{{ asset('images/memories/' . $img) }}" alt="TGD Event Photo {{ $index + 1 }}" loading="lazy">
                            <figcaption class="nb-memory-caption">
                                <span>{{ $captions[$index % count($captions)] ?? 'TGD Live Session' }}</span>
                                <span class="nb-badge nb-badge--gold" style="padding:2px 6px; font-size:10px;">Nº{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </figcaption>
                        </figure>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </section>

        {{-- ======================= STUDENT VOICES ======================= --}}
        <section class="nb-testimonials" id="testimonials" aria-labelledby="testimonials-title">
            <div class="nb-container">
                <div class="nb-section-head">
                    <div>
                        <span class="nb-badge nb-badge--burgundy">Nº07 — Student Voices</span>
                        <h2 class="nb-h2" id="testimonials-title" style="margin-top:14px;">
                            From First-Year to <em>the Podium.</em>
                        </h2>
                        <p class="nb-section-sub">
                            Real stories from students across different faculties who unlocked their confidence and critical reasoning through TGD.
                        </p>
                    </div>
                    <span class="nb-badge nb-badge--gold">100% Student Testimonials</span>
                </div>

                <div class="nb-testimonials-grid">
                    <article class="nb-testimonial-card">
                        <p class="nb-testimonial-quote">
                            "I used to physically tremble whenever a professor asked me to present. After four months in the English division, I delivered a 7-minute opening speech in front of 120 students with zero notes."
                        </p>
                        <div class="nb-testimonial-author">
                            <div class="nb-testimonial-avatar">SK</div>
                            <div>
                                <h4 class="nb-author-name">Sami K.</h4>
                                <span class="nb-author-program">DEUST MIP · 2nd Year</span>
                            </div>
                        </div>
                    </article>

                    <article class="nb-testimonial-card">
                        <p class="nb-testimonial-quote">
                            "In engineering, technical skills only get you so far if you can't defend your design decisions. TGD taught me how to dismantle weak arguments and persuade stakeholders with evidence and clarity."
                        </p>
                        <div class="nb-testimonial-author">
                            <div class="nb-testimonial-avatar" style="background:var(--nb-wine); color:var(--nb-gold);">NE</div>
                            <div>
                                <h4 class="nb-author-name">Nour E.</h4>
                                <span class="nb-author-program">Cycle Ingénieur Génie Informatique</span>
                            </div>
                        </div>
                    </article>

                    <article class="nb-testimonial-card">
                        <p class="nb-testimonial-quote">
                            "The trilingual culture is unlike anything else on campus. Defending a motion in Arabic in the morning and debating in French in the afternoon makes you think in entirely new dimensions."
                        </p>
                        <div class="nb-testimonial-author">
                            <div class="nb-testimonial-avatar">YM</div>
                            <div>
                                <h4 class="nb-author-name">Youssef M.</h4>
                                <span class="nb-author-program">Master Sciences & Techniques</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- ======================= FINAL CALL-TO-ACTION ======================= --}}
        <section class="nb-cta" id="join" aria-labelledby="cta-title">
            <div class="nb-container">
                <div class="nb-cta-box">
                    <div class="nb-cta-content">
                        <span class="nb-badge nb-badge--gold">Nº08 — Take The Floor</span>
                        <h2 id="cta-title">
                            Your Voice<br><em>Belongs Here.</em>
                        </h2>
                        <p>
                            One membership. Three languages. Infinite growth. Pull up a chair, take the opposite side, and argue like it matters — because in university and beyond, your voice shapes the future.
                        </p>

                        <ul class="nb-cta-checklist">
                            <li>
                                <span class="nb-checklist-icon">✓</span>
                                <span>Open to all FST Errachidia students (all years & filières)</span>
                            </li>
                            <li>
                                <span class="nb-checklist-icon">✓</span>
                                <span>Free club membership — no prior debating experience necessary</span>
                            </li>
                            <li>
                                <span class="nb-checklist-icon">✓</span>
                                <span>Choose your tracks: English, French, or Arabic debates</span>
                            </li>
                            <li>
                                <span class="nb-checklist-icon">✓</span>
                                <span>Weekly practice rounds, guest clinics & annual tournaments</span>
                            </li>
                        </ul>
                    </div>

                    <div class="nb-cta-actions-card">
                        <h3 class="nb-cta-actions-title">Step up to the mic</h3>
                        <p class="nb-cta-actions-sub">Submit your candidate recruitment application in under two minutes.</p>

                        <a href="{{ route('recrute') }}" class="nb-btn nb-btn--gold" style="width:100%;">
                            Join The Club — Free →
                        </a>

                        <a href="{{ route('login') }}" class="nb-btn nb-btn--outline-light" style="width:100%;">
                            Already a member? Log in
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================= FOOTER ======================= --}}
        <footer class="nb-footer">
            <div class="nb-container">
                <div class="nb-footer-grid">
                    <div>
                        <span class="nb-footer-brand-title">
                            TGD<em>.</em> The Great Debaters
                        </span>
                        <p>
                            The official student debate society at Faculté des Sciences et Techniques Errachidia, dedicated to public speaking, critical reasoning, and trilingual debate.
                        </p>
                        <div class="nb-footer-uni-badge">
                            <span>🏛️ Faculté des Sciences et Techniques · Errachidia</span>
                        </div>
                    </div>

                    <div class="nb-footer-col">
                        <h4>Navigation</h4>
                        <ul>
                            <li><a href="#about">About the Club</a></li>
                            <li><a href="#pillars">Three Pillars</a></li>
                            <li><a href="#activities">Weekly Activities</a></li>
                            <li><a href="#events">Events Calendar</a></li>
                            <li><a href="#team">The Bureau</a></li>
                            <li><a href="#memories">Photo Memories</a></li>
                        </ul>
                    </div>

                    <div class="nb-footer-col">
                        <h4>Language Tracks</h4>
                        <ul>
                            <li><a href="#activities">🇬🇧 English BP Track</a></li>
                            <li><a href="#activities">🇫🇷 Section Française</a></li>
                            <li><a href="#activities">🇲🇦 فرع المناظرة بالعربية</a></li>
                            <li><a href="#activities">Bi-Weekly Masterclasses</a></li>
                            <li><a href="#activities">Annual Championship Gala</a></li>
                        </ul>
                    </div>

                    <div class="nb-footer-col">
                        <h4>Members Portal</h4>
                        <ul>
                            <li><a href="{{ route('recrute') }}">Recruitment Application</a></li>
                            <li><a href="{{ route('login') }}">Member Log in</a></li>
                            <li><a href="{{ route('register') }}">Create Account</a></li>
                            <li><a href="{{ route('password.recover') }}">Recover Password</a></li>
                            @auth
                            <li><a href="{{ route('dashboard') }}">My Dashboard</a></li>
                            <li><a href="{{ route('events.index') }}">RSVP to Events</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>

                <div class="nb-footer-bottom">
                    <span>© {{ date('Y') }} The Great Debaters Club · FST Errachidia (UMI)</span>
                    <span>Think. Speak. Change. · All Rights Reserved</span>
                </div>
            </div>
        </footer>

</body>

</html>