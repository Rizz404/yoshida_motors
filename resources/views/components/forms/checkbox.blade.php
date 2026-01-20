@props([
    'disabled' => false,
    'label' => null, // Label di samping checkbox
    'name',
    'id' => null,
    'value' => '1', // Value yang dikirim ke server kalau dicentang
    'checked' => false, // Default state (misal dari database)
    'error' => null,
    'hint' => null,
])

@php
    $id = $id ?? $name;
    $resolvedError = $error ?? ($errors->has($name) ? $errors->first($name) : null);

    // Logika Checked:
    // 1. Cek old input (validasi gagal)
    // 2. Cek properti checked (dari database/default)
    $isChecked = old($name) !== null ? old($name) == $value : $checked;

    $baseClasses =
        'rounded border-neutral-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition duration-150 ease-in-out cursor-pointer';

    // Checkbox error styling (jarang dipake, tapi bagus ada)
    $stateClasses = $resolvedError ? 'border-secondary-300 text-secondary-600 focus:ring-secondary-200' : '';

    $classes = "$baseClasses $stateClasses";
@endphp

<div class="{{ $attributes->get('class') }}">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input type="checkbox" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}"
                {{ $isChecked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} {!! $attributes->except(['class', 'checked', 'value']) !!}
                class="{{ $classes }}">
        </div>

        @if ($label)
            <div class="ml-3 text-sm">
                <label for="{{ $id }}"
                    class="font-medium text-neutral-700 {{ $disabled ? 'opacity-50' : '' }}">
                    {{ $label }}
                </label>
                @if ($hint)
                    <p class="text-neutral-500 font-normal mt-0.5">{{ $hint }}</p>
                @endif
            </div>
        @endif
    </div>

    @if ($resolvedError)
        <p class="mt-1 text-xs text-secondary-600 font-medium ml-8">
            {{ $resolvedError }}
        </p>
    @endif
</div>
