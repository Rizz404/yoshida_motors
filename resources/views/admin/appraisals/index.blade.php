<x-layouts.admin :title="__('appraisals.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">{{ __('appraisals.heading') }}</h1>
        <a href="{{ route('appraisals.create') }}">
            <x-forms.button variant="primary">
                <span class="flex items-center">
                    <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                    {{ __('appraisals.new_request') }}
                </span>
            </x-forms.button>
        </a>
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
                            {{ __('appraisals.photo') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('appraisals.vehicle_info') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('appraisals.owner') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('common.status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('appraisals.est_price') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-neutral-50 transition-colors duration-150">
                            @php $firstPhoto = $request->photos->first(); @endphp

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($firstPhoto)
                                    <img src="{{ asset('storage/' . $firstPhoto->image_path) }}" alt="Request photo"
                                        class="w-16 h-16 rounded-md object-cover border border-neutral-200 bg-neutral-100"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                    <div
                                        class="hidden w-16 h-16 rounded-md border border-neutral-200 bg-neutral-100 items-center justify-center text-neutral-400 text-[10px] text-center p-1">
                                        {{ __('common.no_image') }}
                                    </div>
                                @else
                                    <div
                                        class="w-16 h-16 rounded-md border border-neutral-200 bg-neutral-100 flex items-center justify-center text-neutral-400 text-[10px] text-center p-1">
                                        {{ __('common.no_image') }}
                                    </div>
                                @endif
                            </td>

                            {{-- Vehicle Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-neutral-900">
                                    {{ $request->year_manufacture }} {{ $request->vehicle_brand }}
                                </div>
                                <div class="text-sm text-neutral-500">{{ $request->vehicle_model }}</div>
                            </td>

                            {{-- Owner --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-neutral-900">
                                    {{ $request->user->name ?? __('common.unknown_user') }}</div>
                                <div class="text-sm text-neutral-500">{{ $request->user->email }}</div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match ($request->status) {
                                        'draft' => 'bg-neutral-100 text-neutral-800',
                                        'submitted' => 'bg-primary-100 text-primary-800',
                                        'under_review' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-neutral-100 text-neutral-800',
                                    };
                                    $statusLabel = __('appraisals.status_' . str_replace('-', '_', $request->status));
                                @endphp
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Final Price --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                @if ($request->final_price)
                                    <span class="font-mono font-medium">
                                        ¥{{ number_format($request->final_price, 0, '.', ',') }}
                                    </span>
                                @else
                                    <span class="text-neutral-400 italic">{{ __('common.pending') }}</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('appraisals.edit', $request) }}"
                                    class="text-primary-600 hover:text-primary-900 font-semibold transition">{{ __('common.review') }}</a>

                                <form action="{{ route('appraisals.destroy', $request) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('{{ __('appraisals.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-secondary-600 hover:text-secondary-900 font-semibold transition">
                                        {{ __('common.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-document-text class="w-12 h-12 text-neutral-300 mb-3" />
                                    <p>{{ __('appraisals.no_requests') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-neutral-200">
            {{ $requests->links() }}
        </div>
    </div>
</x-layouts.admin>
