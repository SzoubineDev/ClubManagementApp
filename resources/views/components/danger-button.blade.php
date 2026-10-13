<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-warn']) }}>
    {{ $slot }}
</button>
