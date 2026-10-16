@php
$isEdit = $event !== null;
$old = fn ($field, $default = null) => old($field, $isEdit ? $event->$field : $default);
@endphp

<div class="card-body space-y-6">

    {{-- Title --}}
    <div>
        <x-floating-input name="title" label="Title *" id="title" :value="$old('title')"
            placeholder="e.g. Weekly Arabic Meetup" required />
        @error('title')
        <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="field-caption">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="What's this event about?"
            class="input">{{ $old('description') }}</textarea>
        @error('description')
        <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    {{-- Location --}}
    <div>
        <x-floating-input name="location" label="Location" id="location" :value="$old('location')"
            placeholder="e.g. Room 204, Faculty of Sciences" />
        @error('location')
        <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    {{-- Dates --}}
    <div class="grid grid--2 gap-6">
        <div>
            <label for="starts_at" class="field-caption">Starts at *</label>
            <input type="datetime-local" id="starts_at" name="starts_at"
                value="{{ $isEdit ? $event->starts_at?->format('Y-m-d\TH:i') : old('starts_at') }}"
                class="input" required>
            @error('starts_at')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ends_at" class="field-caption">Ends at</label>
            <input type="datetime-local" id="ends_at" name="ends_at"
                value="{{ $isEdit ? $event->ends_at?->format('Y-m-d\TH:i') : old('ends_at') }}"
                class="input">
            @error('ends_at')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Capacity + Team --}}
    <div class="grid grid--2 gap-6">
        <div>
            <label for="capacity" class="field-caption">Capacity</label>
            <input type="number" id="capacity" name="capacity" min="1" value="{{ $old('capacity') }}"
                placeholder="Leave empty for unlimited" class="input">
            @error('capacity')
            <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="team" class="field-caption">Team</label>
            <select id="team" name="team" class="field-select">
                <option value="">Global event (all teams)</option>
                @foreach (['arabic', 'english', 'french'] as $t)
                <option value="{{ $t }}" @selected($old('team')===$t)>
                    {{ ucfirst($t) }} team
                </option>
                @endforeach
            </select>
            @error('team')
            <p class="field-error">{{ $message }}</p>
            @enderror
            <p class="field-hint">Team leads can only manage events for their own team.</p>
        </div>
    </div>

    {{-- Filière --}}
    <div>
        <label for="filiere" class="field-caption">Filière</label>
        <select id="filiere" name="filiere" class="field-select">
            <option value="">All filières (no restriction)</option>
            @foreach (['DEUST', 'LICENSE', 'Cycle Ingénieur', 'Master', 'Doctorat'] as $f)
            <option value="{{ $f }}" @selected($old('filiere')===$f)>{{ $f }}</option>
            @endforeach
        </select>
        @error('filiere')
        <p class="field-error">{{ $message }}</p>
        @enderror
        <p class="field-hint">Restrict this event to a specific study programme, if needed.</p>
    </div>

</div>

<style>
@media (min-width: 640px) {
    #events-form .grid[style] { grid-template-columns: 1fr 1fr !important; }
}
</style>
