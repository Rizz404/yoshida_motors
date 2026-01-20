@props([
    'disabled' => false,
    'label' => null,
    'name',
    'id' => null,
    'rows' => 3,
    'error' => null,
    'hint' => null,
    'value' => '', // Default value
])

@php
    $id = $id ?? $name;
    $resolvedError = $error ?? ($errors->has($name) ? $errors->first($name) : null);

    // Logika Old Value: Input User > Database Value > Kosong
    $resolvedValue = old($name, $value);

    $baseClasses =
        'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out placeholder-neutral-400';

    $stateClasses = $resolvedError
        ? 'border-secondary-300 text-secondary-900 focus:border-secondary-500 focus:ring-secondary-200'
        : 'border-neutral-300 text-neutral-900 focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50 hover:border-primary-400';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-neutral-100 disabled:cursor-not-allowed';

    $classes = "$baseClasses $stateClasses $disabledClasses bg-white p-3";
@endphp

<div class="{{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-0.5">
            {{ $label }}
        </label>
    @endif

    <textarea {{ $disabled ? 'disabled' : '' }} name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}"
        {!! $attributes->except(['class', 'value']) !!} class="{{ $classes }}">{{ $resolvedValue }}</textarea>

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
