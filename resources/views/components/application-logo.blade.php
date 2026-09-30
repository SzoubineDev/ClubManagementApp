@props(['class' => ''])

<img
    src="{{ asset('images/logo.jpg') }}"
    alt="{{ config('app.name', 'The Great Debaters') }}"
    {{ $attributes->merge(['class' => $class]) }}>