<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('events.show', $event) }}"
                class="text-xs text-gray-500 hover:text-gray-900">
                ← {{ $event->title }}
            </a>
            <h1 class="text-lg font-semibold text-gray-900 mt-1">Attendance</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Mark who attended this event
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
            @endif

            @if ($event->attendees->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg px-5 py-12 text-center">
                <p class="text-sm text-gray-500">No attendees yet.</p>
            </div>
            @else
            <form method="POST" action="{{ route('events.attendance.update', $event) }}"
                class="bg-white border border-gray-200 rounded-lg">
                @csrf
                @method('PATCH')

                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Attendees</h2>
                    <span class="text-xs text-gray-500 tabular-nums">
                        {{ $event->attendees->count() }} total
                    </span>
                </div>

                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                            <th class="text-right px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider w-px">Attended</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($event->attendees as $attendee)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 text-xs font-medium">
                                        {{ strtoupper(substr($attendee->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $attendee->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-sm text-gray-500">{{ $attendee->email }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <input type="checkbox"
                                    name="attended[]"
                                    value="{{ $attendee->id }}"
                                    @checked($attendee->pivot->attended)
                                class="rounded border-gray-300 text-bordeaux focus:ring-bordeaux">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-end rounded-b-lg">
                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bordeaux text-white text-sm font-medium rounded-md hover:bg-bordeaux-light transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save attendance
                    </button>
                </div>
            </form>
            @endif

        </div>
    </div>
</x-app-layout>