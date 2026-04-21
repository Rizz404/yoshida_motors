<x-layouts.dealer :title="$vehicle->vehicle_brand . ' ' . $vehicle->vehicle_model . ' - Marketplace'">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-text-primary">
                {{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }} ({{ $vehicle->year_manufacture }})
            </h1>
            <a href="{{ route('dealer.marketplace.index') }}" class="btn-secondary">
                <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
                Back to Marketplace
            </a>
        </div>

        @if (session('notify'))
            <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
                :message="session('notify')['message']" />
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Col: Photos --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">Vehicle Gallery</h2>

                    @if ($vehicle->photos->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($vehicle->photos as $photo)
                                <div class="bg-surface-variant rounded-xl overflow-hidden shadow-sm">
                                    <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->category_name }}"
                                        class="w-full h-48 object-cover">
                                    <div class="p-2 text-center text-sm font-medium text-text-secondary bg-surface border-t border-border">
                                        {{ $photo->category_name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-surface-variant rounded-xl border border-dashed border-border">
                            <x-heroicon-o-photo class="w-12 h-12 mx-auto text-text-muted mb-2" />
                            <p class="text-text-secondary">No photos available for this vehicle.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Col: Specs & Bidding --}}
            <div class="space-y-6">
                {{-- Specs --}}
                <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border">
                    <h2 class="text-lg font-semibold text-text-primary mb-4 flex items-center">
                        <x-heroicon-o-information-circle class="w-5 h-5 mr-2 text-primary" />
                        Specifications
                    </h2>

                    <div class="space-y-4">
                        <div class="flex justify-between pb-3 border-b border-border">
                            <span class="text-text-secondary">Brand</span>
                            <span class="font-medium text-text-primary">{{ $vehicle->vehicle_brand }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-border">
                            <span class="text-text-secondary">Model</span>
                            <span class="font-medium text-text-primary">{{ $vehicle->vehicle_model }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-border">
                            <span class="text-text-secondary">Year</span>
                            <span class="font-medium text-text-primary">{{ $vehicle->year_manufacture }}</span>
                        </div>
                        @if ($vehicle->mileage)
                            <div class="flex justify-between pb-3 border-b border-border">
                                <span class="text-text-secondary">Mileage</span>
                                <span class="font-medium text-text-primary">{{ number_format($vehicle->mileage) }} km</span>
                            </div>
                        @endif
                        @if ($vehicle->license_plate)
                            <div class="flex justify-between pb-3 border-b border-border">
                                <span class="text-text-secondary">License Plate</span>
                                <span class="font-medium text-text-primary bg-surface-variant px-2 py-1 rounded text-sm uppercase tracking-wide">
                                    {{ $vehicle->license_plate }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bidding / Action Card --}}
                @if ($auction && $auction->status !== 'awarded')
                    {{-- Auction Active or Expired --}}
                    <div class="bg-warning/5 rounded-2xl p-6 shadow-sm border border-warning/30">
                        <h2 class="text-lg font-semibold text-warning-dark mb-2 flex items-center gap-2">
                            <x-heroicon-o-trophy class="w-5 h-5" />
                            Auction
                        </h2>

                        <div class="space-y-2 text-sm text-text-secondary mb-4">
                            <div class="flex justify-between">
                                <span>Start</span>
                                <span class="text-text-primary font-medium">{{ $auction->start_time->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>End</span>
                                <span class="text-text-primary font-medium {{ $auction->isExpired() ? 'text-error' : '' }}">
                                    {{ $auction->end_time->format('M d, Y H:i') }}
                                </span>
                            </div>
                        </div>

                        @if ($dealerBid)
                            {{-- Dealer has already bid --}}
                            <div class="bg-success-light/40 rounded-xl p-4 text-center">
                                <x-heroicon-o-check-circle class="w-8 h-8 mx-auto text-success-dark mb-2" />
                                <p class="text-sm font-semibold text-success-dark">Bid Submitted</p>
                                <p class="text-lg font-bold font-mono text-text-primary mt-1">
                                    ¥{{ number_format($dealerBid->amount) }}
                                </p>
                                <p class="text-xs text-text-secondary mt-1">Your bid is final and cannot be changed.</p>
                            </div>
                        @elseif ($auction->isOpen())
                            {{-- Bid Form --}}
                            <form action="{{ route('dealer.bids.store', $vehicle->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="amount" class="block text-sm font-medium text-text-primary mb-1">
                                        Your Bid (¥) <span class="text-error">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-sm">¥</span>
                                        <input type="number" name="amount" id="amount"
                                            min="1" step="1000" required
                                            class="w-full rounded-lg border border-border bg-background pl-8 pr-3 py-2 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-warning @error('amount') border-error @enderror"
                                            placeholder="0">
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                                <p class="text-xs text-text-secondary mb-3">
                                    Blind bid — you cannot see others' bids. Your bid is final once submitted.
                                </p>
                                <button type="submit"
                                    class="w-full py-3 px-4 bg-warning text-white rounded-xl font-medium hover:bg-warning/90 transition-colors">
                                    Place Bid
                                </button>
                            </form>
                        @else
                            {{-- Auction expired, dealer never bid --}}
                            <div class="text-center py-4">
                                <p class="text-sm text-text-secondary">This auction has ended.</p>
                            </div>
                        @endif
                    </div>

                @elseif ($auction && $auction->status === 'awarded')
                    {{-- Auction concluded --}}
                    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border text-center">
                        <x-heroicon-o-lock-closed class="w-8 h-8 mx-auto text-text-muted mb-2" />
                        <p class="text-sm font-semibold text-text-primary">Auction Concluded</p>
                        <p class="text-xs text-text-secondary mt-1">A winner has been selected for this vehicle.</p>
                    </div>

                @else
                    {{-- No auction, just completed vehicle --}}
                    <div class="bg-primary/5 rounded-2xl p-6 shadow-sm border border-primary/20">
                        <h2 class="text-lg font-semibold text-primary mb-2">Vehicle Available</h2>
                        <p class="text-sm text-text-secondary">
                            This vehicle has been appraised and is available. Contact Yoshida Motors for purchase details.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.dealer>
