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
        'rounded border-border text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition duration-150 ease-in-out cursor-pointer';

    // Checkbox error styling
    $stateClasses = $resolvedError ? 'border-error text-error focus:ring-error/20' : '';
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
                    class="font-medium text-text-primary {{ $disabled ? 'opacity-50' : '' }}">
                    {{ $label }}
                </label>
                @if ($hint)
                    <p class="text-text-secondary font-normal mt-0.5">{{ $hint }}</p>
                @endif
            </div>
        @endif
    </div>

    @if ($resolvedError)
        <p class="mt-1 text-xs text-error font-medium ml-8">
            {{ $resolvedError }}
        </p>
    @endif
</div>
