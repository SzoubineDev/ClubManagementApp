@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-blush focus:border-bordeaux focus:ring-bordeaux rounded-xl shadow-sm px-4 py-3 text-base']) }}>