<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">{{ $sectionName }} Section</h1>
                <p class="page-header-sub">Daily presence register</p>
            </div>
            <span class="chip chip--brand">
                {{ $members->count() }} {{ Str::plural('member', $members->count()) }}
            </span>
        </div>
    </x-slot>

    <div class="content-wrap stack">

        @if (session('status'))
        <div class="alert alert-success animate-fade-in-up">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up">{{ $errors->first() }}</div>
        @endif

        {{-- Date picker --}}
        <form method="GET" action="{{ route('section.attendance.index') }}" class="card animate-fade-in-up">
            <div class="card-body flex items-end gap-3 flex-wrap">
                <div class="field" style="max-width: 240px;">
                    <label for="date" class="field-label static">Presence date</label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $selectedDate->format('Y-m-d') }}"
                        max="{{ today()->format('Y-m-d') }}"
                        class="input"
                        onchange="this.form.submit()">
                </div>
                <p class="text-sm text-muted" style="margin-bottom: 10px;">
                    Recording presence for <strong>{{ $selectedDate->format('M j, Y') }}</strong>.
                </p>
            </div>
        </form>

        {{-- Register --}}
        <div class="card animate-fade-in-up delay-100">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Register presence</h2>
                    <p class="card-subtitle">Tick every member who attended on this day.</p>
                </div>
            </div>

            @if ($members->isEmpty())
            <div class="empty-state">
                <p class="empty-state-title">No members in this section</p>
                <p>Members appear here once their team is set to {{ $sectionName }}.</p>
            </div>
            @else
            <form method="POST" action="{{ route('section.attendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th class="hide-mobile col-optional">Email</th>
                                <th class="td-right">Present</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                            <tr>
                                <td>
                                    <div class="person-cell">
                                        <span class="avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <span class="cell-title">{{ $member->name }}</span>
                                            @if ($member->isSuspended())
                                            <span class="chip chip--danger" style="font-size:10px;padding:2px 8px;">Suspended</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile col-optional text-muted">{{ $member->email }}</td>
                                <td class="td-right">
                                    <input
                                        type="checkbox"
                                        name="present[]"
                                        value="{{ $member->id }}"
                                        @checked($records->get($member->id)?->present)
                                        class="checkbox">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer justify-end">
                    <button class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save presence
                    </button>
                </div>
            </form>
            @endif
        </div>

        {{-- Recent history --}}
        <div class="card animate-fade-in-up delay-200">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Last 14 days</h2>
                    <p class="card-subtitle">Presence totals for this section.</p>
                </div>
            </div>

            @if ($history->isEmpty())
            <div class="empty-state">No presence recorded yet.</div>
            @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="td-right">Present</th>
                            <th class="td-right">Recorded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $row)
                        <tr>
                            <td class="cell-title">{{ \Carbon\Carbon::parse($row->date)->format('M j, Y') }}</td>
                            <td class="td-right tabular">{{ (int) $row->present_count }}</td>
                            <td class="td-right tabular">{{ (int) $row->recorded_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
