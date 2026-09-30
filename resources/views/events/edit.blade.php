<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('events.show', $event) }}"
                class="text-xs text-gray-500 hover:text-gray-900">
                ← {{ $event->title }}
            </a>
            <h1 class="text-lg font-semibold text-gray-900 mt-1">Edit event</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update event details</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
            <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-medium text-red-900">Please fix the following:</p>
                <ul class="mt-1.5 list-disc list-inside text-sm text-red-700 space-y-0.5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('events.update', $event) }}"
                class="bg-white border border-gray-200 rounded-lg">
                @csrf
                @method('PUT')

                @include('events._form', ['event' => $event])

                <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between rounded-b-lg">
                    <a href="{{ route('events.show', $event) }}"
                        class="text-sm text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bordeaux text-white text-sm font-medium rounded-md hover:bg-bordeaux-light transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>