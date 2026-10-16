<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div class="min-w-0">
                <a href="{{ route('events.index') }}" class="breadcrumb-link">← Events</a>
                <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <h1 class="page-header-title truncate">{{ $event->title }}</h1>
                    @if (auth()->user()->can('events.update'))
                        @if ($event->isPublished())
                        <span class="status-label status-label--on">
                            <span class="status-dot status-dot--on"></span> Published
                        </span>
                        @else
                        <span class="status-label status-label--draft">
                            <span class="status-dot status-dot--draft"></span> Draft
                        </span>
                        @endif
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @can('manageAttendance', $event)
                <a href="{{ route('events.attendance', $event) }}" class="btn btn-outline">Attendance</a>
                @endcan

                @can('update', $event)
                <a href="{{ route('events.edit', $event) }}" class="btn btn-outline">Edit</a>
                @endcan

                @can('publish', $event)
                @if (! $event->isPublished())
                <form method="POST" action="{{ route('events.publish', $event) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success">Publish</button>
                </form>
                @endif
                @endcan

                @can('delete', $event)
                <form method="POST" action="{{ route('events.destroy', $event) }}"
                    onsubmit="return confirm('Delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-warn">Delete</button>
                </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="content-wrap content-wrap--mid stack">

        @if (session('status'))
        <div class="alert alert-success animate-fade-in-down">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        @error('rsvp')
        <div class="alert alert-danger animate-fade-in-down">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ $message }}</span>
        </div>
        @enderror

        {{-- Details --}}
        <div class="card animate-fade-in-up">
            <dl class="info-grid">
                <div class="info-cell">
                    <dt class="info-key">Starts</dt>
                    <dd class="info-val tabular">{{ $event->starts_at->format('M j, Y') }}</dd>
                    <p class="info-val--sub tabular">{{ $event->starts_at->format('H:i') }}</p>
                </div>
                <div class="info-cell">
                    <dt class="info-key">Ends</dt>
                    <dd class="info-val tabular">{{ $event->ends_at?->format('M j, Y') ?: '—' }}</dd>
                    @if ($event->ends_at)
                    <p class="info-val--sub tabular">{{ $event->ends_at->format('H:i') }}</p>
                    @endif
                </div>
                <div class="info-cell">
                    <dt class="info-key">Location</dt>
                    <dd class="info-val truncate">{{ $event->location ?: 'TBA' }}</dd>
                </div>
                <div class="info-cell">
                    <dt class="info-key">Team</dt>
                    <dd class="info-val capitalize">{{ $event->team ?: 'Global' }}</dd>
                </div>
            </dl>

            <div class="def-row">
                <div class="person-cell">
                    <span class="avatar">{{ strtoupper(substr($event->creator->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="cell-sub" style="margin-top:0;">Organized by</p>
                        <p class="cell-title truncate">{{ $event->creator->name }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="cell-sub" style="margin-top:0;">Capacity</p>
                    <p class="cell-title tabular">
                        {{ $event->attendees->count() }}
                        @if ($event->capacity)
                        <span class="text-muted">/ {{ $event->capacity }}</span>
                        @else
                        <span class="text-muted">/ ∞</span>
                        @endif
                    </p>
                </div>
            </div>

            @if ($event->description)
            <div class="card-body border-t">
                <p class="info-key mb-4">Description</p>
                <div class="text-sm text-muted leading-relaxed whitespace-pre-line">{{ $event->description }}</div>
            </div>
            @endif
        </div>

        {{-- RSVP --}}
        @php $alreadyRegistered = auth()->user()->isRegisteredFor($event); @endphp
        <div class="card animate-fade-in-up delay-100">
            <div class="card-body flex items-center justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <p class="text-base font-medium text-ink">{{ $alreadyRegistered ? "You're going 🎉" : 'Want to join?' }}</p>
                    <p class="cell-sub">
                        @if ($event->capacity)
                        {{ max(0, $event->capacity - $event->attendees->count()) }} spots remaining
                        @else
                        Open to all members
                        @endif
                    </p>
                </div>

                @if ($alreadyRegistered)
                {{-- Cancel RSVP — plain POST form (no JS required), CSRF protected --}}
                <form method="POST" action="{{ route('events.rsvp.cancel', $event) }}"
                    onsubmit="return confirm('Cancel your registration for this event?')"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline" style="min-width: 140px;">Cancel RSVP</button>
                </form>
                @else
                {{-- RSVP — plain POST form (no JS required), CSRF protected --}}
                <form method="POST" action="{{ route('events.rsvp', $event) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="min-width: 140px;"
                        @if ($event->capacity && $event->attendees->count() >= $event->capacity) disabled @endif>
                        {{ $event->capacity && $event->attendees->count() >= $event->capacity ? 'Event full' : 'RSVP' }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Attendees --}}
        @if ($event->attendees->isNotEmpty())
        <div class="card animate-fade-in-up delay-200 overflow-hidden">
            <div class="card-header">
                <h3 class="card-title">Attendees</h3>
                <span class="chip">{{ $event->attendees->count() }} {{ Str::plural('person', $event->attendees->count()) }}</span>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="hide-mobile col-optional">Email</th>
                            <th class="th-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($event->attendees as $attendee)
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <span class="avatar">{{ strtoupper(substr($attendee->name, 0, 1)) }}</span>
                                    <span class="cell-title">{{ $attendee->name }}</span>
                                </div>
                            </td>
                            <td class="hide-mobile col-optional text-muted">{{ $attendee->email }}</td>
                            <td class="td-right">
                                @if ($attendee->pivot->attended)
                                <span class="status-label status-label--on">
                                    <span class="status-dot status-dot--on"></span> Attended
                                </span>
                                @else
                                <span class="text-muted text-xs">Registered</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
