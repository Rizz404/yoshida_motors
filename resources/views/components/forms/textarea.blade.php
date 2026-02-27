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
        'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out placeholder-text-tertiary';

    $stateClasses = $resolvedError
        ? 'border-error text-error-dark focus:border-error focus:ring-error/20'
        : 'border-border text-text-primary focus:border-primary focus:ring-primary/20 hover:border-primary';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-disabled disabled:cursor-not-allowed';

    $classes = "$baseClasses $stateClasses $disabledClasses bg-surface p-3";
@endphp

<div class="{{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-text-primary mb-1.5 ml-0.5">
            {{ $label }}
        </label>
    @endif

    <textarea {{ $disabled ? 'disabled' : '' }} name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}"
        {!! $attributes->except(['class', 'value']) !!} class="{{ $classes }}">{{ $resolvedValue }}</textarea>

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
