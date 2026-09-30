@props(['active'])

@php
$classes = ($active ?? false)
? 'inline-flex items-center px-1 pt-1 border-b-2 border-bordeaux text-sm font-medium leading-5 text-bordeaux focus:outline-none focus:border-bordeaux-light transition duration-150 ease-in-out'
: 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-mauve hover:text-bordeaux hover:border-blush focus:outline-none focus:text-bordeaux focus:border-blush transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>