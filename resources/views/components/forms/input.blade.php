@props([
    'disabled' => false,
    'label' => null,
    'name',
    'id' => null,
    'type' => 'text',
    'error' => null,
    'hint' => null,
    'value' => '',
])

@php
    $id = $id ?? $name;

    // 1. ERROR LOGIC
    // Priority: Manual Error > Laravel Session Error
    $resolvedError = $error ?? ($errors->has($name) ? $errors->first($name) : null);

    // 2. VALUE LOGIC (OLD INPUT)
    // File inputs cannot have a default value for security reasons
    if ($type !== 'file') {
        // Priority: Old Input > Database Value > Empty
        $resolvedValue = old($name, $value);
    } else {
        $resolvedValue = null;
    }

    // 3. STYLING
    $baseClasses =
        'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out placeholder-text-tertiary';

    // State classes
    $stateClasses = $resolvedError
        ? 'border-error text-error-dark focus:border-error focus:ring-error/20 pr-10'
        : 'border-border text-text-primary focus:border-primary focus:ring-primary/20 hover:border-primary';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-disabled disabled:cursor-not-allowed';

    // Specific for File Input
    if ($type === 'file') {
        $typeClasses =
            'file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-primary hover:file:bg-primary-container/80 cursor-pointer text-text-secondary bg-surface border border-border p-1';
    } else {
        $typeClasses = 'bg-surface py-2.5 px-4';
    }

    $classes = "$baseClasses $stateClasses $disabledClasses $typeClasses";
@endphp

<div class="{{ $attributes->get('class') }}">
    {{-- Label Section --}}
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-text-primary mb-1.5 ml-0.5">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        {{-- Input Field --}}
        <input {{ $disabled ? 'disabled' : '' }} type="{{ $type }}" name="{{ $name }}"
            id="{{ $id }}" @if ($resolvedValue) value="{{ $resolvedValue }}" @endif
            {!! $attributes->except(['class', 'value']) !!} class="{{ $classes }}">

        {{-- Error Icon (Only appears if Error exists & not a file input) --}}
        @if ($resolvedError && $type !== 'file')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-error" />
            </div>
        @endif
    </div>

    {{-- Error Message --}}
    @if ($resolvedError)
        <p class="mt-1.5 text-xs text-error font-medium flex items-center animate-pulse">
            {{ $resolvedError }}
        </p>
    @elseif($hint)
        {{-- Optional Hint Text --}}
        <p class="mt-1.5 text-xs text-text-secondary ml-0.5">
            {{ $hint }}
        </p>
    @endif
</div>
