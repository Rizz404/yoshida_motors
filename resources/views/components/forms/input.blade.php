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

    // 1. LOGIKA ERROR
    // Prioritas: Error yang di-pass manual > Error dari Session Laravel (validasi)
    $resolvedError = $error ?? ($errors->has($name) ? $errors->first($name) : null);

    // 2. LOGIKA VALUE (OLD INPUT)
    // Input file tidak boleh punya value default demi keamanan browser
    if ($type !== 'file') {
        // Prioritas: Inputan user barusan (old) > Value dari Database (saat edit) > Kosong
        $resolvedValue = old($name, $value);
    } else {
        $resolvedValue = null;
    }

    // 3. STYLING
    $baseClasses =
        'block w-full text-sm rounded-lg shadow-sm transition duration-200 ease-in-out placeholder-neutral-400';

    // State classes (Kalau error: Merah. Kalau aman: Normal/Biru pas diklik)
    $stateClasses = $resolvedError
        ? 'border-secondary-300 text-secondary-900 focus:border-secondary-500 focus:ring-secondary-200 pr-10' // pr-10 biar text gak nabrak icon error
        : 'border-neutral-300 text-neutral-900 focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50 hover:border-primary-400';

    $disabledClasses = 'disabled:opacity-60 disabled:bg-neutral-100 disabled:cursor-not-allowed';

    // Khusus File Input
    if ($type === 'file') {
        $typeClasses =
            'file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer text-neutral-500 bg-white border border-neutral-300 p-1';
    } else {
        $typeClasses = 'bg-white py-2.5 px-4';
    }

    // Gabungin semua class
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

        {{-- Icon Tanda Seru (Muncul cuma kalau Error & bukan tipe file) --}}
        @if ($resolvedError && $type !== 'file')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-secondary-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Error Message (Teks Merah di Bawah) --}}
    @if ($resolvedError)
        <p class="mt-1.5 text-xs text-secondary-600 font-medium flex items-center animate-pulse">
            {{ $resolvedError }}
        </p>
    @elseif($hint)
        {{-- Optional Hint Text (Abu-abu) --}}
        <p class="mt-1.5 text-xs text-neutral-500 ml-0.5">
            {{ $hint }}
        </p>
    @endif
</div>
