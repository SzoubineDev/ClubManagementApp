@props(['selected' => null])

{{-- Compact chip-style filière picker (kept for backwards compatibility) --}}
@php
$options = ['DEUST', 'LICENSE', 'Cycle Ingénieur', 'Master', 'Doctorat'];
$current = old('filiere', $selected);
@endphp

<div x-data="{ selected: @js($current) }">
    <div class="chip-select">
        @foreach ($options as $opt)
        <label @click="selected = '{{ $opt }}'"
            :class="selected === '{{ $opt }}' && 'is-selected'"
            class="chip-radio">
            <input type="radio" name="filiere" value="{{ $opt }}" class="sr-only"
                :checked="selected === '{{ $opt }}'"
                @if($current === $opt) checked @endif>
            {{ $opt }}
        </label>
        @endforeach
    </div>
</div>
