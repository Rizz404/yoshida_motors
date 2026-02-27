<x-layouts.admin :title="__('notifications.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('notifications.heading') }}</h1>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <x-forms.button type="submit" variant="secondary">
                <span class="flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-1" />
                    {{ __('notifications.mark_all_read') }}
                </span>
            </x-forms.button>
        </form>
    </div>

    @if (session('notify'))
        <div
            class="mb-4 p-4 rounded-lg border-l-4
            {{ session('notify.type') === 'success' ? 'bg-success-light border-success text-success-dark' : 'bg-error-light border-error text-error-dark' }}">
            <p class="font-bold">{{ session('notify.title') }}</p>
            <p>{{ session('notify.message') }}</p>
        </div>
    @endif

    <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-background">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('notifications.status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('notifications.title_col') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('notifications.date') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($notifications as $notification)
                        <tr
                            class="hover:bg-hover transition-colors duration-150 {{ !$notification->is_read ? 'bg-primary-container/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (!$notification->is_read)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-container text-primary">
                                        {{ __('notifications.new_badge') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-variant text-text-secondary">
                                        {{ __('notifications.read_badge') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div
                                    class="text-sm font-bold {{ !$notification->is_read ? 'text-text-primary' : 'text-text-secondary' }}">
                                    {{ $notification->title }}
                                </div>
                                <div class="text-sm text-text-secondary truncate max-w-md">
                                    {{ $notification->body }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                {{ $notification->created_at->diffForHumans() }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('notifications.show', $notification) }}"
                                        class="text-primary hover:text-primary/70" title="View Details">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </a>

                                    @if (!$notification->is_read)
                                        <form action="{{ route('notifications.mark-read', $notification) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-success hover:text-success-dark"
                                                title="{{ __('notifications.mark_as_read') }}">
                                                <x-heroicon-o-check class="w-5 h-5" />
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('{{ __('notifications.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error hover:text-error-dark"
                                            title="{{ __('notifications.delete') }}">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-bell-slash class="w-12 h-12 text-text-tertiary mb-3" />
                                    <p class="text-lg font-medium text-text-primary">
                                        {{ __('notifications.no_notifications') }}</p>
                                    <p class="text-sm">{{ __('notifications.all_caught_up') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notifications->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
