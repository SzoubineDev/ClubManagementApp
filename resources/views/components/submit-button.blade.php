@props(['loadingText' => 'Saving…'])

<button type="submit" {{ $attributes->merge(['class' => 'btn btn-primary']) }}
    x-data="{ loading: false }"
    x-on:click="$el.closest('form')?.addEventListener('submit', () => loading = true, { once: true })">
    <span x-show="loading" x-cloak class="spinner" aria-hidden="true"></span>
    <span x-text="loading ? @js($loadingText) : null" hidden x-show="loading"></span>
    <span :style="loading ? 'display:none' : ''">{{ $slot }}</span>
</button>
