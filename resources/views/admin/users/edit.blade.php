<x-layouts.admin :title="__('users.edit_title')">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-text-primary">{{ __('users.edit_heading', ['name' => $user->name]) }}</h1>
            <a href="{{ route('users.index') }}" class="text-sm text-text-secondary hover:text-primary">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <div class="bg-card shadow-sm rounded-lg border border-border p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 1. Name (Dengan Value Lama) --}}
                <x-forms.input name="name" :label="__('users.full_name')" :value="$user->name" required />

                {{-- 2. Email & Phone (locked depending on auth_provider) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Email: locked for 'email' and 'google' providers --}}
                    @if (in_array($user->auth_provider, ['email', 'google']))
                        <div>
                            <x-forms.input type="email" name="_email_display" :label="__('users.email_address')" :value="$user->email"
                                disabled :hint="__('users.field_locked_email', [
                                    'provider' => ucfirst($user->auth_provider),
                                ])" />
                            <input type="hidden" name="email" value="{{ $user->email }}">
                        </div>
                    @else
                        <x-forms.input type="email" name="email" :label="__('users.email_address')" :value="$user->email" required />
                    @endif

                    {{-- Phone: locked for 'phone' provider --}}
                    @if ($user->auth_provider === 'phone')
                        <div>
                            <x-forms.input type="text" name="_phone_display" :label="__('users.phone_number')" :value="$user->phone_number"
                                disabled :hint="__('users.field_locked_phone', [
                                    'provider' => ucfirst($user->auth_provider),
                                ])" />
                            <input type="hidden" name="phone_number" value="{{ $user->phone_number }}">
                        </div>
                    @else
                        <x-forms.input type="text" name="phone_number" :label="__('users.phone_number')" :value="$user->phone_number" />
                    @endif
                </div>

                {{-- 3. Role & Gender --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-forms.select name="role" :label="__('users.role')">
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User
                            </option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                            </option>
                            <option value="dealer" {{ old('role', $user->role) == 'dealer' ? 'selected' : '' }}>Dealer
                            </option>
                        </x-forms.select>
                    </div>
                    <div>
                        <x-forms.select name="gender" :label="__('users.gender')">
                            <option value="" {{ old('gender', $user->gender) == null ? 'selected' : '' }}>—
                            </option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                {{ __('users.male') }}</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                {{ __('users.female') }}</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                {{ __('users.other') }}</option>
                        </x-forms.select>
                    </div>
                </div>

                {{-- 3b. Birth Date --}}
                <div>
                    <x-forms.input type="date" name="birth_date" :label="__('users.birth_date')" :value="old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '')" />
                </div>

                {{-- 4. Change Password (Optional) --}}
                <div class="p-4 bg-background rounded-md border border-border">
                    <h3 class="text-sm font-medium text-text-primary mb-4">{{ __('users.change_password') }}</h3>
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
                    <label for="profile_photo" class="block text-sm font-medium text-text-primary mb-2">
                        {{ __('users.profile_photo') }}
                    </label>

                    @if ($user->profile_photo)
                        <div class="mb-3 flex items-center space-x-4">
                            <x-ui.image :src="asset('storage/' . $user->profile_photo)" alt="Current profile photo" shape="circle" size="lg"
                                preview fallback="initials" :initials="$user->name ?? 'U'" />
                            <span class="text-sm text-text-secondary">{{ __('users.current_photo') }}</span>
                        </div>
                    @endif

                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                        class="block w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-primary hover:file:bg-primary-container/80" />
                    @error('profile_photo')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-text-secondary">{{ __('users.keep_photo_hint') }}</p>
                </div>

                <div class="flex justify-end pt-4">
                    <x-forms.button type="submit" variant="primary">
                        {{ __('users.update_user') }}
                    </x-forms.button>
                </div>
            </form>

            {{-- System Information (Read-only) --}}
            <div class="mt-6 p-4 bg-background rounded-md border border-border border-dashed">
                <div class="flex items-center gap-2 mb-3">
                    <x-heroicon-o-lock-closed class="w-4 h-4 text-text-secondary" />
                    <h3 class="text-sm font-medium text-text-secondary">{{ __('users.system_information') }}</h3>
                </div>
                <p class="text-xs text-text-secondary mb-4">{{ __('users.system_info_hint') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    {{-- Auth Provider --}}
                    <div>
                        <p class="text-xs font-medium text-text-secondary mb-1">{{ __('users.auth_provider') }}</p>
                        @php
                            $providerColors = [
                                'email' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'phone' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'google' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                            ];
                            $providerColor =
                                $providerColors[$user->auth_provider] ?? 'bg-surface-variant text-text-secondary';
                        @endphp
                        <span
                            class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $providerColor }}">
                            {{ ucfirst($user->auth_provider ?? 'email') }}
                        </span>
                    </div>

                    {{-- Email Verified --}}
                    <div>
                        <p class="text-xs font-medium text-text-secondary mb-1">{{ __('users.email_verified') }}</p>
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 text-sm font-medium">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                {{ __('users.verified') }}
                                <span
                                    class="font-normal text-text-secondary">({{ $user->email_verified_at->format('d M Y') }})</span>
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400 text-sm font-medium">
                                <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                                {{ __('users.not_verified') }}
                            </span>
                        @endif
                    </div>

                    {{-- Firebase UID --}}
                    <div>
                        <p class="text-xs font-medium text-text-secondary mb-1">{{ __('users.firebase_uid') }}</p>
                        @if ($user->firebase_uid)
                            <code
                                class="text-xs bg-surface-variant px-2 py-1 rounded font-mono break-all">{{ $user->firebase_uid }}</code>
                        @else
                            <span class="text-text-secondary text-sm">{{ __('users.not_set') }}</span>
                        @endif
                    </div>

                    {{-- FCM Token --}}
                    <div>
                        <p class="text-xs font-medium text-text-secondary mb-1">{{ __('users.fcm_token') }}</p>
                        @if ($user->fcm_token)
                            <code
                                class="text-xs bg-surface-variant px-2 py-1 rounded font-mono break-all line-clamp-2">{{ Str::limit($user->fcm_token, 60) }}</code>
                        @else
                            <span class="text-text-secondary text-sm">{{ __('users.not_set') }}</span>
                        @endif
                    </div>

                    {{-- Account Created --}}
                    <div>
                        <p class="text-xs font-medium text-text-secondary mb-1">{{ __('users.account_created') }}</p>
                        <p class="text-text-primary">{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
