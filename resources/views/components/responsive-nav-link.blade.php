@props(['active' => false])

<a {{ $attributes->merge(['class' => 'mobile-nav-link' . ($active ? ' is-active' : '')]) }}>
    {{ $slot }}
</a>
