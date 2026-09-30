@props(['label' => 'Save'])

<button type="submit"
    x-data="{ loading: false }"
    @click="loading = true"
    :disabled="loading"
    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-bordeaux text-white text-base font-semibold rounded-lg hover:bg-bordeaux-light disabled:opacity-70 disabled:cursor-not-allowed transition">
    <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <span x-text="loading ? 'Please wait…' : '{{ $label }}'"></span>
</button>