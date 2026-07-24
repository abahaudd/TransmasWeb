@props([
    'variant' => 'primary',
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'ui-btn ui-btn-' . $variant]) }}>
    {{ $slot }}
</button>
