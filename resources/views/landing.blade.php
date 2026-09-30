<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Uni Club') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-[#F8F9F9] text-[#650E19]">

    {{-- Top nav --}}
    <nav class="absolute top-0 left-0 right-0 z-20">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <a href="/" class="text-white font-bold text-xl tracking-tight">
                {{ config('app.name', 'Uni Club') }}
            </a>

            <div class="flex items-center gap-3">
                @auth
                <a href="{{ route('events.index') }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-white/10 backdrop-blur rounded-lg hover:bg-white/20">
                    Go to app
                </a>
                @else
                <a href="{{ route('login') }}"
                    class="px-4 py-2 text-sm font-medium text-white/90 hover:text-white">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                    class="px-4 py-2 text-sm font-medium text-[#650E19] bg-white rounded-lg hover:bg-[#EEE5E7]">
                    Join us
                </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative min-h-[600px] flex items-center overflow-hidden">
        {{-- Background gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#650E19] via-[#7a1420] to-[#3f0810]"></div>
        <div class="absolute inset-0 opacity-25"
            style="background-image: url('https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#3f0810]/70 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-6 py-32 w-full">
            <div class="max-w-3xl">
                <span class="inline-block px-3 py-1 text-xs font-medium text-[#EEE5E7] bg-white/10 backdrop-blur rounded-full mb-6">
                    University Club · Since 2024
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight">
                    Where students<br>
                    <span class="text-[#EEE5E7]">become community.</span>
                </h1>

                <p class="mt-6 text-lg text-[#EEE5E7]/90 leading-relaxed max-w-2xl">
                    Join a vibrant community of students across Arabic, English, and French teams.
                    Attend events, build skills, and make friends that last beyond graduation.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#650E19] font-semibold rounded-xl hover:bg-[#EEE5E7] shadow-lg shadow-black/20">
                        Join us
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>

                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-6 py-3 text-white font-semibold border border-white/40 rounded-xl hover:bg-white/10">
                        I'm already a member
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats strip --}}
    <section class="bg-white border-b border-[#EEE5E7]">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-3xl font-bold text-[#650E19]">250+</p>
                <p class="text-sm text-[#A48395] mt-1">Active members</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-[#650E19]">3</p>
                <p class="text-sm text-[#A48395] mt-1">Language teams</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-[#650E19]">40+</p>
                <p class="text-sm text-[#A48395] mt-1">Events per year</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-[#650E19]">5</p>
                <p class="text-sm text-[#A48395] mt-1">Years running</p>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section class="py-20 bg-[#F8F9F9]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-sm font-semibold text-[#A48395] uppercase tracking-wider">
                        About the club
                    </span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-[#650E19] leading-tight">
                        A community built by students, for students.
                    </h2>
                    <p class="mt-6 text-[#650E19]/70 leading-relaxed">
                        We bring together students from across the university to share ideas, learn new skills,
                        and create lasting friendships. Whether you're into debates, arts, tech, or community
                        service — there's a place for you here.
                    </p>

                    <ul class="mt-8 space-y-3">
                        @foreach ([
                        'Weekly events and workshops',
                        'Three language teams to grow in',
                        'Mentorship from senior members',
                        'Leadership opportunities',
                        ] as $item)
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full bg-[#EEE5E7] flex items-center justify-center text-[#650E19]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span class="text-[#650E19]/80">{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=600&q=80"
                        alt="Students collaborating"
                        class="rounded-2xl w-full h-64 object-cover col-span-2">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=400&q=80"
                        alt="Team meeting"
                        class="rounded-2xl w-full h-48 object-cover">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=400&q=80"
                        alt="Event"
                        class="rounded-2xl w-full h-48 object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- Teams --}}
    <section class="py-20 bg-[#EEE5E7]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto">
                <span class="text-sm font-semibold text-[#650E19] uppercase tracking-wider">
                    Our teams
                </span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-[#650E19]">
                    Three languages. One community.
                </h2>
                <p class="mt-4 text-[#650E19]/70">
                    Every member belongs to one of our language teams. You'll find your people here.
                </p>
            </div>

            <div class="mt-14 grid md:grid-cols-3 gap-6">
                @foreach ([
                ['name' => 'Arabic Team', 'icon' => '🌙', 'desc' => 'Rich discussions, poetry nights, and cultural events in Arabic.'],
                ['name' => 'English Team', 'icon' => '🌍', 'desc' => 'Debates, workshops, and social events held in English.'],
                ['name' => 'French Team', 'icon' => '✨', 'desc' => 'Francophone meetups, film screenings, and creative sessions.'],
                ] as $team)
                <div class="bg-white rounded-2xl border border-[#EEE5E7] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-2 bg-[#650E19]"></div>
                    <div class="p-6">
                        <div class="text-4xl mb-3">{{ $team['icon'] }}</div>
                        <h3 class="text-xl font-bold text-[#650E19]">{{ $team['name'] }}</h3>
                        <p class="mt-2 text-[#650E19]/70 text-sm leading-relaxed">{{ $team['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-[#F8F9F9]">
        <div class="max-w-5xl mx-auto px-6">
            <div class="relative bg-gradient-to-br from-[#650E19] via-[#7a1420] to-[#3f0810] rounded-3xl px-8 py-16 text-center overflow-hidden">
                <div class="absolute inset-0 opacity-15"
                    style="background-image: url('https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1200&q=80'); background-size: cover; background-position: center;"></div>

                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">
                        Ready to join the club?
                    </h2>
                    <p class="mt-4 text-[#EEE5E7]/90 max-w-xl mx-auto">
                        Sign up in less than a minute. No fees, no commitments — just show up and be part of something.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-white text-[#650E19] font-semibold rounded-xl hover:bg-[#EEE5E7]">
                            Join us — it's free
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-6 py-3 text-white font-semibold border border-white/40 rounded-xl hover:bg-white/10">
                            I'm a member
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-[#EEE5E7] bg-white py-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-[#A48395]">
                © {{ date('Y') }} {{ config('app.name', 'Uni Club') }}. All rights reserved.
            </p>

            <div class="flex gap-6 text-sm text-[#A48395]">
                <a href="#" class="hover:text-[#650E19]">Contact</a>
                <a href="#" class="hover:text-[#650E19]">Instagram</a>
                <a href="#" class="hover:text-[#650E19]">Facebook</a>
            </div>
        </div>
    </footer>

</body>

</html>