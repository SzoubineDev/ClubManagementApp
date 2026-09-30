<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <a href="{{ route('events.index') }}"
                    class="text-xs text-gray-500 hover:text-gray-900">
                    ← Events
                </a>
                <div class="flex items-center gap-2 mt-1">
                    <h1 class="text-lg font-semibold text-gray-900 truncate">
                        {{ $event->title }}
                    </h1>

                    @if ($event->isPublished())
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Published
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-700 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                        Draft
                    </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @can('manageAttendance', $event)
                <a href="{{ route('events.attendance', $event) }}"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Attendance
                </a>
                @endcan

                @can('update', $event)
                <a href="{{ route('events.edit', $event) }}"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Edit
                </a>
                @endcan

                @can('publish', $event)
                @if (! $event->isPublished())
                <form method="POST" action="{{ route('events.publish', $event) }}">
                    @csrf
                    @method('PATCH')
                    <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Publish
                    </button>
                </form>
                @endif
                @endcan

                @can('delete', $event)
                <form method="POST" action="{{ route('events.destroy', $event) }}"
                    onsubmit="return confirm('Delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">{{ session('status') }}</p>
            </div>
            @endif

            @error('rsvp')
            <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">{{ $message }}</p>
            </div>
            @enderror

            {{-- Details --}}
            <div class="bg-white border border-gray-200 rounded-lg">
                <dl class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 border-b border-gray-100">
                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Starts</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 tabular-nums">
                            {{ $event->starts_at->format('M j, Y') }}
                        </dd>
                        <p class="text-xs text-gray-500 mt-0.5 tabular-nums">
                            {{ $event->starts_at->format('H:i') }}
                        </p>
                    </div>

                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ends</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 tabular-nums">
                            {{ $event->ends_at?->format('M j, Y') ?: '—' }}
                        </dd>
                        @if ($event->ends_at)
                        <p class="text-xs text-gray-500 mt-0.5 tabular-nums">
                            {{ $event->ends_at->format('H:i') }}
                        </p>
                        @endif
                    </div>

                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 truncate">
                            {{ $event->location ?: 'TBA' }}
                        </dd>
                    </div>

                    <div class="px-5 py-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Team</dt>
                        <dd class="mt-1.5 text-sm font-medium text-gray-900 capitalize">
                            {{ $event->team ?: 'Global' }}
                        </dd>
                    </div>
                </dl>

                <div class="px-5 py-4 flex items-center justify-between gap-3 border-b border-gray-100">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 text-xs font-medium flex-shrink-0">
                            {{ strtoupper(substr($event->creator->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500">Organized by</p>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $event->creator->name }}</p>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-500">Capacity</p>
                        <p class="text-sm font-medium text-gray-900 tabular-nums">
                            {{ $event->attendees->count() }}
                            @if ($event->capacity)
                            <span class="text-gray-400">/ {{ $event->capacity }}</span>
                            @else
                            <span class="text-gray-400">/ ∞</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if ($event->description)
                <div class="px-5 py-4">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h2>
                    <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->description }}</div>
                </div>
                @endif
            </div>

            {{-- RSVP --}}
            <div class="bg-white border border-gray-200 rounded-lg"
                x-data="{
                     registered: {{ auth()->user()->isRegisteredFor($event) ? 'true' : 'false' }},
                     count: {{ $event->attendees->count() }},
                     loading: false,
                     async toggle() {
                         this.loading = true
                         const wasRegistered = this.registered
                         this.registered = ! this.registered
                         this.count += this.registered ? 1 : -1

                         try {
                             const res = await fetch(
                                 wasRegistered
                                     ? '{{ route('events.rsvp.cancel', $event) }}'
                                     : '{{ route('events.rsvp', $event) }}',
                                 {
                                     method: wasRegistered ? 'DELETE' : 'POST',
                                     headers: {
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json',
                                     },
                                 }
                             )
                             if (! res.ok) throw new Error('failed')
                             window.location.reload()
                         } catch (e) {
                             this.registered = wasRegistered
                             this.count += wasRegistered ? 1 : -1
                             alert('Something went wrong. Please try again.')
                         } finally {
                             this.loading = false
                         }
                     }
                 }">
                <div class="px-5 py-4 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900"
                            x-text="registered ? 'You\'re going' : 'Want to join?'"></p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if ($event->capacity)
                            <span x-text="Math.max(0, {{ $event->capacity }} - count)"></span> spots remaining
                            @else
                            Open to all members
                            @endif
                        </p>
                    </div>

                    <button type="button"
                        @click="toggle"
                        :disabled="loading"
                        :class="registered
                                ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                : 'bg-bordeaux text-white border border-bordeaux hover:bg-bordeaux-light'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md disabled:opacity-60 transition-colors">
                        <span x-text="loading ? '…' : (registered ? 'Cancel RSVP' : 'RSVP')"></span>
                    </button>
                </div>
            </div>

            {{-- Attendees --}}
            @if ($event->attendees->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Attendees</h2>
                    <span class="text-xs text-gray-500 tabular-nums">
                        {{ $event->attendees->count() }}
                        {{ Str::plural('person', $event->attendees->count()) }}
                    </span>
                </div>

                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                            <th class="text-right px-5 py-2.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                @if ($attendee->pivot->attended)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Attended
                                </span>
                                @else
                                <span class="text-xs text-gray-500">Registered</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>