<x-layouts.admin :title="__('auctions.detail_title')">
    @php
        $appraisal = $auction->appraisalRequest;
        $isOpen = $auction->isOpen();
        $isExpired = $auction->isExpired();

        $statusClasses = match ($auction->status) {
            'open' => $isOpen ? 'bg-success-light text-success-dark' : 'bg-warning-light text-warning-dark',
            'closed' => 'bg-surface-variant text-text-secondary',
            'awarded' => 'bg-primary-container text-primary',
            default => 'bg-surface-variant text-text-secondary',
        };
        $statusLabel = $isExpired
            ? __('auctions.status_expired')
            : __('auctions.status_' . $auction->status);
    @endphp

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">
                {{ __('auctions.detail_heading', ['id' => $auction->id]) }}
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                {{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }} ({{ $appraisal->year_manufacture }})
            </p>
        </div>
        <a href="{{ route('auctions.index') }}" class="btn-secondary">
            <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
            {{ __('common.back_to_list') }}
        </a>
    </div>

    @if (session('notify'))
        <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
            :message="session('notify')['message']" :details="session('notify')['details'] ?? null" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Bids List --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Auction Info Card --}}
            <div class="bg-card rounded-xl shadow-sm border border-border p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider">{{ __('auctions.status') }}</p>
                        <span class="mt-1 px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider">{{ __('auctions.bid_count') }}</p>
                        <p class="mt-1 text-lg font-bold text-text-primary">{{ $auction->bids->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider">{{ __('auctions.start_time') }}</p>
                        <p class="mt-1 text-sm text-text-primary">{{ $auction->start_time->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-text-secondary uppercase tracking-wider">{{ __('auctions.end_time') }}</p>
                        <p class="mt-1 text-sm text-text-primary {{ $isExpired ? 'text-error' : '' }}">
                            {{ $auction->end_time->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>

                @if ($auction->reserve_price)
                    <div class="mt-4 pt-4 border-t border-border">
                        <p class="text-xs text-text-secondary uppercase tracking-wider">{{ __('auctions.reserve_price') }}</p>
                        <p class="mt-1 text-sm font-mono font-medium text-text-primary">¥{{ number_format($auction->reserve_price) }}</p>
                    </div>
                @endif
            </div>

            {{-- Bids Table --}}
            <div class="bg-card rounded-xl shadow-sm border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-background">
                    <h3 class="text-base font-semibold text-text-primary">{{ __('auctions.bids_heading') }}</h3>
                </div>

                @if ($auction->bids->isEmpty())
                    <div class="px-6 py-12 text-center text-text-secondary">
                        <x-heroicon-o-inbox class="w-10 h-10 mx-auto text-text-tertiary mb-2" />
                        <p>{{ __('auctions.no_bids') }}</p>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-background">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    {{ __('auctions.dealer') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    {{ __('auctions.bid_amount') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    {{ __('auctions.bid_submitted_at') }}</th>
                                @if ($isExpired || $auction->status === Auction::STATUS_AWARDED)
                                    <th class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        {{ __('common.actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-surface divide-y divide-border">
                            @foreach ($auction->bids->sortByDesc('amount') as $bid)
                                @php $isWinner = $auction->winning_bid_id === $bid->id; @endphp
                                <tr class="{{ $isWinner ? 'bg-success-light/30' : 'hover:bg-hover' }} transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-text-primary">
                                        <span class="font-medium">{{ __('auctions.dealer_id_label', ['id' => $bid->dealer_id]) }}</span>
                                        @if ($isWinner)
                                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-success-light text-success-dark font-semibold">
                                                {{ __('auctions.winner_badge') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-text-primary">
                                        ¥{{ number_format($bid->amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                        {{ $bid->created_at->format('M d, Y H:i') }}
                                    </td>
                                    @if ($isExpired || $auction->status === Auction::STATUS_AWARDED)
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            @if ($auction->status !== Auction::STATUS_AWARDED)
                                                <form action="{{ route('auctions.award', $auction) }}" method="POST"
                                                    onsubmit="return confirm('{{ __('auctions.award_confirm') }}')">
                                                    @csrf
                                                    <input type="hidden" name="bid_id" value="{{ $bid->id }}">
                                                    <button type="submit"
                                                        class="text-primary hover:text-primary/70 font-semibold transition text-xs">
                                                        {{ __('auctions.award_winner_button') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Right: Vehicle Info --}}
        <div class="space-y-6">
            <div class="bg-card rounded-xl shadow-sm border border-border p-6">
                <h3 class="text-base font-semibold text-text-primary mb-4">{{ __('auctions.vehicle_info') }}</h3>

                @php $thumb = $appraisal->photos->first(); @endphp
                @if ($thumb)
                    <div class="rounded-lg overflow-hidden aspect-video bg-surface-variant mb-4">
                        <img src="{{ asset('storage/' . $thumb->image_path) }}" alt="{{ $appraisal->vehicle_brand }}"
                            class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-text-secondary">{{ __('appraisals.brand') }}</span>
                        <span class="font-medium text-text-primary">{{ $appraisal->vehicle_brand }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-secondary">{{ __('appraisals.model') }}</span>
                        <span class="font-medium text-text-primary">{{ $appraisal->vehicle_model }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-secondary">{{ __('appraisals.year') }}</span>
                        <span class="font-medium text-text-primary">{{ $appraisal->year_manufacture }}</span>
                    </div>
                    @if ($appraisal->mileage)
                        <div class="flex justify-between">
                            <span class="text-text-secondary">{{ __('appraisals.mileage_label') }}</span>
                            <span class="font-medium text-text-primary">{{ number_format($appraisal->mileage) }} km</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-border">
                    <a href="{{ route('appraisals.edit', $appraisal) }}"
                        class="text-sm text-primary hover:text-primary/70 font-medium transition">
                        {{ __('auctions.view_appraisal') }} →
                    </a>
                </div>
            </div>

            @if ($highestBid)
                <div class="bg-primary/5 rounded-xl border border-primary/20 p-6">
                    <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">
                        {{ __('auctions.highest_bid') }}
                    </h3>
                    <p class="text-3xl font-bold font-mono text-primary">¥{{ number_format($highestBid->amount) }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
