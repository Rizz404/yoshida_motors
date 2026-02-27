<x-layouts.main :title="__('auth.page_title')">

    <div class="min-h-screen flex items-center justify-center bg-background">
        <div class="max-w-md w-full bg-card rounded-lg shadow-md p-8 border border-border">

            {{-- Locale Switcher --}}
            <div class="flex justify-end mb-4 space-x-1 text-xs">
                <a href="{{ route('locale.switch', 'en') }}"
                    class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-text-on-primary font-semibold' : 'text-text-secondary hover:text-primary' }}">
                    EN
                </a>
                <span class="text-text-disabled self-center">|</span>
                <a href="{{ route('locale.switch', 'ja') }}"
                    class="px-2 py-1 rounded {{ app()->getLocale() === 'ja' ? 'bg-primary text-text-on-primary font-semibold' : 'text-text-secondary hover:text-primary' }}">
                    JA
                </a>
            </div>

            <div class="flex items-center justify-center mb-6">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-primary mr-2" />
                <h2 class="text-2xl font-bold text-text-primary">
                    {{ __('auth.heading') }}
                </h2>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email Input --}}
                <x-forms.input :label="__('auth.email_address')" name="email" type="email" placeholder="admin@example.com" required
                    autofocus />

                {{-- Password Input --}}
                <x-forms.input :label="__('auth.password')" name="password" type="password" placeholder="••••••••" required />

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <x-forms.checkbox name="remember" :label="__('auth.remember_me')" />

                    <a href="#" class="text-sm text-primary hover:text-primary/80 hover:underline">
                        {{ __('auth.forgot_password') }}
                    </a>
                </div>

                {{-- Button Component --}}
                <x-forms.button class="mt-2" fullWidth>
                    {{ __('auth.login_button') }}
                </x-forms.button>

            </form>
        </div>
    </div>

</x-layouts.main>
