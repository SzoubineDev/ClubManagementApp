<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Uni Club') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="landing">

    {{-- Top nav --}}
    <nav class="landing-nav">
        <div class="landing-nav-inner">
            <a href="/" class="landing-nav-brand toolbar-brand">
                <span class="toolbar-logo-badge">UC</span>
                {{ config('app.name', 'Uni Club') }}
            </a>

            <div class="flex items-center gap-3">
                @auth
                <a href="{{ route('events.index') }}" class="btn btn-glass btn-sm">Go to app</a>
                @else
                <a href="{{ route('login') }}" class="link-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-invert btn-sm">Join us</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <header class="landing-hero">
        <div class="photo"
            style="background-image:url('https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1600&q=80')"></div>
        <div class="fade"></div>

        <div class="landing-hero-inner">
            <span class="landing-eyebrow animate-fade-in-down">UNIVERSITY CLUB · SINCE 2024</span>

            <h1 class="landing-title animate-fade-in-up">
                Where students<br>
                <strong>become community.</strong>
            </h1>

            <p class="landing-lead animate-fade-in-up delay-100">
                Join a vibrant community of students across Arabic, English, and French teams.
                Attend events, build skills, and make friends that last beyond graduation.
            </p>

            <div class="landing-actions animate-fade-in-up delay-200">
                <a href="{{ route('register') }}" class="btn btn-invert btn-lg">
                    Join us
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="btn btn-stroked btn-invert-text btn-lg">I'm already a member</a>
            </div>
        </div>
    </header>

    {{-- Stats strip --}}
    <section class="section section--white section--tight">
        <div class="content-wrap">
            <div class="landing-stats stagger">
                <div class="landing-stat"><b>250+</b><span>Active members</span></div>
                <div class="landing-stat"><b>3</b><span>Language teams</span></div>
                <div class="landing-stat"><b>40+</b><span>Events per year</span></div>
                <div class="landing-stat"><b>5</b><span>Years running</span></div>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section class="section">
        <div class="content-wrap">
            <div class="split-grid">
                <div>
                    <div class="section-head section-head--start">
                        <span class="section-kicker">About the club</span>
                        <h2 class="section-title">A community built by students, for students.</h2>
                        <p class="section-desc">
                            We bring together students from across the university to share ideas, learn new skills,
                            and create lasting friendships. Whether you're into debates, arts, tech, or community
                            service — there's a place for you here.
                        </p>
                    </div>

                    <ul class="check-list">
                        @foreach ([
                        'Weekly events and workshops',
                        'Three language teams to grow in',
                        'Mentorship from senior members',
                        'Leadership opportunities',
                        ] as $item)
                        <li>
                            <span class="check-bullet">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="photo-grid">
                    <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=600&q=80"
                        alt="Students collaborating" class="span-2">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=400&q=80"
                        alt="Team meeting">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=400&q=80"
                        alt="Event">
                </div>
            </div>
        </div>
    </section>

    {{-- Teams --}}
    <section class="section section--blush">
        <div class="content-wrap">
            <div class="section-head">
                <span class="section-kicker">Our teams</span>
                <h2 class="section-title">Three languages. One community.</h2>
                <p class="section-desc">
                    Every member belongs to one of our language teams. You'll find your people here.
                </p>
            </div>

            <div class="team-grid stagger">
                @foreach ([
                ['name' => 'Arabic Team', 'icon' => '🌙', 'desc' => 'Rich discussions, poetry nights, and cultural events in Arabic.'],
                ['name' => 'English Team', 'icon' => '🌍', 'desc' => 'Debates, workshops, and social events held in English.'],
                ['name' => 'French Team', 'icon' => '✨', 'desc' => 'Francophone meetups, film screenings, and creative sessions.'],
                ] as $team)
                <article class="team-card">
                    <div class="team-card-bar"></div>
                    <div class="team-card-body">
                        <div class="team-card-icon">{{ $team['icon'] }}</div>
                        <h3>{{ $team['name'] }}</h3>
                        <p>{{ $team['desc'] }}</p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section">
        <div class="content-wrap">
            <div class="cta-panel">
                <div class="photo"
                    style="background-image:url('https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1200&q=80')"></div>
                <div class="cta-panel-inner">
                    <h2>Ready to join the club?</h2>
                    <p>Sign up in less than a minute. No fees, no commitments — just show up and be part of something.</p>

                    <div class="cta-actions">
                        <a href="{{ route('register') }}" class="btn btn-invert btn-lg">Join us — it's free</a>
                        <a href="{{ route('login') }}" class="btn btn-glass btn-lg">I'm a member</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer">
        <div class="footer-inner">
            <small>© {{ date('Y') }} {{ config('app.name', 'Uni Club') }}. All rights reserved.</small>
            <div class="footer-links">
                <a href="#">Contact</a>
                <a href="#">Instagram</a>
                <a href="#">Facebook</a>
            </div>
        </div>
    </footer>

</body>

</html>
