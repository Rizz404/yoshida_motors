<aside class="flex flex-col w-64 h-full bg-[#0f3d3a] text-white transition-all duration-300">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 bg-[#042f2e] border-b border-[#134e4a] shrink-0">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wider uppercase">
            Yoshida<span class="text-[#2dd4bf]">Motors</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

        @php
            $currentRoute = Route::currentRouteName();

            $navItems = [
                [
                    'label' => __('navigation.dashboard'),
                    'route' => route('dashboard'),
                    'active' => request()->routeIs('dashboard'),
                    'icon' => 'home',
                ],
                [
                    'label' => __('navigation.appraisals'),
                    'route' => route('appraisals.index'),
                    'active' => request()->routeIs('appraisals.*'),
                    'icon' => 'clipboard-document-check',
                ],
                [
                    'label' => __('navigation.notifications'),
                    'route' => route('notifications.index'),
                    'active' => request()->routeIs('notifications.*'),
                    'icon' => 'bell',
                ],
                [
                    'label' => __('navigation.user_management'),
                    'route' => route('users.index'),
                    'active' => request()->routeIs('users.*'),
                    'icon' => 'users',
                ],
                [
                    'label' => __('navigation.auctions'),
                    'route' => route('auctions.index'),
                    'active' => request()->routeIs('auctions.*'),
                    'icon' => 'trophy',
                ],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-lg group
               {{ $item['active'] ? 'bg-[#134e4a] text-white' : 'text-[#f0fdfa]/80 hover:bg-[#134e4a] hover:text-white' }}">

                {{-- Dynamic Heroicon Rendering --}}
                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                    class="w-5 h-5 mr-3 transition-colors duration-200 {{ $item['active'] ? 'text-[#2dd4bf]' : 'text-[#94a3b8] group-hover:text-white' }}" />

                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <!-- Footer / Logout -->
    <div class="p-4 border-t border-[#134e4a] shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center w-full px-4 py-2 text-sm font-medium text-error transition-colors duration-200 rounded-lg hover:bg-error/10 hover:text-error">
                <x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5 mr-3" />
                {{ __('navigation.logout') }}
            </button>
        </form>
    </div>
</aside>
