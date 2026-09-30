@props([
'name' => 'filiere',
'label' => 'Filière',
'value' => null,
])

@php
$options = [
'DEUST' => [
'icon' => '📘',
'desc' => '2-year diploma',
],
'LICENSE' => [
'icon' => '🎓',
'desc' => "Bachelor's degree",
],
'Cycle Ingénieur' => [
'icon' => '⚙️',
'desc' => 'Engineering cycle',
],
'Master' => [
'icon' => '📚',
'desc' => "Master's degree",
],
'Doctorat' => [
'icon' => '🔬',
'desc' => 'PhD program',
],
];
$selected = old($name, $value);
@endphp

<div x-data="{ selected: '{{ $selected }}' }" class="space-y-2">

    <label class="block font-medium text-sm text-bordeaux">{{ $label }}</label>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

        @foreach ($options as $key => $opt)
        <label
            @click="selected = '{{ $key }}'"
            :class="selected === '{{ $key }}'
                    ? 'border-bordeaux bg-blush ring-2 ring-bordeaux/20'
                    : 'border-blush bg-white hover:bg-snow'"
            class="relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-150">
            <input
                type="radio"
                name="{{ $name }}"
                value="{{ $key }}"
                class="sr-only"
                @checked($selected===$key)>

            <span class="text-2xl">{{ $opt['icon'] }}</span>

            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm text-bordeaux">{{ $key }}</p>
                <p class="text-xs text-mauve">{{ $opt['desc'] }}</p>
            </div>

            {{-- Check mark --}}
            <span
                :class="selected === '{{ $key }}' ? 'bg-bordeaux border-bordeaux' : 'border-mauve/40'"
                class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors">
                <svg x-show="selected === '{{ $key }}'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </span>
        </label>
        @endforeach

    </div>
</div>