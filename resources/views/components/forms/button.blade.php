@props([
    'type' => 'submit',
    'variant' => 'primary',
    'fullWidth' => false,
    'disabled' => false,
])

@php
    $baseClasses =
        'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

    $variants = [
        'primary' => 'bg-primary text-text-on-primary hover:bg-primary/90 focus:ring-primary',
        'secondary' => 'bg-error text-text-on-accent hover:bg-error-dark focus:ring-error',
        'accent' => 'bg-accent text-text-on-accent hover:bg-accent-hover focus:ring-accent',
        'neutral' => 'bg-surface-variant text-text-primary hover:bg-border focus:ring-border',
        'outline' => 'bg-transparent border-border text-text-secondary hover:bg-hover focus:ring-primary',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ($fullWidth ? ' w-full' : '');
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
