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

@php
$invalid = $errors->has($name);
@endphp

{{-- Compact native dropdown so the registration form fits the page --}}
<div class="field-flat">
    <label class="field-caption" for="{{ $name }}">{{ $label }}</label>

    <select id="{{ $name }}" name="{{ $name }}" class="field-select"
        @if($attributes->has('required')) required @endif>
        <option value="" disabled @if(!$current) selected @endif>Select your filière…</option>
        @foreach ($options as [$val, $desc, $icon])
        <option value="{{ $val }}" @if(($current ?? '') === $val) selected @endif>{{ $icon }}  {{ $val }} — {{ $desc }}</option>
        @endforeach
    </select>

    @error($name)
    <p class="field-error">{{ $message }}</p>
    @enderror
</div>
