@props([
'name',
'label',
'type' => 'text',
'value' => null,
'id' => null,
'required' => false,
])

@php
$id = $id ?? $name;
@endphp

<div class="field">
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder=" "
        @if($required) required @endif
        {{ $attributes->whereDoesntStartWith(['class']) }}
        class="field-input {{ $attributes->get('class') }}">
    <label class="field-label" for="{{ $id }}">{{ $label }}</label>
</div>
