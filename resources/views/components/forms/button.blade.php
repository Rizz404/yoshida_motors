@props([
    'type' => 'submit',
    'variant' => 'primary',
    'fullWidth' => false,
    'disabled' => false,
])

@php
    $baseClasses =
        'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

    // Mapping colors from your app.css palette
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
        'secondary' => 'bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500',
        'accent' => 'bg-accent-500 text-white hover:bg-accent-600 focus:ring-accent-400',
        'neutral' => 'bg-neutral-200 text-neutral-800 hover:bg-neutral-300 focus:ring-neutral-400',
        'outline' => 'bg-transparent border-neutral-300 text-neutral-700 hover:bg-neutral-50 focus:ring-primary-500',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ($fullWidth ? ' w-full' : '');
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
