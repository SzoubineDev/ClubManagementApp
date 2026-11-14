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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/landing.css','resources/css/recrute.css', 'resources/js/app.js'])
</head>

<body class="nb" x-data="{ navOpen: {{ request('nav') ? 'true' : 'false' }} }">

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
                        Register Now →
                    </a>
                </div>

                {{-- Arabic row --}}
                <div class="nb-recruit-row nb-recruit-row--ar" dir="rtl" lang="ar">
                    <span class="nb-recruit-dot" aria-hidden="true"></span>
                    <p class="nb-recruit-text">
                        <strong>التسجيلات مفتوحة</strong> — باب الترشح لموسم 2026/2027 مفتوح الآن.
                    </p>
                    <a href="{{ route('recrute') }}" class="nb-recruit-btn">
                        سجّل الآن ←
                    </a>
                </div>

            </div>
        </div>
    </div>
    {{-- ======================= HERO SECTION ======================= --}}
    <section class="nb-hero" id="top">
        <div class="nb-container">
            <div class="nb-hero-content">
                <div class="nb-hero-kicker">
                    <span class="nb-badge">2026/2027 Season</span>
                    <span class="nb-badge">FST Errachidia</span>
                </div>

                <h1 class="nb-hero-title">
                    Think. Speak.<br>
                    <span class="line-burg">Change<span class="dot">.</span></span>
                </h1>

                <p class="nb-hero-sub">
                    A student-run debate club at FST Errachidia. We meet every Wednesday at 15:00 in Amphithéâtre A to debate in English, French and Arabic — no experience needed.
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
                    <span class="nb-proof-item">
                        <span class="nb-proof-num">150+</span>
                        <span class="nb-proof-label">Active Debaters</span>
                    </span>
                    <span class="nb-proof-item">
                        <span class="nb-proof-num">3</span>
                        <span class="nb-proof-label">Languages</span>
                    </span>
                    <span class="nb-proof-item">
                        <span class="nb-proof-num">Weekly</span>
                        <span class="nb-proof-label">Open Rounds</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= ABOUT SECTION ======================= --}}
    <section class="nb-about" id="about" aria-labelledby="about-title">
        <div class="nb-container">
            <div class="nb-about-grid">
                {{-- Narrative --}}
                <div class="nb-about-narrative">
                    <span class="nb-badge nb-badge--burgundy">Nº01 — Who We Are</span>
                    <h2 class="nb-h2" id="about-title">
                        We meet to argue.<br><em>And to get better at it.</em>
                    </h2>
                    <p>
                        Every week we put a motion on the table. Two teams take a side, fifteen minutes to prepare, seven minutes each to speak. You learn to make a case, take a point of information, and change your mind when the argument demands it.
                    </p>
                    <p>
                        Members come from every department and every year. Some arrive terrified of the microphone; most leave able to hold a room. The rest is practice, and there is plenty of it.
                    </p>

                    <blockquote class="nb-charter-quote">
                        <p>"A debate club is where you learn to love truth more than winning, and respect your opponent as your greatest teacher."</p>
                        <footer>— The TGD Founding Charter · Est. 2023</footer>
                    </blockquote>
                </div>

                {{-- Portrait & Values Stack --}}
                <div class="nb-about-stack">
                    <figure class="nb-about-photo">
                        <img src="{{ asset('images/memories/tournament-finale-03.jpg') }}" alt="Two TGD debaters shaking hands after the grand finale" loading="lazy">
                        <figcaption>The Grand FST Debate Gala · Tournament Finale</figcaption>
                    </figure>

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
                    <h2 class="nb-h2" id="pillars-title">
                        Voice, logic<br>and <em>language.</em>
                    </h2>
                    <p class="nb-section-sub">
                        Three things we practise every week: how you sound, how you think, and how you argue in more than one language.
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
                        <h3>Public Speaking and Presence</h3>
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
                        <h3>Critical Thinking and Logic</h3>
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
                    <h2 class="nb-h2" id="activities-title">
                        What We <em>Do</em> Each Week.
                    </h2>
                    <p class="nb-section-sub">
                        Beyond the weekly round, we run workshops, language-track sessions and the annual gala. Everything is open to every faculty.
                    </p>
                </div>
                <span class="nb-badge nb-badge--gold">Open to All Faculties</span>
            </div>

            <div class="nb-activities-grid">
                {{-- Activity 1 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--burgundy">
                        <span class="nb-badge nb-badge--gold">Weekly Round</span>
                        <span>Wednesdays · 15:00</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>British Parliamentary Rounds</h3>
                            <p>Four teams of two. 15 minutes of preparation after the motion is announced. 7 minutes per speaker. High adrenaline, strategic alliances, and intense points of information.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>Amphithéâtre A</li>
                            <li>8 Debaters + Jury</li>
                            <li>15m Prep · 7m Speeches</li>
                            <li>Speaker Point Awards</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 2 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--gold">
                        <span class="nb-badge nb-badge--burgundy">Skill Workshop</span>
                        <span>Bi-Weekly Sessions</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>Rhetoric and Speech Clinics</h3>
                            <p>Interactive coaching sessions led by professors, guest debaters, and alumni. We analyze historical speeches, train breath and body language, and run spontaneous table topics.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>Library Conference Hall</li>
                            <li>Mic Techniques</li>
                            <li>Body Language</li>
                            <li>Speech Structure</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 3 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--wine">
                        <span class="nb-badge nb-badge--gold">Multilingual</span>
                        <span>3 Active Tracks</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>Trilingual Division Cups</h3>
                            <p>Rotate between our English, French, and Arabic tracks. Gain conversational fluency, expand your technical vocabulary, and tailor arguments across distinct cultural perspectives.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>English · French · Arabic</li>
                            <li>Section Badges</li>
                            <li>Cultural Exchange</li>
                            <li>Language Fluency</li>
                        </ul>
                    </div>
                </div>

                {{-- Activity 4 --}}
                <div class="nb-activity-card">
                    <div class="nb-activity-banner nb-activity-banner--dark">
                        <span class="nb-badge nb-badge--gold">Flagship Event</span>
                        <span>Spring Term</span>
                    </div>
                    <div class="nb-activity-body">
                        <div>
                            <h3>The Grand FST Debate Gala</h3>
                            <p>The crown jewel of our campus year. The finest debaters compete before an open auditorium of over 300 students, faculty deans, and external guests, culminating in an awards ceremony.</p>
                        </div>
                        <ul class="nb-activity-specs">
                            <li>Grand Auditorium</li>
                            <li>300+ Attendees</li>
                            <li>TGD Championship Cup</li>
                            <li>Formal Ceremony</li>
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
                        <h2 class="nb-h2" id="events-title">
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
                                    <div class="nb-event-loc">{{ $event->location ?: 'Campus Hall TBA' }}</div>
                                </div>
                            </div>

                            <h3 class="nb-event-title">{{ $event->title }}</h3>
                        </div>

                        <div class="nb-event-footer">
                            <span class="nb-badge nb-badge--burgundy">
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
        ['name' => 'Wissal Jamal', 'role' => 'Vice President', 'mark' => 'VP', 'dept' => 'Coordination'],
        ['name' => 'Fanta Ba', 'role' => 'General assistant', 'mark' => 'SG', 'dept' => 'Administration'],
        ['name' => 'Baidi oussama', 'role' => 'RH Manager', 'mark' => 'MM', 'dept' => 'Public Relations'],
        ['name' => 'Hammali Imane', 'role' => 'Media Manager', 'mark' => 'DM', 'dept' => 'Brand & Creative'],
        ['name' => 'Ghezlane Herraf', 'role' => 'Design Manager', 'mark' => 'MM', 'dept' => 'Public Relations'],
        ['name' => 'Abdousse NourEddine', 'role' => 'Event Manager ', 'mark' => 'FR', 'dept' => 'French Eloquence'],
        ['name' => 'Khaoula Meggouri', 'role' => 'Event Manager Assistant ', 'mark' => 'FR', 'dept' => 'French Eloquence'],
        ['name' => 'Aymane Fassali', 'role' => 'English Section Lead', 'mark' => 'EN', 'dept' => 'English BP Track'],
        ['name' => 'Oubahou Fatima Ezahrae', 'role' => 'Arabic Section Lead', 'mark' => 'AR', 'dept' => 'Arabic Rhetoric'],
        ];

        $initials = fn (string $n) => collect(explode(' ', trim($n)))
        ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
        @endphp

        <section class="nb-team" id="team" aria-labelledby="team-title">
            <div class="nb-container">
                <div class="nb-section-head">
                    <div>
                        <span class="nb-badge nb-badge--burgundy">Nº05 — Leadership</span>
                        <h2 class="nb-h2" id="team-title">
                            The students behind <em>the club.</em>
                        </h2>
                        <p class="nb-section-sub">
                            The people who schedule rounds, keep the three language tracks going, and make sure newcomers get a seat.
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
                            <span class="nb-team-dept">{{ $member['dept'] }}</span>
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
                        <h2 class="nb-h2" id="memories-title">
                            Snapshots from <em>the Floor.</em>
                        </h2>
                        <p class="nb-section-sub">
                            Photos from our rounds, workshops and the annual gala at FST Errachidia.
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
                                <span class="nb-badge nb-badge--gold">Nº{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
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
                        <h2 class="nb-h2" id="testimonials-title">
                            From First-Year to <em>the Podium.</em>
                        </h2>
                        <p class="nb-section-sub">
                            Members on what changed after a few months at the podium.
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
                            Membership is free and open to every FST student. Come to one session or come to all of them — the floor is yours.
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
                            <span>Faculté des Sciences et Techniques · Errachidia</span>
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
                            <li><a href="#activities">English BP Track</a></li>
                            <li><a href="#activities">Section Française</a></li>
                            <li><a href="#activities">فرع المناظرة بالعربية</a></li>
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
