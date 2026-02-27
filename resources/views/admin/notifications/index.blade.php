<x-layouts.admin :title="__('notifications.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">{{ __('notifications.heading') }}</h1>
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
            {{ session('notify.type') === 'success' ? 'bg-primary-50 border-primary-500 text-primary-700' : 'bg-secondary-50 border-secondary-500 text-secondary-700' }}">
            <p class="font-bold">{{ session('notify.title') }}</p>
            <p>{{ session('notify.message') }}</p>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-neutral-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('notifications.status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('notifications.title_col') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('notifications.date') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    @forelse($notifications as $notification)
                        <tr
                            class="hover:bg-neutral-50 transition-colors duration-150 {{ !$notification->is_read ? 'bg-primary-50/30' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (!$notification->is_read)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ __('notifications.new_badge') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                                        {{ __('notifications.read_badge') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div
                                    class="text-sm font-bold {{ !$notification->is_read ? 'text-neutral-900' : 'text-neutral-600' }}">
                                    {{ $notification->title }}
                                </div>
                                <div class="text-sm text-neutral-500 truncate max-w-md">
                                    {{ $notification->body }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('notifications.show', $notification) }}"
                                        class="text-primary-600 hover:text-primary-900" title="View Details">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </a>

                                    @if (!$notification->is_read)
                                        <form action="{{ route('notifications.mark-read', $notification) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900"
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
                                        <button type="submit" class="text-secondary-600 hover:text-secondary-900"
                                            title="{{ __('notifications.delete') }}">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-bell-slash class="w-12 h-12 text-neutral-300 mb-3" />
                                    <p class="text-lg font-medium text-neutral-900">
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
            <div class="px-6 py-4 border-t border-neutral-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
