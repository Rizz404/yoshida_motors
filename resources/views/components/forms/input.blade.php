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
        'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out placeholder-neutral-400';

    // State classes
    $stateClasses = $resolvedError
        ? 'border-secondary-300 text-secondary-900 focus:border-secondary-500 focus:ring-secondary-200 pr-10' // pr-10 prevents text overlapping the error icon
        : 'border-neutral-300 text-neutral-900 focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50 hover:border-primary-400';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-neutral-100 disabled:cursor-not-allowed';

    // Specific for File Input
    if ($type === 'file') {
        $typeClasses =
            'file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer text-neutral-500 bg-white border border-neutral-300 p-1';
    } else {
        $typeClasses = 'bg-white py-2.5 px-4';
    }

    $classes = "$baseClasses $stateClasses $disabledClasses $typeClasses";
@endphp

<div class="{{ $attributes->get('class') }}">
    {{-- Label Section --}}
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-0.5">
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
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-secondary-500" />
            </div>
        @endif
    </div>

    {{-- Error Message --}}
    @if ($resolvedError)
        <p class="mt-1.5 text-xs text-secondary-600 font-medium flex items-center animate-pulse">
            {{ $resolvedError }}
        </p>
    @elseif($hint)
        {{-- Optional Hint Text --}}
        <p class="mt-1.5 text-xs text-neutral-500 ml-0.5">
            {{ $hint }}
        </p>
    @endif
</div>
