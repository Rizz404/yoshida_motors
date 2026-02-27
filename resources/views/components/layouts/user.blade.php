<x-layouts.main :title="$title ?? 'Dashboard User'">

    {{-- Navbar Khusus User --}}
    <nav x-data="{ open: false }" class="bg-surface shadow-sm border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="font-bold text-xl text-primary">CarRongsok User</span>
                </div>

                {{-- Tombol Hamburger Menu (Contoh Alpine) --}}
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="p-2 rounded-md text-text-tertiary hover:text-text-secondary hover:bg-surface-variant">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-surface-variant">
            <div class="pt-2 pb-3 space-y-1">
                <a href="#"
                    class="block pl-3 pr-4 py-2 border-l-4 border-primary text-base font-medium text-primary bg-primary-container">Home</a>
                <a href="#"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-text-secondary hover:bg-surface-variant hover:border-border-hover">Riwayat
                    Transaksi</a>
            </div>
        </div>
    </nav>

    {{-- Konten Halaman User Masuk Sini --}}
    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

</x-layouts.main>
