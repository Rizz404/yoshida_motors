<x-layouts.main :title="$title ?? 'Dealer Panel'">

    <div class="flex h-screen bg-background overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- ================= MOBILE SIDEBAR (Off-Canvas) ================= --}}
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-overlay" @click="sidebarOpen = false">
            </div>

            {{-- Sidebar Panel Mobile --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-0 flex">

                {{-- Sidebar Component --}}
                <div class="relative w-full max-w-xs flex-1 bg-[#1a365d]">
                    <x-layouts.dealer-sidebar />

                    {{-- Close Button --}}
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button"
                            class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            @click="sidebarOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <x-heroicon-o-x-mark class="h-6 w-6 text-white" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= DESKTOP SIDEBAR (Static) ================= --}}
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 text-white bg-[#1a365d]">
            <x-layouts.dealer-sidebar />
        </div>

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="flex flex-col flex-1 lg:pl-64 w-0 overflow-hidden">

            {{-- Top Header --}}
            <header
                class="flex items-center justify-between h-16 px-6 py-4 bg-surface border-b border-border shadow-sm shrink-0">
                <div class="flex items-center">
                    {{-- Hamburger Button (Mobile Only) --}}
                    <button @click="sidebarOpen = true"
                        class="text-text-secondary focus:outline-none lg:hidden hover:text-primary p-2 -ml-2">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                    <h2 class="ml-4 text-lg font-semibold text-text-primary lg:ml-0">
                        {{ $title ?? 'Dashboard' }}
                    </h2>
                </div>

                <div class="flex items-center space-x-4">
                    {{-- Locale Switcher: swap segment[0] to target locale --}}
                    <div class="flex items-center space-x-1 text-xs">
                        @php
                            $segments = request()->segments();
                            $enUrl = url(collect($segments)->put(0, 'en')->implode('/'));
                            $jaUrl = url(collect($segments)->put(0, 'ja')->implode('/'));
                        @endphp
                        <a href="{{ $enUrl }}"
                            class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-text-on-primary font-semibold' : 'text-text-secondary hover:text-primary' }}">
                            EN
                        </a>
                        <span class="text-text-disabled">|</span>
                        <a href="{{ $jaUrl }}"
                            class="px-2 py-1 rounded {{ app()->getLocale() === 'ja' ? 'bg-primary text-text-on-primary font-semibold' : 'text-text-secondary hover:text-primary' }}">
                            JA
                        </a>
                    </div>

                    <div class="relative flex items-center text-text-secondary">
                        <span class="mr-2 text-sm font-medium hidden sm:block">{{ __('navigation.hi_admin') }}</span>
                        <div
                            class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-primary font-bold border border-primary/20">
                            A
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Scrollable Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-background p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

</x-layouts.main>
