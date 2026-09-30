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

<div class="relative">
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder=" "
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'peer block w-full px-4 pt-6 pb-2 text-bordeaux bg-white border border-blush rounded-xl shadow-sm focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none transition-colors']) }}>

    <label
        for="{{ $id }}"
        class="absolute left-4 top-4 text-mauve text-base transition-all duration-150 ease-out pointer-events-none
               peer-focus:top-2 peer-focus:text-xs peer-focus:text-bordeaux
               peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs
               peer-[:not(:placeholder-shown)]:text-bordeaux">
        {{ $label }}
    </label>
</div>