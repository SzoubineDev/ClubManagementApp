<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Events</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    Browse and RSVP to club events
                </p>
            </div>

            @can('create', \App\Models\Event::class)
            <a href="{{ route('events.create') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-bordeaux rounded-md hover:bg-bordeaux-light transition-colors flex-shrink-0">
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

            @if (session('status'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                @if ($events->isEmpty())
                <div class="px-5 py-16 text-center">
                    <p class="text-sm text-gray-500">No events yet.</p>
                    @can('create', \App\Models\Event::class)
                    <a href="{{ route('events.create') }}"
                        class="mt-3 inline-block text-sm font-medium text-bordeaux hover:underline">
                        Create the first event →
                    </a>
                    @endcan
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Location</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Team</th>
                                <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-right px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                                <th class="w-px"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('events.show', $event) }}"
                                        class="text-sm font-medium text-gray-900 hover:text-bordeaux">
                                        {{ $event->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        by {{ $event->creator->name }}
                                    </p>
                                </td>

                                <td class="px-5 py-3 hidden md:table-cell whitespace-nowrap">
                                    <span class="text-sm text-gray-700 tabular-nums">
                                        {{ $event->starts_at->format('M j, Y') }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-0.5 tabular-nums">
                                        {{ $event->starts_at->format('H:i') }}
                                    </p>
                                </td>

                                <td class="px-5 py-3 hidden lg:table-cell">
                                    <span class="text-sm text-gray-700">
                                        {{ $event->location ?: '—' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3 hidden sm:table-cell">
                                    @if ($event->team)
                                    <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-700 capitalize">
                                        {{ $event->team }}
                                    </span>
                                    @else
                                    <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3">
                                    @if ($event->isPublished())
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Published
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Draft
                                    </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-right">
                                    <span class="text-sm text-gray-700 tabular-nums">
                                        {{ $event->attendees_count }}
                                        @if ($event->capacity)
                                        <span class="text-gray-400">/ {{ $event->capacity }}</span>
                                        @endif
                                    </span>
                                </td>

                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('events.show', $event) }}"
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

            @if ($events->hasPages())
            <div>{{ $events->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>