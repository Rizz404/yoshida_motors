<x-layouts.admin title="Create New User">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">Create New User</h1>
            <a href="{{ route('users.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; Back to List
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- 1. Full Name --}}
                <x-forms.input name="name" label="Full Name" required />

                {{-- 2. Email & Phone (Grid) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-forms.input type="email" name="email" label="Email Address" required />
                    <x-forms.input type="text" name="phone_number" label="Phone Number" placeholder="+62..." />
                </div>

                {{-- 3. Role (Select Component) --}}
                <div>
                    {{-- Note: Kita pakai logic old() di option biar kalau error, pilihan gak reset --}}
                    <x-forms.select name="role" label="Role">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </x-forms.select>
                </div>

                {{-- 4. Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-forms.input type="password" name="password" label="Password" required />
                    <x-forms.input type="password" name="password_confirmation" label="Confirm Password" required />
                </div>

                {{-- 5. Address (Textarea Component) --}}
                <div>
                    <x-forms.textarea name="address" label="Address" rows="3"
                        placeholder="Enter full address..." />
                </div>

                <div class="flex justify-end pt-4">
                    <x-forms.button type="submit" variant="primary">
                        Save User
                    </x-forms.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
