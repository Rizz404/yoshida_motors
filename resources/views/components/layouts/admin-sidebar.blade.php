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
            $currentRoute = Route::currentRouteName();

            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => route('dashboard'),
                    'active' => request()->routeIs('dashboard'),
                    'icon' => 'home', // Uses heroicon-o-home
                ],
                [
                    'label' => 'Vehicle Listings',
                    'route' => '#',
                    'active' => false,
                    'icon' => 'truck', // Uses heroicon-o-truck
                ],
                [
                    'label' => 'Appraisals',
                    'route' => '#',
                    'active' => false,
                    'icon' => 'clipboard-document-check', // Uses heroicon-o-clipboard-document-check
                ],
                [
                    'label' => 'User Management',
                    'route' => route('users.index'),
                    'active' => request()->routeIs('users.*'),
                    'icon' => 'users', // Uses heroicon-o-users
                ],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-lg group
               {{ $item['active'] ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }}">

                {{-- Dynamic Heroicon Rendering --}}
                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                    class="w-5 h-5 mr-3 transition-colors duration-200 {{ $item['active'] ? 'text-white' : 'text-primary-300 group-hover:text-white' }}" />

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
                <x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5 mr-3" />
                Logout
            </button>
        </form>
    </div>
</aside>
