<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Members</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Manage club members, roles, and teams
                </p>
            </div>
            <span class="text-sm text-gray-500 tabular-nums">
                {{ $members->total() }} {{ Str::plural('member', $members->total()) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPI strip --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Suspended</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">{{ $stats['suspended'] }}</p>
                </div>
                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">{{ $stats['trashed'] }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('members.index') }}"
                class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">Filters</h2>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div class="lg:col-span-2">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search name, email, or CNE"
                            class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                    </div>

                    <select name="team"
                        class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                        <option value="">All teams</option>
                        @foreach ($teams as $t)
                        <option value="{{ $t }}" @selected(request('team')===$t)>{{ ucfirst($t) }}</option>
                        @endforeach
                        <option value="none" @selected(request('team')==='none' )>No team</option>
                    </select>

                    <select name="role"
                        class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                        <option value="">All roles</option>
                        @foreach ($roles as $r)
                        <option value="{{ $r }}" @selected(request('role')===$r)>{{ str_replace('_', ' ', $r) }}</option>
                        @endforeach
                    </select>

                    <select name="filiere"
                        class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                        <option value="">All filières</option>
                        @foreach ($filieres as $f)
                        <option value="{{ $f }}" @selected(request('filiere')===$f)>{{ $f }}</option>
                        @endforeach
                    </select>

                    <select name="status"
                        class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                        <option value="">Any status</option>
                        <option value="active" @selected(request('status')==='active' )>Active only</option>
                        <option value="suspended" @selected(request('status')==='suspended' )>Suspended only</option>
                    </select>
                </div>

                <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3 rounded-b-lg">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="trashed" value="1" @checked(request('trashed'))
                            class="rounded border-gray-300 text-bordeaux focus:ring-bordeaux">
                        Show deleted members
                    </label>

                    <div class="flex items-center gap-2">
                        @if (request()->hasAny(['q', 'team', 'role', 'filiere', 'status', 'trashed']))
                        <a href="{{ route('members.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">
                            Clear
                        </a>
                        @endif

                        <button type="submit"
                            class="px-3 py-1.5 bg-bordeaux text-white text-sm font-medium rounded-md hover:bg-bordeaux-light transition-colors">
                            Apply
                        </button>
                    </div>
                </div>
            </form>

            {{-- Members table --}}
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                @if ($members->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500">No members match your filters.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">CNE</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Filière</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Team</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="w-px"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($members as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 text-xs font-medium">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('members.show', $member) }}"
                                                    class="text-sm font-medium text-gray-900 hover:text-bordeaux truncate">
                                                    {{ $member->name }}
                                                </a>

                                                @if ($member->trashed())
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-gray-200 text-gray-700 uppercase tracking-wide">
                                                    Deleted
                                                </span>
                                                @elseif ($member->isSuspended())
                                                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-red-100 text-red-700 uppercase tracking-wide">
                                                    Suspended
                                                </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 truncate mt-0.5">
                                                {{ $member->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-3 hidden md:table-cell">
                                    <span class="text-sm text-gray-700 tabular-nums">
                                        {{ $member->cne ?: '—' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3 hidden lg:table-cell">
                                    <span class="text-sm text-gray-700">
                                        {{ $member->filiere ?: '—' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3 hidden sm:table-cell">
                                    @if ($member->team)
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-700 capitalize">
                                        {{ $member->team }}
                                    </span>
                                    @else
                                    <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($member->roles as $role)
                                        <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded
                                                        {{ $role->name === 'president' ? 'bg-bordeaux text-white' : 'bg-gray-100 text-gray-700' }}">
                                            {{ str_replace('_', ' ', $role->name) }}
                                        </span>
                                        @empty
                                        <span class="text-xs text-gray-400">None</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('members.show', $member) }}"
                                        class="text-xs font-medium text-bordeaux hover:underline whitespace-nowrap">
                                        View
                                    </a>
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
            <div>
                {{ $members->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>