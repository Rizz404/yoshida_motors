<x-layouts.dealer title="Vehicle Marketplace">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-text-primary">Vehicle Marketplace</h1>
        </div>

        @if (session('notify'))
            <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']" :message="session('notify')['message']" />
        @endif

        @if ($vehicles->isEmpty())
            <div class="bg-surface p-8 text-center rounded-2xl border border-border shadow-sm">
                <x-heroicon-o-shopping-bag class="w-16 h-16 mx-auto text-text-muted mb-4" />
                <h3 class="text-lg font-medium text-text-primary">No vehicles available</h3>
                <p class="text-text-secondary mt-2">There are currently no vehicles ready for purchase in the
                    marketplace.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($vehicles as $vehicle)
                    <div
                        class="bg-surface rounded-2xl border border-border shadow-sm overflow-hidden flex flex-col transition-transform hover:-translate-y-1 hover:shadow-md">
                        {{-- Vehicle Main Photo --}}
                        <div class="relative aspect-video bg-surface-variant">
                            @if ($vehicle->photos->isNotEmpty())
                                <img src="{{ Storage::url($vehicle->photos->first()->file_path) }}"
                                    alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-text-muted">
                                    <x-heroicon-o-photo class="w-12 h-12" />
                                </div>
                            @endif
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-text-primary mb-1">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </h3>
                            <p class="text-sm border-b border-border pb-3 mb-3 text-text-secondary">
                                Year: {{ $vehicle->manufacture_year }}
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
</x-layouts.dealer>
