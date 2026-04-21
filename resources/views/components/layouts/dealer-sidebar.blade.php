<aside class="flex flex-col w-64 h-full bg-[#1a365d] text-white transition-all duration-300">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 bg-[#0f172a] border-b border-[#1e293b] shrink-0">
        <a href="{{ route('dealer.marketplace.index') }}" class="text-xl font-bold tracking-wider uppercase">
            Yoshida<span class="text-[#38bdf8]">Motors</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

        @php
            $currentRoute = Route::currentRouteName();

            $navItems = [
                [
                    'label' => 'Marketplace',
                    'route' => route('dealer.marketplace.index'),
                    'active' => request()->routeIs('dealer.marketplace.*'),
                    'icon' => 'shopping-cart',
                ],
                [
                    'label' => 'My Bids',
                    'route' => route('dealer.bids.index'),
                    'active' => request()->routeIs('dealer.bids.*'),
                    'icon' => 'currency-yen',
                ],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ $item['route'] }}"
                class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-lg group
               {{ $item['active'] ? 'bg-[#1e40af] text-white' : 'text-[#e0e7ff]/80 hover:bg-[#1e40af] hover:text-white' }}">

                {{-- Dynamic Heroicon Rendering --}}
                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                    class="w-5 h-5 mr-3 transition-colors duration-200 {{ $item['active'] ? 'text-[#38bdf8]' : 'text-[#94a3b8] group-hover:text-white' }}" />

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
