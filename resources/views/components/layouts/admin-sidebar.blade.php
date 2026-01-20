<aside class="flex flex-col w-64 h-full bg-primary-900 text-white transition-all duration-300">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 bg-primary-950 border-b border-primary-800 shrink-0">
        <h1 class="text-xl font-bold tracking-wider uppercase">
            Yoshida<span class="text-primary-400">Motors</span>
        </h1>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

        @php
            // Disini nih kuncinya! Kita cek route aktif biar menu-nya nyala (active state) otomatis
            // Kalau route sekarang diawali 'users.', berarti kita lagi di menu User Management
            $currentRoute = Route::currentRouteName();

            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => route('dashboard'), // Pastiin route dashboard kamu namanya 'dashboard' ya!
                    'active' => request()->routeIs('dashboard'),
                    'icon' =>
                        'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                ],
                [
                    'label' => 'Vehicle Listings',
                    'route' => '#', // Nanti diisi kalau fitur Vehicle udah jadi
                    'active' => false,
                    'icon' =>
                        'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                ],
                [
                    'label' => 'Appraisals',
                    'route' => '#', // Nanti diisi kalau fitur Appraisal udah jadi
                    'active' => false,
                    'icon' =>
                        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                ],
                [
                    'label' => 'User Management',
                    'route' => route('users.index'), // <--- INI DIA UPDATENYA! ✨
                    'active' => request()->routeIs('users.*'), // Biar tetep nyala pas lagi Create atau Edit user
                    'icon' =>
                        'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                ],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-lg group
               {{ $item['active'] ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 transition-colors duration-200 {{ $item['active'] ? 'text-white' : 'text-primary-300 group-hover:text-white' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <!-- Footer / Logout -->
    <div class="p-4 border-t border-primary-800 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-300 transition-colors duration-200 rounded-lg hover:bg-red-900/50 hover:text-red-200">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
