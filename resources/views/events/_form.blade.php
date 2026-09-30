@php
$isEdit = $event !== null;
$old = fn ($field, $default = null) => old($field, $isEdit ? $event->$field : $default);
@endphp

<div class="divide-y divide-gray-100">

    {{-- Title --}}
    <div class="px-5 py-4">
        <label for="title" class="block text-sm font-semibold text-gray-900">
            Title <span class="text-red-500">*</span>
        </label>
        <input type="text"
            id="title"
            name="title"
            value="{{ $old('title') }}"
            placeholder="e.g. Weekly Arabic Meetup"
            class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none"
            required>
        @error('title')
        <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div class="px-5 py-4">
        <label for="description" class="block text-sm font-semibold text-gray-900">Description</label>
        <textarea id="description"
            name="description"
            rows="4"
            placeholder="What's this event about?"
            class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">{{ $old('description') }}</textarea>
        @error('description')
        <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    {{-- Location --}}
    <div class="px-5 py-4">
        <label for="location" class="block text-sm font-semibold text-gray-900">Location</label>
        <input type="text"
            id="location"
            name="location"
            value="{{ $old('location') }}"
            placeholder="e.g. Room 204, Faculty of Sciences"
            class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
        @error('location')
        <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    {{-- Dates --}}
    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="starts_at" class="block text-sm font-semibold text-gray-900">
                Starts at <span class="text-red-500">*</span>
            </label>
            <input type="datetime-local"
                id="starts_at"
                name="starts_at"
                value="{{ $isEdit ? $event->starts_at?->format('Y-m-d\TH:i') : old('starts_at') }}"
                class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none"
                required>
            @error('starts_at')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ends_at" class="block text-sm font-semibold text-gray-900">Ends at</label>
            <input type="datetime-local"
                id="ends_at"
                name="ends_at"
                value="{{ $isEdit ? $event->ends_at?->format('Y-m-d\TH:i') : old('ends_at') }}"
                class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
            @error('ends_at')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Capacity + Team --}}
    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="capacity" class="block text-sm font-semibold text-gray-900">Capacity</label>
            <input type="number"
                id="capacity"
                name="capacity"
                min="1"
                value="{{ $old('capacity') }}"
                placeholder="Leave empty for unlimited"
                class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
            @error('capacity')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="team" class="block text-sm font-semibold text-gray-900">Team</label>
            <select id="team"
                name="team"
                class="mt-1.5 block w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none">
                <option value="">Global event (all teams)</option>
                @foreach (['arabic', 'english', 'french'] as $t)
                <option value="{{ $t }}" @selected($old('team')===$t)>
                    {{ ucfirst($t) }} team
                </option>
                @endforeach
            </select>
            @error('team')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1.5">
                Team leads can only manage events for their own team.
            </p>
        </div>
    </div>

</div>