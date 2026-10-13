<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('events.show', $event) }}" class="breadcrumb-link">← {{ $event->title }}</a>
            <h1 class="page-header-title mt-1">Attendance</h1>
            <p class="page-header-sub">Mark who attended this event</p>
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

        @if ($event->attendees->isEmpty())
        <div class="card animate-fade-in-up">
            <div class="empty-state">
                <p class="empty-state-title">No attendees yet</p>
                <p>Once members RSVP, they'll show up here so you can mark attendance.</p>
            </div>
        </div>
        @else
        <form method="POST" action="{{ route('events.attendance.update', $event) }}" class="card animate-fade-in-up overflow-hidden">
            @csrf
            @method('PATCH')

            <div class="card-header">
                <h3 class="card-title">Attendees</h3>
                <span class="chip">{{ $event->attendees->count() }} total</span>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="hide-mobile">Email</th>
                            <th class="th-right">Attended</th>
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
                            <td class="hide-mobile text-muted">{{ $attendee->email }}</td>
                            <td class="td-right">
                                <input type="checkbox"
                                    name="attended[]"
                                    value="{{ $attendee->id }}"
                                    @checked($attendee->pivot->attended)
                                    class="checkbox">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer justify-end">
                <button class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save attendance
                </button>
            </div>
        </form>
        @endif

    </div>
</x-app-layout>
