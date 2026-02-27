@props([
    'disabled' => false,
    'label' => null,
    'name',
    'id' => null,
    'error' => null,
    'hint' => null,
])

@php
    $id = $id ?? $name;
    $resolvedError = $error ?? ($errors->has($name) ? $errors->first($name) : null);

    $baseClasses = 'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out cursor-pointer';

    $stateClasses = $resolvedError
        ? 'border-error text-error-dark focus:border-error focus:ring-error/20'
        : 'border-border text-text-primary focus:border-primary focus:ring-primary/20 hover:border-primary';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-disabled disabled:cursor-not-allowed';

    $classes = "$baseClasses $stateClasses $disabledClasses bg-surface py-2.5 px-4";
@endphp

<div class="{{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-text-primary mb-1.5 ml-0.5">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <select {{ $disabled ? 'disabled' : '' }} name="{{ $name }}" id="{{ $id }}"
            {!! $attributes->except(['class']) !!} class="{{ $classes }}">
            {{ $slot }}
        </select>

        {{-- Error Icon --}}
        @if ($resolvedError)
            <div class="absolute inset-y-0 right-8 pr-3 flex items-center pointer-events-none">
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-error" />
            </div>
        @endif
    </div>

    @if ($resolvedError)
        <p class="mt-1.5 text-xs text-error font-medium flex items-center animate-pulse">
            {{ $resolvedError }}
        </p>
    @elseif($hint)
        <p class="mt-1.5 text-xs text-text-secondary ml-0.5">
            {{ $hint }}
        </p>
    @endif
</div>
