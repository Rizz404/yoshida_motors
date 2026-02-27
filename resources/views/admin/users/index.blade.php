<x-layouts.admin :title="__('users.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">{{ __('users.heading') }}</h1>
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
        <div class="mb-4 p-4 bg-primary-50 border-l-4 border-primary-500 text-primary-700">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-neutral-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('users.user') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('users.contact') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('users.role') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('users.joined_date') }}</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-neutral-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="shrink-0 h-10 w-10">
                                        @if ($user->profile_photo)
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                                alt="{{ $user->name }}">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-primary-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ $user->name ?? __('users.no_name') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-neutral-900">{{ $user->email }}</div>
                                <div class="text-sm text-neutral-500">{{ $user->phone_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role === 'admin' ? 'bg-primary-100 text-primary-800' : 'bg-neutral-100 text-neutral-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('users.edit', $user) }}"
                                    class="text-primary-600 hover:text-primary-900 font-semibold">{{ __('common.edit') }}</a>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('{{ __('users.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-secondary-600 hover:text-secondary-900 font-semibold">{{ __('common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-neutral-500">
                                {{ __('users.no_users') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.admin>
