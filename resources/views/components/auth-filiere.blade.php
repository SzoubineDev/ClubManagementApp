@props([
'name' => 'filiere',
'label' => 'Filière',
'value' => null,
])

@php
$options = ['DEUST', 'LICENSE', 'Cycle Ingénieur', 'Master', 'Doctorat'];
$selected = old($name, $value);
@endphp

<div x-data="{ selected: '{{ $selected }}' }">
    <label class="block text-xs font-medium text-gray-500 mb-1.5">{{ $label }}</label>

    <div class="flex flex-wrap gap-1.5">
        @foreach ($options as $opt)
        <label
            @click="selected = '{{ $opt }}'"
            :class="selected === '{{ $opt }}'
                    ? 'bg-bordeaux text-white border-bordeaux'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
            class="px-3 py-1.5 text-xs font-medium border rounded-full cursor-pointer transition-colors">
            <input type="radio" name="{{ $name }}" value="{{ $opt }}" class="sr-only" :checked="selected === '{{ $opt }}'">
            {{ $opt }}
        </label>
        @endforeach
    </div>
</div>