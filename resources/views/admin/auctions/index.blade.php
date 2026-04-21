<x-layouts.admin :title="__('auctions.page_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('auctions.heading') }}</h1>
        <a href="{{ route('auctions.create') }}">
            <x-forms.button variant="primary">
                <span class="flex items-center">
                    <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                    {{ __('auctions.publish_vehicle') }}
                </span>
            </x-forms.button>
        </a>
    </div>

    @if (session('notify'))
        <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
            :message="session('notify')['message']" :details="session('notify')['details'] ?? null" />
    @endif

    <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-background">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('auctions.vehicle') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('auctions.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('auctions.bid_window') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('auctions.bid_count') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('auctions.highest_bid') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($auctions as $auction)
                        @php
                            $appraisal = $auction->appraisalRequest;
                            $isOpen = $auction->isOpen();
                            $isExpired = $auction->isExpired();
                            $highestBid = $auction->bids->max('amount');

                            $statusClasses = match ($auction->status) {
                                'open' => $isOpen
                                    ? 'bg-success-light text-success-dark'
                                    : 'bg-warning-light text-warning-dark',
                                'closed' => 'bg-surface-variant text-text-secondary',
                                'awarded' => 'bg-primary-container text-primary',
                                default => 'bg-surface-variant text-text-secondary',
                            };
                            $statusLabel = $isExpired
                                ? __('auctions.status_expired')
                                : __('auctions.status_' . $auction->status);
                        @endphp
                        <tr class="hover:bg-hover transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-text-primary">
                                    {{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }}
                                </div>
                                <div class="text-sm text-text-secondary">{{ $appraisal->year_manufacture }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                <div>{{ $auction->start_time->format('M d, Y H:i') }}</div>
                                <div>→ {{ $auction->end_time->format('M d, Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary font-medium">
                                {{ $auction->bids->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                @if ($highestBid)
                                    <span class="font-mono font-medium">¥{{ number_format($highestBid) }}</span>
                                @else
                                    <span class="text-text-tertiary italic">{{ __('common.pending') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('auctions.show', $auction) }}"
                                    class="text-primary hover:text-primary/70 font-semibold transition">
                                    {{ __('auctions.view_bids') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-trophy class="w-12 h-12 text-text-tertiary mb-3" />
                                    <p>{{ __('auctions.no_auctions') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $auctions->links() }}
        </div>
    </div>
</x-layouts.admin>
