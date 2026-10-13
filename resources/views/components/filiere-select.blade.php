@props([
'name' => 'filiere',
'label' => 'Filière',
'selected' => null,
])

@php
$options = [
['DEUST', "2-year foundational diploma — broad first steps.", '🌱'],
['LICENSE', "3-year bachelor's degree — core specialization.", '🎓'],
['Cycle Ingénieur', "5-year engineering cycle — advanced track.", '⚙️'],
['Master', "Postgraduate master's — research and expertise.", '🔬'],
['Doctorat', "Doctoral studies — original research.", '📚'],
];
$current = old($name, $selected);
@endphp

<div x-data="{ selected: @js($current) }" class="stack--sm">
    <label class="field-caption">{{ $label }}</label>

    <div class="select-grid">
        @foreach ($options as [$val, $desc, $icon])
        <label @click="selected = '{{ $val }}'"
            :class="selected === '{{ $val }}' && 'is-selected'"
            class="select-card">
            <input type="radio" name="{{ $name }}" value="{{ $val }}" class="sr-only"
                :checked="selected === '{{ $val }}'"
                @if(($current ?? '') === $val) checked @endif
                @if($loop->first && $attributes->has('required')) required @endif>
            <span class="select-card-icon">{{ $icon }}</span>
            <span class="flex-1 min-w-0">
                <span class="select-card-title block">{{ $val }}</span>
                <span class="select-card-desc block">{{ $desc }}</span>
            </span>
            <span class="select-card-check">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </span>
        </label>
        @endforeach
    </div>
</div>
