<x-layouts.admin :title="__('users.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('users.heading') }}</h1>
        <a href="{{ route('users.create') }}">
            <x-forms.button variant="primary">
                <span class="flex items-center">
                    <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                    {{ __('users.add_new_user') }}
                </span>
            </x-forms.button>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-primary-container border-l-4 border-primary text-primary">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-background">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.user') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.contact') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.details') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.auth_provider') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.role') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('users.joined_date') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($users as $user)
                        <tr class="hover:bg-hover transition-colors duration-150">
                            {{-- User: avatar + name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <x-ui.image :src="$user->profile_photo ? asset('storage/' . $user->profile_photo) : null" :alt="$user->name ?? __('users.no_name')" shape="circle" size="sm"
                                        fallback="initials" :initials="$user->name ?? 'U'" class="shrink-0" />
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text-primary">
                                            {{ $user->name ?? __('users.no_name') }}
                                        </div>
                                        <div class="text-xs text-text-secondary">#{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact: email (+ verified badge) + phone --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-sm text-text-primary">
                                    {{ $user->email ?? '-' }}
                                    @if ($user->email)
                                        @if ($user->email_verified_at)
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                                title="{{ $user->email_verified_at->format('d M Y H:i') }}">
                                                ✓
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                !
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="text-sm text-text-secondary">{{ $user->phone_number ?? '-' }}</div>
                                @if ($user->address)
                                    <div class="text-xs text-text-secondary mt-0.5 max-w-[180px] truncate"
                                        title="{{ $user->address }}">
                                        {{ $user->address }}
                                    </div>
                                @endif
                            </td>

                            {{-- Details: gender + birth date --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary">
                                    @if ($user->gender)
                                        <span class="capitalize">{{ __('users.' . $user->gender) }}</span>
                                    @else
                                        <span class="text-text-secondary">-</span>
                                    @endif
                                </div>
                                <div class="text-xs text-text-secondary">
                                    {{ $user->birth_date ? $user->birth_date->format('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Auth Provider --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $providerColors = [
                                        'email' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'phone' =>
                                            'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                        'google' =>
                                            'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    ];
                                    $providerColor =
                                        $providerColors[$user->auth_provider] ??
                                        'bg-surface-variant text-text-secondary';
                                @endphp
                                <span
                                    class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $providerColor }}">
                                    {{ ucfirst($user->auth_provider ?? 'email') }}
                                </span>
                                @if ($user->firebase_uid)
                                    <div class="text-xs text-text-secondary mt-0.5 max-w-[100px] truncate"
                                        title="{{ $user->firebase_uid }}">
                                        UID: {{ $user->firebase_uid }}
                                    </div>
                                @endif
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-primary-container text-primary' : 'bg-surface-variant text-text-secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            {{-- Joined Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                {{ $user->created_at->format('d M Y') }}
                                <div class="text-xs">{{ $user->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('users.edit', $user) }}"
                                    class="text-primary hover:text-primary/70 font-semibold">{{ __('common.edit') }}</a>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('{{ __('users.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-error hover:text-error-dark font-semibold">{{ __('common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-text-secondary">
                                {{ __('users.no_users') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>
