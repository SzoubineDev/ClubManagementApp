@props(['active'])

@php
$classes = ($active ?? false)
? 'block w-full ps-3 pe-4 py-2 border-l-4 border-bordeaux text-start text-base font-medium text-bordeaux bg-blush focus:outline-none focus:text-bordeaux focus:bg-blush focus:border-bordeaux transition duration-150 ease-in-out'
: 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-mauve hover:text-bordeaux hover:bg-snow hover:border-blush focus:outline-none focus:text-bordeaux focus:bg-snow focus:border-blush transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>