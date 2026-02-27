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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10">
                                        @if ($user->profile_photo)
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                                alt="{{ $user->name }}">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-primary-container flex items-center justify-center">
                                                <span class="text-primary font-semibold text-sm">
                                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text-primary">
                                            {{ $user->name ?? __('users.no_name') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary">{{ $user->email }}</div>
                                <div class="text-sm text-text-secondary">{{ $user->phone_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-primary-container text-primary' : 'bg-surface-variant text-text-secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
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
                            <td colspan="5" class="px-6 py-4 text-center text-text-secondary">
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
