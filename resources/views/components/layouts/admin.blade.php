<x-layouts.main :title="$title ?? 'Admin Panel'">

    <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">

        {{-- Sidebar Admin --}}
        <aside x-show="sidebarOpen" class="w-64 bg-gray-800 text-white flex-shrink-0 transition-all duration-300">
            <div class="p-4 font-bold text-lg border-b border-gray-700">
                Admin Panel 🛠️
            </div>
            <nav class="mt-4 px-2 space-y-2">
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Kelola Mobil</a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Laporan</a>
            </nav>
        </aside>

        {{-- Konten Utama --}}
        <div class="flex-1 flex flex-col bg-gray-100">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="font-semibold text-gray-700">Halo, Admin!</div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

</x-layouts.main>
