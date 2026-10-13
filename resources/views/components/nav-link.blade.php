@props(['active' => false])

<a {{ $attributes->merge(['class' => 'nav-tab' . ($active ? ' is-active' : '')]) }}>
    {{ $slot }}
</a>
