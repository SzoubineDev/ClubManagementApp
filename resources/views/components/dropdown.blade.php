@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

{{-- Generic Alpine dropdown — panel is styled by .menu-panel (mat-menu). --}}
<div class="menu-relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" {{ $attributes->only(['x-bind', 'id']) }}>
    <div @click="open = ! open" aria-haspopup="menu" :aria-expanded="open">
        {{ $trigger }}
    </div>

    <div x-show="open" x-cloak
        x-transition:enter="animate-scale-in"
        class="menu-panel"
        role="menu"
        @click="open = false">
        {{ $content }}
    </div>
</div>
