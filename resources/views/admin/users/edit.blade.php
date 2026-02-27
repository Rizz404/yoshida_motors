<x-layouts.admin :title="__('users.edit_title')">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">{{ __('users.edit_heading', ['name' => $user->name]) }}</h1>
            <a href="{{ route('users.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 1. Name (Dengan Value Lama) --}}
                <x-forms.input name="name" :label="__('users.full_name')" :value="$user->name" required />

                {{-- 2. Email & Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-forms.input type="email" name="email" :label="__('users.email_address')" :value="$user->email" required />
                    <x-forms.input type="text" name="phone_number" :label="__('users.phone_number')" :value="$user->phone_number" />
                </div>

                {{-- 3. Role (Logic old + database value) --}}
                <div>
                    <x-forms.select name="role" :label="__('users.role')">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                        </option>
                    </x-forms.select>
                </div>

                {{-- 4. Change Password (Optional) --}}
                <div class="p-4 bg-neutral-50 rounded-md border border-neutral-200">
                    <h3 class="text-sm font-medium text-neutral-900 mb-4">{{ __('users.change_password') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input type="password" name="password" :label="__('users.new_password')" :hint="__('users.keep_password_hint')" />
                        <x-forms.input type="password" name="password_confirmation" :label="__('users.confirm_new_password')" />
                    </div>
                </div>

                {{-- 5. Address (Pass value lewat props) --}}
                <div>
                    <x-forms.textarea name="address" :label="__('users.address')" rows="3" :value="$user->address" />
                </div>

                {{-- 6. Profile Photo --}}
                <div>
                    <label for="profile_photo" class="block text-sm font-medium text-neutral-700 mb-2">
                        {{ __('users.profile_photo') }}
                    </label>

                    @if ($user->profile_photo)
                        <div class="mb-3 flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Current profile photo"
                                class="w-20 h-20 rounded-full object-cover border-2 border-neutral-200">
                            <span class="text-sm text-neutral-600">{{ __('users.current_photo') }}</span>
                        </div>
                    @endif

                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                        class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                    @error('profile_photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-500">{{ __('users.keep_photo_hint') }}</p>
                </div>

                <div class="flex justify-end pt-4">
                    <x-forms.button type="submit" variant="primary">
                        {{ __('users.update_user') }}
                    </x-forms.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
