<x-layouts.main title="Admin Login - Yoshida Motors">

    <div class="min-h-screen flex items-center justify-center bg-neutral-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 border border-neutral-200">

            <div class="flex items-center justify-center mb-6">
                <x-heroicon-o-lock-closed class="w-8 h-8 text-primary-600 mr-2" />
                <h2 class="text-2xl font-bold text-neutral-800">
                    Admin Login
                </h2>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email Input --}}
                <x-forms.input label="Email Address" name="email" type="email" placeholder="admin@example.com"
                    required autofocus />

                {{-- Password Input --}}
                <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" required />

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <x-forms.checkbox name="remember" label="Remember me" />

                    <a href="#" class="text-sm text-primary-600 hover:text-primary-900 hover:underline">
                        Forgot Password?
                    </a>
                </div>

                {{-- Button Component --}}
                <x-forms.button class="mt-2" fullWidth>
                    Login to Dashboard
                </x-forms.button>

            </form>
        </div>
    </div>

</x-layouts.main>
