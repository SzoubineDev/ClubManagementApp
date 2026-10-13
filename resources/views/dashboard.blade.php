<x-app-layout>
    <x-slot name="header">
        {{-- Intentionally empty — the hero below owns the top of the page --}}
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ============================================
                 HERO PANEL — the signature element
                 ============================================ --}}
            {{-- ============================================
     HERO PANEL
     ============================================ --}}
            <div class="relative overflow-hidden rounded-2xl bg-bordeaux text-white px-8 py-10 sm:px-12 sm:py-14 shadow-pop animate-fade-in">

                {{-- Subtle dot texture --}}
                <div class="absolute inset-0 opacity-[0.08] pointer-events-none"
                    style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 22px 22px;"></div>

                {{-- Warm glow --}}
                <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-saffron/20 blur-3xl pointer-events-none"></div>

                <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-8">
                    <div class="flex items-center gap-6 max-w-xl">

                        {{-- ── Profile image placeholder ── --}}
                        <div class="shrink-0">
                            @if (!empty($user->avatar))
                            <img src="{{ $user->avatar }}"
                                alt="{{ $user->name }}"
                                class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover
                                ring-2 ring-white/20 shadow-pop">
                            @else
                            {{-- Placeholder: initials on a soft tint --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl
                                bg-saffron/20 ring-2 ring-white/20 shadow-pop
                                flex items-center justify-center
                                text-saffron text-3xl sm:text-4xl font-bold tracking-tight select-none">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/50">
                                {{ now()->format('l, F j') }}
                            </p>
                            <h1 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight leading-[1.1]">
                                Welcome back,<br>
                                <span class="text-saffron">{{ $user->name }}</span>
                            </h1>
                            <p class="mt-4 text-sm sm:text-base text-white/75 leading-relaxed">
                                @if ($stats['rsvps'] > 0)
                                You have <strong class="text-white font-semibold">{{ $stats['rsvps'] }} upcoming event{{ $stats['rsvps'] === 1 ? '' : 's' }}</strong> on your calendar.
                                @else
                                Your calendar is clear. Time to find your next one.
                                @endif
                            </p>
                        </div>
                    </div>

                    @can('create', \App\Models\Event::class)
                    <a href="{{ route('events.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl bg-white text-bordeaux font-bold text-sm
                  hover:bg-saffron hover:text-bordeaux-900 transition-colors shrink-0 shadow-pop self-start sm:self-end">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create event
                    </a>
                    @endcan
                </div>
            </div>
            {{-- ============================================
                 STATS ROW — one card, three sections
                 ============================================ --}}
            <div class="grid grid-cols-3 bg-white rounded-2xl border border-blush shadow-soft overflow-hidden divide-x divide-blush animate-fade-in-up delay-100">
                <div class="px-6 py-6 sm:px-8 sm:py-7">
                    <p class="text-4xl sm:text-5xl font-bold text-ink tabular-nums tracking-tight leading-none">
                        {{ $stats['attended'] }}
                    </p>
                    <p class="mt-3 text-[11px] font-bold text-mauve uppercase tracking-widest">
                        Events attended
                    </p>
                </div>
                <div class="px-6 py-6 sm:px-8 sm:py-7">
                    <p class="text-4xl sm:text-5xl font-bold text-ink tabular-nums tracking-tight leading-none">
                        {{ $stats['rsvps'] }}
                    </p>
                    <p class="mt-3 text-[11px] font-bold text-mauve uppercase tracking-widest">
                        Upcoming RSVPs
                    </p>
                </div>
                <div class="px-6 py-6 sm:px-8 sm:py-7">
                    <p class="text-4xl sm:text-5xl font-bold text-ink tracking-tight leading-none">
                        {{ $stats['member_since']->format('M Y') }}
                    </p>
                    <p class="mt-3 text-[11px] font-bold text-mauve uppercase tracking-widest">
                        Member since
                    </p>
                </div>
            </div>

            {{-- ============================================
                 MAIN GRID
                 ============================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ---------- LEFT column ---------- --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Calendar --}}
                    <section class="bg-white rounded-2xl border border-blush shadow-soft overflow-hidden animate-fade-in-up delay-200">
                        <header class="px-6 py-5 flex items-baseline justify-between border-b border-blush">
                            <div>
                                <h2 class="text-base font-bold text-ink">Your calendar</h2>
                                <p class="text-xs text-mauve mt-0.5">Events you've committed to</p>
                            </div>
                            <a href="{{ route('events.index') }}"
                                class="text-xs font-bold text-bordeaux hover:text-bordeaux-dark link-underline">
                                All events
                            </a>
                        </header>

                        @if ($upcomingRsvps->isEmpty())
                        <div class="px-6 py-20 text-center">
                            <p class="text-base font-bold text-ink">Nothing scheduled</p>
                            <p class="text-sm text-mauve mt-2">When you RSVP, it shows up here.</p>
                            <a href="{{ route('events.index') }}"
                                class="mt-5 inline-block text-sm font-bold text-bordeaux hover:text-bordeaux-dark link-underline">
                                Browse events →
                            </a>
                        </div>
                        @else
                        <ul class="divide-y divide-blush">
                            @foreach ($upcomingRsvps as $event)
                            <li>
                                <a href="{{ route('events.show', $event) }}"
                                    class="group flex items-center gap-6 px-6 py-5 hover:bg-bordeaux-50/40 transition-colors">

                                    {{-- Date block — replaces the useless calendar icon --}}
                                    <div class="w-14 shrink-0 text-center">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-bordeaux">
                                            {{ $event->starts_at->format('M') }}
                                        </p>
                                        <p class="mt-1 text-3xl font-bold text-ink tabular-nums leading-none">
                                            {{ $event->starts_at->format('j') }}
                                        </p>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[15px] font-semibold text-ink group-hover:text-bordeaux transition-colors truncate">
                                            {{ $event->title }}
                                        </p>
                                        <p class="text-xs text-mauve mt-1 truncate tabular-nums">
                                            {{ $event->starts_at->format('H:i') }}
                                            @if ($event->location)
                                            <span class="mx-1.5">·</span>{{ $event->location }}
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Status --}}
                                    <span class="shrink-0 text-[10px] font-bold uppercase tracking-[0.15em]
                                                 text-teal-700 bg-teal-50 px-2.5 py-1 rounded-full
                                                 ring-1 ring-teal-200/70">
                                        Going
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </section>

                    {{-- Discover --}}
                    @if ($visibleEvents->isNotEmpty())
                    <section class="bg-white rounded-2xl border border-blush shadow-soft overflow-hidden animate-fade-in-up delay-300">
                        <header class="px-6 py-5 border-b border-blush">
                            <h2 class="text-base font-bold text-ink">Discover</h2>
                            <p class="text-xs text-mauve mt-0.5">Published events you haven't joined</p>
                        </header>

                        <ul class="divide-y divide-blush">
                            @foreach ($visibleEvents as $event)
                            <li>
                                <a href="{{ route('events.show', $event) }}"
                                    class="group flex items-center gap-6 px-6 py-5 hover:bg-bordeaux-50/40 transition-colors">

                                    <div class="w-14 shrink-0 text-center">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-mauve">
                                            {{ $event->starts_at->format('M') }}
                                        </p>
                                        <p class="mt-1 text-3xl font-bold text-ink tabular-nums leading-none">
                                            {{ $event->starts_at->format('j') }}
                                        </p>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-[15px] font-semibold text-ink group-hover:text-bordeaux transition-colors truncate">
                                            {{ $event->title }}
                                        </p>
                                        <p class="text-xs text-mauve mt-1 truncate">
                                            by {{ $event->creator->name }}
                                            @if ($event->team)
                                            <span class="mx-1.5">·</span>{{ ucfirst($event->team) }}
                                            @endif
                                        </p>
                                    </div>

                                    <span class="shrink-0 text-sm font-bold text-bordeaux
                                                 group-hover:translate-x-0.5 transition-transform">
                                        →
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </section>
                    @endif
                </div>

                {{-- ---------- RIGHT sidebar ---------- --}}
                <aside class="space-y-6">

                    {{-- Account --}}
                    <section class="bg-white rounded-2xl border border-blush shadow-soft overflow-hidden animate-fade-in-up delay-200">
                        <header class="px-6 py-5 border-b border-blush">
                            <h2 class="text-base font-bold text-ink">Your account</h2>
                        </header>

                        <dl class="px-6 py-6 space-y-5">
                            <div>
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-mauve">Name</dt>
                                <dd class="mt-1.5 text-sm font-semibold text-ink">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-mauve">Email</dt>
                                <dd class="mt-1.5 text-sm text-ink truncate">{{ $user->email }}</dd>
                            </div>
                            <div class="grid grid-cols-2 gap-5 pt-1">
                                <div>
                                    <dt class="text-[10px] font-bold uppercase tracking-widest text-mauve">CNE</dt>
                                    <dd class="mt-1.5 text-sm font-semibold text-ink tabular-nums">{{ $user->cne ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] font-bold uppercase tracking-widest text-mauve">Team</dt>
                                    <dd class="mt-1.5 text-sm font-semibold text-ink capitalize">{{ $user->team ?: '—' }}</dd>
                                </div>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-mauve">Filière</dt>
                                <dd class="mt-1.5 text-sm font-semibold text-ink">{{ $user->filiere ?: '—' }}</dd>
                            </div>
                        </dl>

                        <div class="px-6 py-4 border-t border-blush bg-blush/20">
                            <a href="{{ route('profile.edit') }}"
                                class="text-xs font-bold text-bordeaux hover:text-bordeaux-dark link-underline">
                                Edit profile
                            </a>
                        </div>
                    </section>

                    {{-- Roles --}}
                    <section class="bg-white rounded-2xl border border-blush shadow-soft overflow-hidden animate-fade-in-up delay-300">
                        <header class="px-6 py-5 border-b border-blush">
                            <h2 class="text-base font-bold text-ink">Roles</h2>
                        </header>

                        <div class="px-6 py-5">
                            @if ($user->roles->isEmpty())
                            <p class="text-sm text-mauve">No roles assigned.</p>
                            @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($user->roles as $role)
                                <span class="text-xs font-bold px-2.5 py-1.5 rounded-md
                                                 bg-bordeaux-50 text-bordeaux-800 capitalize
                                                 ring-1 ring-bordeaux-100">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </section>

                    {{-- Quick links --}}
                    <section class="bg-white rounded-2xl border border-blush shadow-soft overflow-hidden animate-fade-in-up delay-400">
                        <header class="px-6 py-5 border-b border-blush">
                            <h2 class="text-base font-bold text-ink">Jump to</h2>
                        </header>

                        <nav class="divide-y divide-blush">
                            <a href="{{ route('events.index') }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-bordeaux-50/40 transition-colors group">
                                <span class="text-sm font-medium text-ink group-hover:text-bordeaux transition-colors">Browse events</span>
                                <span class="text-mauve group-hover:text-bordeaux group-hover:translate-x-1 transition-all text-base leading-none">→</span>
                            </a>

                            @can('viewAny', \App\Models\User::class)
                            <a href="{{ route('members.index') }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-bordeaux-50/40 transition-colors group">
                                <span class="text-sm font-medium text-ink group-hover:text-bordeaux transition-colors">Members</span>
                                <span class="text-mauve group-hover:text-bordeaux group-hover:translate-x-1 transition-all text-base leading-none">→</span>
                            </a>
                            @endcan

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-bordeaux-50/40 transition-colors group">
                                <span class="text-sm font-medium text-ink group-hover:text-bordeaux transition-colors">Profile settings</span>
                                <span class="text-mauve group-hover:text-bordeaux group-hover:translate-x-1 transition-all text-base leading-none">→</span>
                            </a>
                        </nav>
                    </section>

                </aside>
            </div>

        </div>
    </div>
</x-app-layout>