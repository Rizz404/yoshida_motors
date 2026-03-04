@props([
    'src' => null, // Full URL — pass asset('storage/...') before calling
    'alt' => '', // Alt text / aria description
    'shape' => 'rounded', // rounded | circle | square
    'size' => 'md', // xs | sm | md | lg | xl | full
    'height' => 'h-40', // Tailwind height class, only used when size="full"
    'preview' => false, // true → clicking opens full-size modal
    'fallback' => 'icon', // icon | initials
    'initials' => '', // Text to derive the initial letter from (e.g. $user->name)
    'label' => null, // Optional caption shown below the image and inside modal
    'fit' => 'cover', // cover | contain
])

@php
    // ── Size map ──────────────────────────────────────────────────────
    $sizeMap = [
        'xs' => 'w-8 h-8',
        'sm' => 'w-10 h-10',
        'md' => 'w-16 h-16',
        'lg' => 'w-20 h-20',
        'xl' => 'w-24 h-24',
        'full' => 'w-full ' . $height,
    ];

    // ── Shape map ─────────────────────────────────────────────────────
    $shapeMap = [
        'circle' => 'rounded-full',
        'rounded' => 'rounded-lg',
        'square' => 'rounded-none',
    ];

    // ── Initials font size relative to container ──────────────────────
    $textSizeMap = [
        'xs' => 'text-[10px]',
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
        'full' => 'text-sm',
    ];

    $containerSize = $sizeMap[$size] ?? 'w-16 h-16';
    $containerShape = $shapeMap[$shape] ?? 'rounded-lg';
    $textSize = $textSizeMap[$size] ?? 'text-sm';
    $fitClass = $fit === 'contain' ? 'object-contain' : 'object-cover';

    // When size="full" the root wrapper must be block+full-width to fill the grid cell
    $wrapperClass = $size === 'full' ? 'block w-full' : 'inline-block';

    // Preview requires an actual src
    $hasPreview = $preview && $src;

    // Derive the single initial letter shown in the fallback
    $letter = $initials ? strtoupper(substr(trim($initials), 0, 1)) : '?';
@endphp

{{-- ══════════════════════════════════════════════════════════════════
     Root wrapper — carries x-data when preview is enabled so
     the modal state is scoped to this exact image instance.
     Fixed-position modal children are NOT clipped by overflow:hidden.
═══════════════════════════════════════════════════════════════════ --}}
<div @if ($hasPreview) x-data="{ open: false }" @endif {{ $attributes->class([$wrapperClass]) }}>

    {{-- ── Clickable image box ──────────────────────────────────── --}}
    <div class="group relative {{ $containerSize }} {{ $containerShape }} overflow-hidden
                bg-surface-variant border border-border flex-shrink-0
                {{ $hasPreview ? 'cursor-zoom-in' : '' }}"
        @if ($hasPreview) @click="open = true" @endif>
        @if ($src)
            {{-- Real image --}}
            <img src="{{ $src }}" alt="{{ $alt }}" class="w-full h-full {{ $fitClass }}"
                onerror="this.style.display='none';
                         var fb=this.nextElementSibling;
                         fb.style.display='flex';" />

            {{-- Error fallback (hidden by default, shown via JS onerror) --}}
            <div class="absolute inset-0 hidden items-center justify-center bg-primary-container {{ $textSize }}">
                @if ($fallback === 'initials' && $initials)
                    <span class="font-bold text-primary leading-none select-none">{{ $letter }}</span>
                @else
                    <x-heroicon-o-photo class="w-1/2 h-1/2 text-text-tertiary" />
                @endif
            </div>
        @else
            {{-- No src — show fallback immediately --}}
            <div class="absolute inset-0 flex items-center justify-center bg-primary-container {{ $textSize }}">
                @if ($fallback === 'initials' && $initials)
                    <span class="font-bold text-primary leading-none select-none">{{ $letter }}</span>
                @else
                    <x-heroicon-o-photo class="w-1/2 h-1/2 text-text-tertiary" />
                @endif
            </div>
        @endif

        {{-- Hover overlay — zoom icon hint when preview is on --}}
        @if ($hasPreview)
            <div
                class="absolute inset-0 flex items-center justify-center
                        bg-transparent group-hover:bg-black/35
                        transition-colors duration-200 pointer-events-none">
                <x-heroicon-o-magnifying-glass-plus
                    class="w-6 h-6 text-white opacity-0 group-hover:opacity-100
                           transition-opacity duration-200 drop-shadow-md" />
            </div>
        @endif
    </div>

    {{-- ── Optional caption below ────────────────────────────────── --}}
    @if ($label)
        <p class="mt-1 text-xs text-text-secondary text-center truncate max-w-full leading-tight">
            {{ $label }}
        </p>
    @endif

    {{-- ── Full-size preview modal ─────────────────────────────────
         Uses position:fixed so it escapes any overflow:hidden parent.
         z-index 9999 keeps it above admin sidebar (z-50).
    ─────────────────────────────────────────────────────────────── --}}
    @if ($hasPreview)
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="open = false"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4" role="dialog" aria-modal="true"
            :aria-label="'{{ addslashes($label ?? $alt) }}'">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="open = false"></div>

            {{-- Panel --}}
            <div class="relative z-10 flex flex-col items-center max-w-5xl max-h-[90vh]"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                {{-- Close button --}}
                <button @click="open = false"
                    class="absolute -top-3 -right-3 w-8 h-8 bg-surface rounded-full
                           flex items-center justify-center shadow-lg border border-border
                           hover:bg-hover transition-colors z-20"
                    aria-label="Close preview">
                    <x-heroicon-o-x-mark class="w-4 h-4 text-text-primary" />
                </button>

                {{-- Full-size image --}}
                <img src="{{ $src }}" alt="{{ $alt }}"
                    class="max-w-full max-h-[80vh] object-contain rounded-xl shadow-2xl
                           ring-1 ring-white/10" />

                {{-- Caption --}}
                @if ($label)
                    <p class="mt-3 text-white/80 text-sm text-center px-4">{{ $label }}</p>
                @elseif($alt)
                    <p class="mt-3 text-white/50 text-xs text-center px-4">{{ $alt }}</p>
                @endif
            </div>
        </div>
    @endif

</div>
