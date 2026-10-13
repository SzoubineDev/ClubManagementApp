<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">Events</h1>
                <p class="page-header-sub">Browse and RSVP to club events</p>
            </div>

            @can('create', \App\Models\Event::class)
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New event
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="content-wrap stack">

        @if (session('status'))
        <div class="alert alert-success animate-fade-in-down">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <div class="card animate-fade-in-up">
            @if ($events->isEmpty())
            <div class="empty-state">
                <p class="empty-state-title">No events yet</p>
                <p>The first club gathering is still waiting to be planned.</p>
                @can('create', \App\Models\Event::class)
                <a href="{{ route('events.create') }}" class="text-action">Create the first event →</a>
                @endcan
            </div>
            @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th class="hide-mobile col-secondary">Date</th>
                            <th class="only-desktop col-optional">Location</th>
                            <th class="hide-mobile col-optional">Team</th>
                            <th>Status</th>
                            <th class="th-right">Attendees</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                        <tr>
                            <td>
                                <p class="cell-title"><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></p>
                                <p class="cell-sub">by {{ $event->creator->name }}</p>
                            </td>

                            <td class="hide-mobile col-secondary td-nowrap">
                                <span class="tabular">{{ $event->starts_at->format('M j, Y') }}</span>
                                <p class="cell-sub tabular">{{ $event->starts_at->format('H:i') }}</p>
                            </td>

                            <td class="only-desktop col-optional">{{ $event->location ?: '—' }}</td>

                            <td class="hide-mobile col-optional">
                                @if ($event->team)
                                <span class="chip capitalize">{{ $event->team }}</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                @if ($event->isPublished())
                                <span class="status-label status-label--on">
                                    <span class="status-dot status-dot--on"></span> Published
                                </span>
                                @else
                                <span class="status-label status-label--draft">
                                    <span class="status-dot status-dot--draft"></span> Draft
                                </span>
                                @endif
                            </td>

                            <td class="td-right tabular">
                                {{ $event->attendees_count }}
                                @if ($event->capacity)
                                <span class="text-muted">/ {{ $event->capacity }}</span>
                                @endif
                            </td>

                            <td class="td-right td-nowrap">
                                <a href="{{ route('events.show', $event) }}" class="text-action">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @if ($events->hasPages())
        <div>{{ $events->links() }}</div>
        @endif

    </div>
</x-app-layout>
