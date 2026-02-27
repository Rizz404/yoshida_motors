<x-layouts.admin :title="__('users.create_title')">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">{{ __('users.create_heading') }}</h1>
            <a href="{{ route('users.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- 1. Full Name --}}
                <x-forms.input name="name" :label="__('users.full_name')" required />

                {{-- 2. Email & Phone (Grid) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-forms.input type="email" name="email" :label="__('users.email_address')" required />
                    <x-forms.input type="text" name="phone_number" :label="__('users.phone_number')" placeholder="+62..." />
                </div>

                {{-- 3. Role (Select Component) --}}
                <div>
                    <x-forms.select name="role" :label="__('users.role')">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </x-forms.select>
                </div>

                {{-- 4. Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-forms.input type="password" name="password" :label="__('users.password')" required />
                    <x-forms.input type="password" name="password_confirmation" :label="__('users.confirm_password')" required />
                </div>

                {{-- 5. Address (Textarea Component) --}}
                <div>
                    <x-forms.textarea name="address" :label="__('users.address')" rows="3" :placeholder="__('users.address_placeholder')" />
                </div>

                {{-- 6. Profile Photo --}}
                <div>
                    <label for="profile_photo" class="block text-sm font-medium text-neutral-700 mb-2">
                        {{ __('users.profile_photo') }}
                    </label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                        class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                    @error('profile_photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-500">{{ __('users.max_photo_size') }}</p>
                </div>

                <div class="flex justify-end pt-4">
                    <x-forms.button type="submit" variant="primary">
                        {{ __('users.save_user') }}
                    </x-forms.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
