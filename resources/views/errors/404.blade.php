<x-layouts.main title="404 — Page Not Found">

    <div class="min-h-screen bg-background flex flex-col items-center justify-center px-6 py-20">

        {{-- Decorative teal ring --}}
        <div class="relative mb-10 flex items-center justify-center">
            <div class="absolute size-48 rounded-full bg-primary/10 blur-2xl"></div>
            <div
                class="relative flex size-36 items-center justify-center rounded-full border-2 border-border bg-surface shadow-sm">
                {{-- Car / search icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="size-16 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 15.75 21 21m-5.25-5.25A7.5 7.5 0 1 0 3 10.5a7.5 7.5 0 0 0 7.5 7.5Z" />
                </svg>
            </div>
        </div>

        {{-- Error code --}}
        <p class="text-8xl font-extrabold tracking-tight text-primary leading-none select-none">404</p>

        {{-- Headline --}}
        <h1 class="mt-4 text-2xl font-semibold text-text-primary text-center">
            Page Not Found
        </h1>

        {{-- Description --}}
        <p class="mt-3 max-w-md text-center text-base text-text-secondary">
            The page you're looking for doesn't exist or has been moved.
            Double-check the URL, or head back to the dashboard.
        </p>

        {{-- Divider --}}
        <div class="mt-8 h-px w-24 bg-divider rounded-full"></div>

        {{-- Actions --}}
        {{-- NOTE: @auth is unreliable here because 404s from unmatched routes bypass
             the session/web middleware. Linking to "/" lets Laravel's redirect chain
             decide: guests go to login, authenticated users bounce to dashboard. --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
            <a href="/"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5
                       text-sm font-semibold text-text-on-primary shadow-sm
                       hover:opacity-90 active:opacity-75 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l9-9 9 9M4.5 10.5V19.5a.75.75 0 0 0 .75.75H9.75V15h4.5v5.25h4.5a.75.75 0 0 0 .75-.75V10.5" />
                </svg>
                Go Home
            </a>

            <button onclick="window.history.back()"
                class="inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-6 py-2.5
                           text-sm font-semibold text-text-secondary
                           hover:border-border-hover hover:bg-hover active:bg-pressed transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Go Back
            </button>
        </div>

        {{-- Branding --}}
        <p class="mt-12 text-xs text-text-tertiary tracking-wide uppercase">
            Yoshida Motors &mdash; Vehicle Appraisal System
        </p>

    </div>

</x-layouts.main>
