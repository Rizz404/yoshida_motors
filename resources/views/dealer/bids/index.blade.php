<x-layouts.dealer title="My Bids">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-text-primary">My Bids</h1>
        </div>

        @if (session('notify'))
            <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
                :message="session('notify')['message']" />
        @endif

        @if ($bids->isEmpty())
            <div class="bg-surface p-8 text-center rounded-2xl border border-border shadow-sm">
                <x-heroicon-o-currency-yen class="w-16 h-16 mx-auto text-text-muted mb-4" />
                <h3 class="text-lg font-medium text-text-primary">No bids yet</h3>
                <p class="text-text-secondary mt-2">You haven't placed any bids yet.</p>
                <a href="{{ route('dealer.marketplace.index') }}"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium text-sm">
                    Browse Auction Pool
                </a>
            </div>
        @else
            <div class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-background">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    My Bid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    Auction Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    Bid Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                                    Result</th>
                            </tr>
                        </thead>
                        <tbody class="bg-surface divide-y divide-border">
                            @foreach ($bids as $bid)
                                @php
                                    $auction = $bid->auction;
                                    $appraisal = $auction->appraisalRequest;
                                    $isWon = $auction->winning_bid_id === $bid->id;
                                    $isLost = $auction->status === 'awarded' && !$isWon;
                                    $isOpen = $auction->isOpen();
                                @endphp
                                <tr class="hover:bg-hover transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-text-primary">
                                            {{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }}
                                        </div>
                                        <div class="text-sm text-text-secondary">{{ $appraisal->year_manufacture }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-text-primary">
                                        ¥{{ number_format($bid->amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $auctionStatusClasses = $isOpen
                                                ? 'bg-success-light text-success-dark'
                                                : ($auction->status === 'awarded'
                                                    ? 'bg-primary-container text-primary'
                                                    : 'bg-surface-variant text-text-secondary');
                                            $auctionStatusLabel = $isOpen
                                                ? 'Open'
                                                : ($auction->isExpired()
                                                    ? 'Expired'
                                                    : ucfirst($auction->status));
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $auctionStatusClasses }}">
                                            {{ $auctionStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                        {{ $bid->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @if ($isWon)
                                            <span class="px-2.5 py-1 rounded-full bg-success-light text-success-dark font-semibold text-xs">
                                                Won 🎉
                                            </span>
                                        @elseif ($isLost)
                                            <span class="px-2.5 py-1 rounded-full bg-surface-variant text-text-secondary font-semibold text-xs">
                                                Lost
                                            </span>
                                        @elseif ($isOpen)
                                            <span class="px-2.5 py-1 rounded-full bg-warning-light text-warning-dark font-semibold text-xs">
                                                Pending
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-surface-variant text-text-secondary font-semibold text-xs">
                                                Awaiting Decision
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-border">
                    {{ $bids->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.dealer>
