<x-layouts.dealer :title="$vehicle->brand . ' ' . $vehicle->model . ' - Marketplace'">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-text-primary">
                {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacture_year }})
            </h1>
            <a href="{{ route('dealer.marketplace.index') }}" class="btn-secondary">
                <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
                Back to Marketplace
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Col: Photos --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface rounded-2xl p-6 shadow-sm border border-border">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">Vehicle Gallery</h2>

                    @if ($vehicle->photos->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($vehicle->photos as $photo)
                                <div class="bg-surface-variant rounded-xl overflow-hidden shadow-sm aspect-4/3">
                                    <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->category }}"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="p-2 text-center text-sm font-medium text-text-secondary bg-surface border-t border-border">
                                        {{ $photo->category }}
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

            {{-- Right Col: Specs & Info --}}
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
                            <span class="font-medium text-text-primary">{{ $vehicle->brand }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-border">
                            <span class="text-text-secondary">Model</span>
                            <span class="font-medium text-text-primary">{{ $vehicle->model }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-border">
                            <span class="text-text-secondary">Year</span>
                            <span class="font-medium text-text-primary">{{ $vehicle->manufacture_year }}</span>
                        </div>
                        @if ($vehicle->mileage)
                            <div class="flex justify-between pb-3 border-b border-border">
                                <span class="text-text-secondary">Mileage</span>
                                <span class="font-medium text-text-primary">{{ number_format($vehicle->mileage) }}
                                    km</span>
                            </div>
                        @endif
                        @if ($vehicle->license_plate)
                            <div class="flex justify-between pb-3 border-b border-border">
                                <span class="text-text-secondary">License Plate</span>
                                <span
                                    class="font-medium text-text-primary bg-surface-variant px-2 py-1 rounded text-sm uppercase tracking-wide">{{ $vehicle->license_plate }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Card (For Bidding Later) --}}
                <div class="bg-primary/5 rounded-2xl p-6 shadow-sm border border-primary/20">
                    <h2 class="text-lg font-semibold text-primary mb-2">Purchase/Bidding</h2>
                    <p class="text-sm text-text-secondary mb-4">
                        Bidding functionality for this vehicle will be available soon (Phase 3).
                    </p>
                    <button disabled
                        class="w-full py-3 px-4 bg-surface-variant text-text-muted rounded-xl font-medium cursor-not-allowed">
                        Bidding Coming Soon
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dealer>
