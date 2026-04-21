<x-layouts.dealer title="Vehicle Marketplace">
    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-text-primary">Vehicle Marketplace</h1>
        </div>

        @if (session('notify'))
            <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
                :message="session('notify')['message']" />
        @endif

        {{-- ============================================================ --}}
        {{-- AUCTION POOL Section                                         --}}
        {{-- ============================================================ --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <x-heroicon-o-trophy class="w-6 h-6 text-warning-dark" />
                <h2 class="text-lg font-semibold text-text-primary">Auction Pool</h2>
                @if ($auctions->total() > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-warning-light text-warning-dark">
                        {{ $auctions->total() }} active
                    </span>
                @endif
            </div>

            @if ($auctions->isEmpty())
                <div class="bg-surface p-6 text-center rounded-2xl border border-dashed border-border">
                    <p class="text-text-secondary text-sm">No active auctions right now. Check back later.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($auctions as $auction)
                        @php
                            $appraisal = $auction->appraisalRequest;
                            $thumb = $appraisal->photos->first();
                            $timeLeft = now()->diffForHumans($auction->end_time, ['parts' => 2]);
                        @endphp
                        <div class="bg-surface rounded-2xl border border-warning/30 shadow-sm overflow-hidden flex flex-col transition-transform hover:-translate-y-1 hover:shadow-md">
                            {{-- Badge --}}
                            <div class="relative">
                                <div class="aspect-video bg-surface-variant">
                                    @if ($thumb)
                                        <img src="{{ Storage::url($thumb->image_path) }}"
                                            alt="{{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-text-muted">
                                            <x-heroicon-o-photo class="w-12 h-12" />
                                        </div>
                                    @endif
                                </div>
                                <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-bold rounded-full bg-warning-light text-warning-dark shadow">
                                    AUCTION
                                </span>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-text-primary mb-1">
                                    {{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }}
                                </h3>
                                <p class="text-sm border-b border-border pb-3 mb-3 text-text-secondary">
                                    Year: {{ $appraisal->year_manufacture }}
                                    @if ($appraisal->mileage)
                                        | {{ number_format($appraisal->mileage) }} km
                                    @endif
                                </p>
                                <div class="flex items-center gap-2 text-xs text-text-secondary mb-3">
                                    <x-heroicon-o-clock class="w-4 h-4 text-warning-dark" />
                                    <span>Ends {{ $timeLeft }}</span>
                                </div>
                                <div class="mt-auto pt-4 flex flex-col gap-2">
                                    <a href="{{ route('dealer.marketplace.show', $appraisal->id) }}"
                                        class="inline-flex justify-center items-center px-4 py-2 bg-warning text-white rounded-lg hover:bg-warning/90 transition-colors font-medium text-sm">
                                        <x-heroicon-o-trophy class="w-4 h-4 mr-1" />
                                        Place Bid
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($auctions->hasPages())
                    <div class="mt-4">{{ $auctions->links() }}</div>
                @endif
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- COMPLETED VEHICLES Section (available for purchase inquiry)  --}}
        {{-- ============================================================ --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <x-heroicon-o-shopping-bag class="w-6 h-6 text-primary" />
                <h2 class="text-lg font-semibold text-text-primary">Available Vehicles</h2>
            </div>

            @if ($vehicles->isEmpty())
                <div class="bg-surface p-6 text-center rounded-2xl border border-dashed border-border">
                    <p class="text-text-secondary text-sm">No vehicles currently available.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($vehicles as $vehicle)
                        <div class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden flex flex-col transition-transform hover:-translate-y-1 hover:shadow-md">
                            <div class="relative aspect-video bg-surface-variant">
                                @if ($vehicle->photos->isNotEmpty())
                                    <img src="{{ Storage::url($vehicle->photos->first()->image_path) }}"
                                        alt="{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-text-muted">
                                        <x-heroicon-o-photo class="w-12 h-12" />
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-text-primary mb-1">
                                    {{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}
                                </h3>
                                <p class="text-sm border-b border-border pb-3 mb-3 text-text-secondary">
                                    Year: {{ $vehicle->year_manufacture }}
                                    @if ($vehicle->mileage)
                                        | {{ number_format($vehicle->mileage) }} km
                                    @endif
                                </p>

                                <div class="mt-auto pt-4 flex flex-col gap-2">
                                    <a href="{{ route('dealer.marketplace.show', $vehicle->id) }}"
                                        class="inline-flex justify-center items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium text-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.dealer>
