<x-layouts.admin :title="__('appraisals.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('appraisals.heading') }}</h1>
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
                            {{ __('appraisals.photo') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('appraisals.vehicle_info') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('appraisals.owner') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('common.status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('appraisals.est_price') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($requests as $request)
                        <tr class="hover:bg-hover transition-colors duration-150">
                            @php $firstPhoto = $request->photos->first(); @endphp

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($firstPhoto)
                                    <img src="{{ asset('storage/' . $firstPhoto->image_path) }}" alt="Request photo"
                                        class="w-16 h-16 rounded-md object-cover border border-border bg-surface-variant"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                    <div
                                        class="hidden w-16 h-16 rounded-md border border-border bg-surface-variant items-center justify-center text-text-tertiary text-[10px] text-center p-1">
                                        {{ __('common.no_image') }}
                                    </div>
                                @else
                                    <div
                                        class="w-16 h-16 rounded-md border border-border bg-surface-variant flex items-center justify-center text-text-tertiary text-[10px] text-center p-1">
                                        {{ __('common.no_image') }}
                                    </div>
                                @endif
                            </td>

                            {{-- Vehicle Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-text-primary">
                                    {{ $request->year_manufacture }} {{ $request->vehicle_brand }}
                                </div>
                                <div class="text-sm text-text-secondary">{{ $request->vehicle_model }}</div>
                            </td>

                            {{-- Owner --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary">
                                    {{ $request->user->name ?? __('common.unknown_user') }}</div>
                                <div class="text-sm text-text-secondary">{{ $request->user->email }}</div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match ($request->status) {
                                        'draft' => 'bg-surface-variant text-text-secondary',
                                        'submitted' => 'bg-primary-container text-primary',
                                        'under_review' => 'bg-warning-light text-warning-dark',
                                        'completed' => 'bg-success-light text-success-dark',
                                        default => 'bg-surface-variant text-text-secondary',
                                    };
                                    $statusLabel = __('appraisals.status_' . str_replace('-', '_', $request->status));
                                @endphp
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Final Price --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                @if ($request->final_price)
                                    <span class="font-mono font-medium">
                                        ¥{{ number_format($request->final_price, 0, '.', ',') }}
                                    </span>
                                @else
                                    <span class="text-text-tertiary italic">{{ __('common.pending') }}</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('appraisals.edit', $request) }}"
                                    class="text-primary hover:text-primary/70 font-semibold transition">{{ __('common.review') }}</a>

                                <form action="{{ route('appraisals.destroy', $request) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('{{ __('appraisals.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-error hover:text-error-dark font-semibold transition">
                                        {{ __('common.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-document-text class="w-12 h-12 text-text-tertiary mb-3" />
                                    <p>{{ __('appraisals.no_requests') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $requests->links() }}
        </div>
    </div>
</x-layouts.admin>
