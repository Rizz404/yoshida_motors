<x-layouts.main :title="$title ?? 'Admin Panel'">

    <div class="flex h-screen bg-neutral-50 overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- ================= MOBILE SIDEBAR (Off-Canvas) ================= --}}
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-neutral-900/80" @click="sidebarOpen = false">
            </div>

            {{-- Sidebar Panel Mobile --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-0 flex">

                {{-- Sidebar Component --}}
                <div class="relative w-full max-w-xs flex-1 bg-primary-900">
                    <x-layouts.admin-sidebar />

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
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <x-layouts.admin-sidebar />
        </div>

        {{-- ================= MAIN CONTENT ================= --}}
        <div class="flex flex-col flex-1 lg:pl-64 w-0 overflow-hidden">

            {{-- Top Header --}}
            <header
                class="flex items-center justify-between h-16 px-6 py-4 bg-white border-b border-neutral-200 shadow-sm shrink-0">
                <div class="flex items-center">
                    {{-- Hamburger Button (Mobile Only) --}}
                    <button @click="sidebarOpen = true"
                        class="text-neutral-500 focus:outline-none lg:hidden hover:text-primary-600 p-2 -ml-2">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>
                    <h2 class="ml-4 text-lg font-semibold text-neutral-800 lg:ml-0">
                        {{ $title ?? 'Dashboard' }}
                    </h2>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative flex items-center text-neutral-600">
                        <span class="mr-2 text-sm font-medium hidden sm:block">Hi, Admin</span>
                        <div
                            class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold border border-primary-200">
                            A
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Scrollable Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-neutral-50 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

</x-layouts.main>
