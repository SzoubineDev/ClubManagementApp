@php
$isEdit = ($event !== null);
$old = fn ($field, $default = null) => old($field, $isEdit ? $event->$field : $default);

$initialTitle = $old('title', '');
$initialDesc = $old('description', '');
$initialLocation = $old('location', '');
$initialStartsAt = $isEdit && $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : old('starts_at', '');
$initialEndsAt = $isEdit && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : old('ends_at', '');
$initialTeam = $old('team', '');
$initialFiliere = $old('filiere', '');
$initialCapacity = $old('capacity', '');

if (!empty($initialTeam)) {
    $initialAudience = 'team';
} elseif (!empty($initialFiliere)) {
    $initialAudience = 'filiere';
} else {
    $initialAudience = 'all';
}
@endphp

<div class="card-body space-y-6" x-data="{
    audience: '{{ $initialAudience }}',
    team: '{{ $initialTeam }}',
    filiere: '{{ $initialFiliere }}',
    setAudience(val) {
        this.audience = val;
        if (val === 'all') {
            this.team = '';
            this.filiere = '';
        } else if (val === 'team') {
            this.filiere = '';
            if (!this.team) this.team = 'arabic';
        } else if (val === 'filiere') {
            this.team = '';
            if (!this.filiere) this.filiere = 'LICENSE';
        }
    }
}">

    {{-- Title --}}
    <div>
        <label for="title" class="field-caption">Event Title <span class="text-danger">*</span></label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ $initialTitle }}"
            required
            maxlength="255"
            placeholder="e.g. Weekly Debate Tournament"
            class="input @error('title') is-invalid @enderror">
        @error('title')
            <p class="field-error mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="field-caption">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Event details, motions, guest speakers, or preparation agenda..."
            class="input @error('description') is-invalid @enderror">{{ $initialDesc }}</textarea>
        @error('description')
            <p class="field-error mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Location --}}
    <div>
        <label for="location" class="field-caption">Location / Venue</label>
        <input
            type="text"
            id="location"
            name="location"
            value="{{ $initialLocation }}"
            maxlength="255"
            placeholder="e.g. Amphitheater A, Room 204, or Online Meet"
            class="input @error('location') is-invalid @enderror">
        @error('location')
            <p class="field-error mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Schedule (Starts At & Ends At) --}}
    <div class="grid grid--2 gap-4">
        <div>
            <label for="starts_at" class="field-caption">Starts at <span class="text-danger">*</span></label>
            <input
                type="datetime-local"
                id="starts_at"
                name="starts_at"
                value="{{ $initialStartsAt }}"
                @if(!$isEdit) min="{{ now()->format('Y-m-d\TH:i') }}" @endif
                required
                class="input @error('starts_at') is-invalid @enderror">
            @error('starts_at')
                <p class="field-error mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ends_at" class="field-caption">Ends at <span class="text-muted">(Optional)</span></label>
            <input
                type="datetime-local"
                id="ends_at"
                name="ends_at"
                value="{{ $initialEndsAt }}"
                class="input @error('ends_at') is-invalid @enderror">
            @error('ends_at')
                <p class="field-error mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Capacity --}}
    <div>
        <label for="capacity" class="field-caption">Capacity <span class="text-muted">(Optional)</span></label>
        <input
            type="number"
            id="capacity"
            name="capacity"
            min="1"
            value="{{ $initialCapacity }}"
            placeholder="Leave empty for unlimited attendance"
            class="input @error('capacity') is-invalid @enderror"
            style="max-width: 320px;">
        <p class="field-hint">Maximum number of attendees allowed to RSVP. Leave blank if uncapped.</p>
        @error('capacity')
            <p class="field-error mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Audience & Eligibility --}}
    <div class="border-t pt-4">
        <label class="field-caption">Audience Restriction</label>
        <p class="field-hint mb-3">Choose whether this event is open to all members, or restricted to a specific team or study programme.</p>

        {{-- Hidden fields submitted to server --}}
        <input type="hidden" name="team" :value="audience === 'team' ? team : ''">
        <input type="hidden" name="filiere" :value="audience === 'filiere' ? filiere : ''">

        <div class="space-y-3">
            <label class="audience-radio-row">
                <input
                    type="radio"
                    name="audience_choice"
                    value="all"
                    class="radio"
                    :checked="audience === 'all'"
                    @change="setAudience('all')">
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-sm text-ink">Open to All</span>
                    <p class="text-xs text-muted mt-0.5">Available to all club members across all teams and programmes.</p>
                </div>
            </label>

            <label class="audience-radio-row">
                <input
                    type="radio"
                    name="audience_choice"
                    value="team"
                    class="radio"
                    :checked="audience === 'team'"
                    @change="setAudience('team')">
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-sm text-ink">Restrict to Team</span>
                    <p class="text-xs text-muted mt-0.5">Only members of the selected team can register.</p>

                    <div x-show="audience === 'team'" x-cloak class="mt-2.5">
                        <select class="field-select" x-model="team" style="max-width: 320px;">
                            <option value="arabic">Arabic Team</option>
                            <option value="english">English Team</option>
                            <option value="french">French Team</option>
                        </select>
                        @error('team')
                            <p class="field-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </label>

            <label class="audience-radio-row">
                <input
                    type="radio"
                    name="audience_choice"
                    value="filiere"
                    class="radio"
                    :checked="audience === 'filiere'"
                    @change="setAudience('filiere')">
                <div class="min-w-0 flex-1">
                    <span class="font-bold text-sm text-ink">Restrict to Filière</span>
                    <p class="text-xs text-muted mt-0.5">Only students enrolled in the selected programme can register.</p>

                    <div x-show="audience === 'filiere'" x-cloak class="mt-2.5">
                        <select class="field-select" x-model="filiere" style="max-width: 320px;">
                            <option value="DEUST">DEUST</option>
                            <option value="LICENSE">LICENSE</option>
                            <option value="Cycle Ingénieur">Cycle Ingénieur</option>
                            <option value="Master">Master</option>
                            <option value="Doctorat">Doctorat</option>
                        </select>
                        @error('filiere')
                            <p class="field-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </label>
        </div>
    </div>

</div>
