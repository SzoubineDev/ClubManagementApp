<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">Members</h1>
                <p class="page-header-sub">Manage club members, roles, and teams</p>
            </div>
            <span class="chip chip--brand">{{ $members->total() }} {{ Str::plural('member', $members->total()) }}</span>
        </div>
    </x-slot>

    <div class="content-wrap stack">

        {{-- KPI strip --}}
        <div class="kpi-grid stagger">
            <div class="kpi-card">
                <p class="stat-label">Total</p>
                <p class="stat-value tabular mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Active</p>
                <p class="stat-value tabular mt-1" style="color: var(--teal-600);">{{ $stats['active'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Suspended</p>
                <p class="stat-value tabular mt-1" style="color: var(--danger);">{{ $stats['suspended'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Deleted</p>
                <p class="stat-value tabular mt-1" style="color: var(--gray-500);">{{ $stats['trashed'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('members.index') }}" class="card animate-fade-in-up">
            <div class="card-header">
                <h2 class="card-title">Filters</h2>
            </div>

            <div class="card-body grid grid--2 gap-3" id="member-filters">
                <div style="grid-column: span 1;">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search name, email, or CNE" class="input">
                </div>

                <select name="team" class="field-select">
                    <option value="">All teams</option>
                    @foreach ($teams as $t)
                    <option value="{{ $t }}" @selected(request('team')===$t)>{{ ucfirst($t) }}</option>
                    @endforeach
                    <option value="none" @selected(request('team')==='none')>No team</option>
                </select>

                <select name="role" class="field-select">
                    <option value="">All roles</option>
                    @foreach ($roles as $r)
                    <option value="{{ $r }}" @selected(request('role')===$r)>{{ str_replace('_', ' ', $r) }}</option>
                    @endforeach
                </select>

                <select name="filiere" class="field-select">
                    <option value="">All filières</option>
                    @foreach ($filieres as $f)
                    <option value="{{ $f }}" @selected(request('filiere')===$f)>{{ $f }}</option>
                    @endforeach
                </select>

                <select name="status" class="field-select">
                    <option value="">Any status</option>
                    <option value="active" @selected(request('status')==='active')>Active only</option>
                    <option value="suspended" @selected(request('status')==='suspended')>Suspended only</option>
                </select>
            </div>

            <div class="card-footer flex-wrap">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" name="trashed" value="1" @checked(request('trashed')) class="checkbox">
                    Show deleted members
                </label>

                <div class="card-footer-actions">
                    @if (request()->hasAny(['q', 'team', 'role', 'filiere', 'status', 'trashed']))
                    <a href="{{ route('members.index') }}" class="text-action">Clear</a>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </div>
        </form>

        {{-- Members table --}}
        <div class="card overflow-hidden animate-fade-in-up delay-100">
            @if ($members->isEmpty())
            <div class="empty-state">
                <p class="empty-state-title">No matching members</p>
                <p>Try adjusting or clearing your filters.</p>
            </div>
            @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th class="hide-mobile">CNE</th>
                            <th class="only-desktop">Filière</th>
                            <th class="hide-mobile">Team</th>
                            <th>Roles</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <span class="avatar">
                                        @if ($member->photo_path)
                                        <img src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                                        @else
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('members.show', $member) }}" class="cell-title truncate">{{ $member->name }}</a>

                                            @if ($member->trashed())
                                            <span class="chip" style="font-size:10px; padding:2px 8px;">Deleted</span>
                                            @elseif ($member->isSuspended())
                                            <span class="chip chip--danger" style="font-size:10px; padding:2px 8px;">Suspended</span>
                                            @endif
                                        </div>
                                        <p class="cell-sub truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="hide-mobile tabular">{{ $member->cne ?: '—' }}</td>

                            <td class="only-desktop">{{ $member->filiere ?: '—' }}</td>

                            <td class="hide-mobile">
                                @if ($member->team)
                                <span class="chip capitalize">{{ $member->team }}</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="chip-row">
                                    @forelse ($member->roles as $role)
                                    <span class="chip {{ $role->name === 'president' ? 'chip--primary-solid' : '' }}"
                                        style="font-size:11px; padding:2px 10px;">
                                        {{ str_replace('_', ' ', $role->name) }}
                                    </span>
                                    @empty
                                    <span class="text-muted text-xs">None</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="td-right td-nowrap">
                                <a href="{{ route('members.show', $member) }}" class="text-action">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($members->hasPages())
        <div>{{ $members->links() }}</div>
        @endif

    </div>

    <style>
    @media (min-width: 1024px) {
        #member-filters { grid-template-columns: repeat(6, 1fr) !important; }
        #member-filters > div:first-child { grid-column: span 2 !important; }
    }
    @media (min-width: 640px) and (max-width: 1023px) {
        #member-filters { grid-template-columns: 1fr 1fr !important; }
    }
    </style>
</x-app-layout>
