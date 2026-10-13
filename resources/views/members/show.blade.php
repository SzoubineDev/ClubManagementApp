<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('members.index') }}" class="breadcrumb-link">← Members</a>
                <h1 class="page-header-title truncate">{{ $member->name }}</h1>
                <p class="page-header-sub truncate">{{ $member->email }}</p>
            </div>

            @can('manage', $member)
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary btn-sm flex-shrink-0">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="content-wrap">
        <div class="stack">

            {{-- Status banners --}}
            @if ($member->trashed())
            <div class="alert alert-neutral">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                </svg>
                <div>
                    <p class="alert-title">This member is deleted</p>
                    <p>Deleted {{ $member->deleted_at->diffForHumans() }}</p>
                </div>
            </div>
            @elseif ($member->isSuspended())
            <div class="alert alert-danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                </svg>
                <div>
                    <p class="alert-title">Account suspended</p>
                    <p>
                        Suspended {{ $member->suspended_at?->diffForHumans() }}
                        @if ($member->suspension_reason)
                        · {{ $member->suspension_reason }}
                        @endif
                    </p>
                </div>
            </div>
            @endif

            {{-- Profile card --}}
            <div class="card">
                <div class="card-header">
                    <div class="avatar avatar--lg avatar--tinted">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="cell-title truncate">{{ $member->name }}</p>
                        <p class="cell-sub truncate">{{ $member->email }}</p>
                    </div>
                </div>

                <div class="stats-strip">
                    <div class="stat-cell">
                        <span class="stat-label">CNE</span>
                        <span class="stat-value tabular">{{ $member->cne ?: '—' }}</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-label">Birthday</span>
                        <span class="stat-value">{{ $member->birthday?->format('M j, Y') ?: '—' }}</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-label">Filière</span>
                        <span class="stat-value">{{ $member->filiere ?: '—' }}</span>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-label">Team</span>
                        <span class="stat-value capitalize">{{ $member->team ?: 'None' }}</span>
                    </div>
                </div>
            </div>

            {{-- Two-column --}}
            <div class="layout-grid">

                {{-- Left --}}
                <div class="stack span-2">

                    {{-- Upcoming --}}
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Upcoming events</h2>
                        </div>

                        @if ($upcomingRsvps->isEmpty())
                        <div class="empty-state">No upcoming RSVPs.</div>
                        @else
                        <div class="table-wrap">
                            <table class="data-table">
                                <tbody>
                                    @foreach ($upcomingRsvps as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ route('events.show', $event) }}" class="cell-title">
                                                {{ $event->title }}
                                            </a>
                                        </td>
                                        <td class="td-right td-nowrap">
                                            <span class="tabular">{{ $event->starts_at->format('M j · H:i') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>

                    {{-- Attended --}}
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Events attended</h2>
                            <span class="chip chip--outline tabular">
                                {{ $member->events()->wherePivot('attended', true)->count() }} total
                            </span>
                        </div>

                        @if ($attendedEvents->isEmpty())
                        <div class="empty-state">No attendance recorded.</div>
                        @else
                        <div class="table-wrap">
                            <table class="data-table">
                                <tbody>
                                    @foreach ($attendedEvents as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ route('events.show', $event) }}" class="cell-title">
                                                {{ $event->title }}
                                            </a>
                                        </td>
                                        <td class="td-right td-nowrap text-muted tabular">
                                            {{ $event->starts_at->format('M j, Y') }}
                                        </td>
                                        <td class="td-right w-px">
                                            <span class="status-label status-label--on">Attended</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Right --}}
                <div class="stack">

                    {{-- Roles --}}
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Roles</h2>
                        </div>
                        <div class="card-body">
                            @if ($member->roles->isEmpty())
                            <p class="text-muted text-sm">No roles assigned.</p>
                            @else
                            <div class="chip-row">
                                @foreach ($member->roles as $role)
                                <span class="chip {{ $role->name === 'president' ? 'chip--primary-solid' : 'chip--outline' }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Metadata</h2>
                        </div>
                        <div class="card-body card-body--flush">
                            <div class="def-row">
                                <span class="def-key">Joined</span>
                                <span class="def-val">{{ $member->created_at->format('M j, Y') }}</span>
                            </div>
                            <div class="def-row">
                                <span class="def-key">ID</span>
                                <span class="def-val tabular">#{{ $member->id }}</span>
                            </div>
                            @if ($member->trashed())
                            <div class="def-row">
                                <span class="def-key">Deleted</span>
                                <span class="def-val">{{ $member->deleted_at->format('M j, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
