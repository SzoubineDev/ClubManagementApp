<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-header-title">{{ __('Dashboard') }}</h1>
            <p class="page-header-sub">Welcome back, {{ Auth::user()->name }}.</p>
        </div>
        @can('create', \App\Models\Event::class)
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Event
        </a>
        @endcan
    </x-slot>

    <div class="content-wrap stack">
        {{-- Hero --}}
        <section class="hero-card animate-fade-in-up">
            <div class="hero-inner">
                <div class="hero-id">
                    <div class="hero-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div>
                        <p class="hero-eyebrow">{{ config('app.name', 'Uni Club') }}</p>
                        <h2 class="hero-title">Welcome back, <strong>{{ Auth::user()->name }}</strong></h2>
                        <p class="hero-text">
                            You're all set for the week ahead. Your club currently has
                            <strong>{{ $totalMembers ?? \App\Models\User::count() }}</strong> active members.
                        </p>
                    </div>
                </div>
                @if (Auth::user()->hasRole('president'))
                <a href="{{ route('members.index') }}" class="fab">Manage Members</a>
                @endif
            </div>
        </section>

        {{-- Stats strip --}}
        <section class="stats-strip animate-fade-in-up delay-100">
            <div class="stat-cell">
                <p class="stat-value tabular">{{ $upcomingCount ?? 0 }}</p>
                <p class="stat-label">Upcoming Events</p>
            </div>
            <div class="stat-cell">
                <p class="stat-value tabular">{{ $recentAttendanceCount ?? 0 }}</p>
                <p class="stat-label">Attendances Logged</p>
            </div>
            <div class="stat-cell">
                <p class="stat-value tabular">{{ $totalMembers ?? \App\Models\User::count() }}</p>
                <p class="stat-label">Total Members</p>
            </div>
        </section>

        {{-- Two column layout --}}
        <div class="layout-grid">
            {{-- Upcoming events --}}
            <section class="card animate-fade-in-up delay-200">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Upcoming Events</h3>
                        <p class="card-subtitle">Next activities on the calendar</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-action">View all</a>
                </div>

                @forelse (($upcomingEvents ?? collect()) as $event)
                <a href="{{ route('events.show', $event) }}" class="list-item">
                    <div class="date-chip">
                        <div class="date-chip-month">{{ $event->starts_at->format('M') }}</div>
                        <div class="date-chip-day">{{ $event->starts_at->format('j') }}</div>
                    </div>
                    <div class="list-item-main">
                        <div class="list-item-title">{{ $event->title }}</div>
                        <div class="list-item-sub">
                            {{ $event->starts_at->translatedFormat('l, g:i A') }}
                            @if ($event->location) · {{ $event->location }} @endif
                        </div>
                    </div>
                    @if (auth()->user()->can('events.update'))
                        @if ($event->isPublished())
                        <span class="chip chip--success hide-mobile">Published</span>
                        @else
                        <span class="chip chip--warning hide-mobile">Draft — only you & managers see this</span>
                        @endif
                    @endif
                    <span class="list-chevron">›</span>
                </a>
                @empty
                <div class="empty-state">
                    <p class="empty-state-title">No upcoming events yet</p>
                    <p>Plan your next gathering and it will show up here.</p>
                    @can('create', \App\Models\Event::class)
                    <a href="{{ route('events.create') }}" class="text-action">Create an event →</a>
                    @endcan
                </div>
                @endforelse
            </section>

            {{-- Sidebar --}}
            <aside class="stack stack--sm animate-fade-in-up delay-300">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Your Account</h3>
                    </div>
                    <dl class="spec-list">
                        <div>
                            <dt class="spec-key">Name</dt>
                            <dd class="spec-val">{{ Auth::user()->name }}</dd>
                        </div>
                        <div>
                            <dt class="spec-key">Email</dt>
                            <dd class="spec-val">{{ Auth::user()->email }}</dd>
                        </div>
                        <div class="spec-grid-2">
                            <div>
                                <dt class="spec-key">CNE</dt>
                                <dd class="spec-val">{{ Auth::user()->cne ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="spec-key">Filière</dt>
                                <dd class="spec-val">{{ Auth::user()->filiere ?? '—' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="spec-key">Roles</dt>
                            <dd class="spec-val capitalize">{{ Auth::user()->roles->pluck('name')->join(', ') ?: 'member' }}</dd>
                        </div>
                    </dl>
                    <div class="card-footer">
                        <a href="{{ route('profile.edit') }}" class="text-action">Edit profile →</a>
                    </div>
                </div>

                @can('viewAny', \App\Models\User::class)
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Administration</h3>
                        <p class="section-desc" style="margin-top:8px; text-align:left;">
                            Review member roles, teams, and suspensions.
                        </p>
                        <a href="{{ route('members.index') }}" class="btn btn-outline w-full mt-4">Open Members Console</a>
                    </div>
                </div>
                @endcan
            </aside>
        </div>
    </div>
</x-app-layout>
