@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    /* Fonts */
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';

    /*
     * Primary Colors: Blue for trust and professionalism
     * (Transferred from tailwind.config.js)
     */
    --color-primary-50: #eff6ff;
    --color-primary-100: #dbeafe;
    --color-primary-200: #bfdbfe;
    --color-primary-300: #93c5fd;
    --color-primary-400: #60a5fa;
    --color-primary-500: #3b82f6;
    --color-primary-600: #2563eb;
    --color-primary-700: #1d4ed8;
    --color-primary-800: #1e40af;
    --color-primary-900: #1e3a8a;
    --color-primary-950: #172554;

    /*
     * Secondary Colors: Red for energy and urgency
     */
    --color-secondary-50: #fef2f2;
    --color-secondary-100: #fee2e2;
    --color-secondary-200: #fecaca;
    --color-secondary-300: #fca5a5;
    --color-secondary-400: #f87171;
    --color-secondary-500: #ef4444;
    --color-secondary-600: #dc2626;
    --color-secondary-700: #b91c1c;
    --color-secondary-800: #991b1b;
    --color-secondary-900: #7f1d1d;
    --color-secondary-950: #450a0a;

    /*
     * Accent Colors: Orange for action and vibrancy
     */
    --color-accent-50: #fff7ed;
    --color-accent-100: #ffedd5;
    --color-accent-200: #fed7aa;
    --color-accent-300: #fdba74;
    --color-accent-400: #fb923c;
    --color-accent-500: #f97316;
    --color-accent-600: #ea580c;
    --color-accent-700: #c2410c;
    --color-accent-800: #9a3412;
    --color-accent-900: #7c2d12;
    --color-accent-950: #431407;

    /*
     * Neutral Colors: Gray for backgrounds and text
     */
    --color-neutral-50: #f9fafb;
    --color-neutral-100: #f3f4f6;
    --color-neutral-200: #e5e7eb;
    --color-neutral-300: #d1d5db;
    --color-neutral-400: #9ca3af;
    --color-neutral-500: #6b7280;
    --color-neutral-600: #4b5563;
    --color-neutral-700: #374151;
    --color-neutral-800: #1f2937;
    --color-neutral-900: #111827;
    --color-neutral-950: #030712;

    /* Base Colors */
    --color-background: #ffffff;
    --color-text: #000000;
}
