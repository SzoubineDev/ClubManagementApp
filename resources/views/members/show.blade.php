<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <a href="{{ route('members.index') }}"
                    class="text-xs text-gray-500 hover:text-gray-900">
                    ← Members
                </a>
                <h1 class="text-lg font-semibold text-gray-900 mt-1 truncate">
                    {{ $member->name }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5 truncate">
                    {{ $member->email }}
                </p>
            </div>

            @can('manage', $member)
            <a href="{{ route('members.edit', $member) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-bordeaux rounded-md hover:bg-bordeaux-light transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status banners --}}
            @if ($member->trashed())
            <div class="px-4 py-3 bg-gray-100 border border-gray-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">This member is deleted</p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        Deleted {{ $member->deleted_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @elseif ($member->isSuspended())
            <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-900">Account suspended</p>
                    <p class="text-xs text-red-700 mt-0.5">
                        Suspended {{ $member->suspended_at?->diffForHumans() }}
                        @if ($member->suspension_reason)
                        · {{ $member->suspension_reason }}
                        @endif
                    </p>
                </div>
            </div>
            @endif

            {{-- Profile card --}}
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 text-base font-medium">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $member->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $member->email }}</p>
                    </div>
                </div>

                <dl class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100">
                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">CNE</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 tabular-nums">{{ $member->cne ?: '—' }}</dd>
                    </div>
                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Birthday</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900">
                            {{ $member->birthday?->format('M j, Y') ?: '—' }}
                        </dd>
                    </div>
                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Filière</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900">{{ $member->filiere ?: '—' }}</dd>
                    </div>
                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 capitalize">{{ $member->team ?: 'None' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Two-column --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Upcoming --}}
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Upcoming events</h2>
                        </div>

                        @if ($upcomingRsvps->isEmpty())
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm text-gray-500">No upcoming RSVPs.</p>
                        </div>
                        @else
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($upcomingRsvps as $event)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-sm font-medium text-gray-900 hover:text-bordeaux">
                                            {{ $event->title }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <span class="text-sm text-gray-700 tabular-nums">
                                            {{ $event->starts_at->format('M j · H:i') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>

                    {{-- Attended --}}
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-900">Events attended</h2>
                            <span class="text-xs text-gray-500 tabular-nums">
                                {{ $member->events()->wherePivot('attended', true)->count() }} total
                            </span>
                        </div>

                        @if ($attendedEvents->isEmpty())
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm text-gray-500">No attendance recorded.</p>
                        </div>
                        @else
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($attendedEvents as $event)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-sm font-medium text-gray-900 hover:text-bordeaux">
                                            {{ $event->title }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <span class="text-sm text-gray-500 tabular-nums">
                                            {{ $event->starts_at->format('M j, Y') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right w-px">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Attended
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>

                {{-- Right --}}
                <div class="space-y-6">

                    {{-- Roles --}}
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Roles</h2>
                        </div>

                        <div class="px-5 py-4">
                            @if ($member->roles->isEmpty())
                            <p class="text-sm text-gray-500">No roles assigned.</p>
                            @else
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($member->roles as $role)
                                <span class="inline-flex items-center text-xs font-medium px-2 py-1 rounded capitalize
                                            {{ $role->name === 'president' ? 'bg-bordeaux text-white' : 'bg-gray-100 text-gray-700' }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h2 class="text-sm font-semibold text-gray-900">Metadata</h2>
                        </div>

                        <dl class="divide-y divide-gray-100">
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">Joined</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $member->created_at->format('M j, Y') }}
                                </dd>
                            </div>
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">ID</dt>
                                <dd class="text-sm text-gray-700 tabular-nums">#{{ $member->id }}</dd>
                            </div>
                            @if ($member->trashed())
                            <div class="px-5 py-3 flex items-center justify-between gap-3">
                                <dt class="text-xs text-gray-500">Deleted</dt>
                                <dd class="text-sm text-gray-700">
                                    {{ $member->deleted_at->format('M j, Y') }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>