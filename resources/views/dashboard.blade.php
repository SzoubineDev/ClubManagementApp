<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Welcome back, {{ $user->name }}
                </p>
            </div>

            @can('create', \App\Models\Event::class)
            <a href="{{ route('events.create') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-bordeaux rounded-md hover:bg-bordeaux-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New event
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPI strip --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Events attended</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">
                        {{ $stats['attended'] }}
                    </p>
                </div>

                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Upcoming RSVPs</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 tabular-nums">
                        {{ $stats['rsvps'] }}
                    </p>
                </div>

                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Member since</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ $stats['member_since']->format('M Y') }}
                    </p>
                </div>

                <div class="bg-white px-5 py-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 capitalize">
                        {{ $user->team ?: '—' }}
                    </p>
                </div>
            </div>

            {{-- Two-column layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main column --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Upcoming events --}}
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">Your upcoming events</h2>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Events you've RSVP'd to
                                </p>
                            </div>
                            <a href="{{ route('events.index') }}"
                                class="text-xs font-medium text-bordeaux hover:underline">
                                View all
                            </a>
                        </div>

                        @if ($upcomingRsvps->isEmpty())
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm text-gray-500">You haven't RSVP'd to anything yet.</p>
                            <a href="{{ route('events.index') }}"
                                class="mt-2 inline-block text-sm font-medium text-bordeaux hover:underline">
                                Browse events →
                            </a>
                        </div>
                        @else
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                    <th class="text-right px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($upcomingRsvps as $event)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-sm font-medium text-gray-900 hover:text-bordeaux">
                                            {{ $event->title }}
                                        </a>
                                        @if ($event->location)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $event->location }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        <span class="text-sm text-gray-700 tabular-nums">
                                            {{ $event->starts_at->format('M j, Y') }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-0.5 tabular-nums">
                                            {{ $event->starts_at->format('H:i') }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Going
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>

                    {{-- Discover --}}
                    @if ($visibleEvents->isNotEmpty())
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">Discover events</h2>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Published events you haven't joined yet
                                </p>
                            </div>
                        </div>

                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($visibleEvents as $event)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-sm font-medium text-gray-900 hover:text-bordeaux">
                                            {{ $event->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            by {{ $event->creator->name }}
                                            @if ($event->team)
                                            · <span class="capitalize">{{ $event->team }}</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <span class="text-sm text-gray-700 tabular-nums">
                                            {{ $event->starts_at->format('M j') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right w-px">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-xs font-medium text-bordeaux hover:underline">
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

                {{-- Side column --}}
                <div class="space-y-6">

                    {{-- Account --}}
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Account</h2>
                        </div>

                        <dl class="divide-y divide-gray-100">
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">CNE</dt>
                                <dd class="text-sm font-medium text-gray-900 tabular-nums">{{ $user->cne ?: '—' }}</dd>
                            </div>
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">Filière</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $user->filiere ?: '—' }}</dd>
                            </div>
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">Team</dt>
                                <dd class="text-sm font-medium text-gray-900 capitalize">{{ $user->team ?: 'None' }}</dd>
                            </div>
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-700 truncate max-w-[60%]">{{ $user->email }}</dd>
                            </div>
                        </dl>

                        <div class="px-5 py-3 border-t border-gray-100">
                            <a href="{{ route('profile.edit') }}"
                                class="text-xs font-medium text-bordeaux hover:underline">
                                Edit profile →
                            </a>
                        </div>
                    </div>

                    {{-- Roles --}}
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Your roles</h2>
                        </div>

                        <div class="px-5 py-4">
                            @if ($user->roles->isEmpty())
                            <p class="text-sm text-gray-500">No roles assigned.</p>
                            @else
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($user->roles as $role)
                                <span class="inline-flex items-center text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-700 capitalize">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick links --}}
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Quick links</h2>
                        </div>

                        <nav class="divide-y divide-gray-100">
                            <a href="{{ route('events.index') }}"
                                class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors group">
                                <span class="text-sm text-gray-700 group-hover:text-bordeaux">Browse events</span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-bordeaux" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            @can('viewAny', \App\Models\User::class)
                            <a href="{{ route('members.index') }}"
                                class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors group">
                                <span class="text-sm text-gray-700 group-hover:text-bordeaux">Members</span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-bordeaux" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endcan

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors group">
                                <span class="text-sm text-gray-700 group-hover:text-bordeaux">Profile settings</span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-bordeaux" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </nav>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>