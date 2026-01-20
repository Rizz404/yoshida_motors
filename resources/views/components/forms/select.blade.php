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
        ? 'border-secondary-300 text-secondary-900 focus:border-secondary-500 focus:ring-secondary-200'
        : 'border-neutral-300 text-neutral-900 focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50 hover:border-primary-400';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-neutral-100 disabled:cursor-not-allowed';

    $classes = "$baseClasses $stateClasses $disabledClasses bg-white py-2.5 px-4";
@endphp

<div class="{{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-0.5">
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
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-secondary-500" />
            </div>
        @endif
    </div>

    @if ($resolvedError)
        <p class="mt-1.5 text-xs text-secondary-600 font-medium flex items-center animate-pulse">
            {{ $resolvedError }}
        </p>
    @elseif($hint)
        <p class="mt-1.5 text-xs text-neutral-500 ml-0.5">
            {{ $hint }}
        </p>
    @endif
</div>
