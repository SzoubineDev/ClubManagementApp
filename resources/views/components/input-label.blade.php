@props(['value'])

<label {{ $attributes->merge(['class' => 'field-caption']) }}>
    {{ $value ?? $slot }}
</label>
