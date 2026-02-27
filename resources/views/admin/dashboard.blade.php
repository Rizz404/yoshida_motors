<x-layouts.admin :title="__('dashboard.page_title')">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: Total Vehicles --}}
        <div class="p-6 bg-card rounded-xl shadow-sm border border-border">
            <div class="flex items-center">
                <div class="p-3 bg-surface-variant rounded-full text-text-secondary">
                    <x-heroicon-o-archive-box class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-text-secondary">{{ __('dashboard.total_vehicles') }}</p>
                    <p class="text-2xl font-bold text-text-primary">{{ number_format($totalVehicles) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Pending Reviews (Submitted) --}}
        <div class="p-6 bg-card rounded-xl shadow-sm border border-border">
            <div class="flex items-center">
                <div class="p-3 bg-warning-light rounded-full text-warning-dark">
                    <x-heroicon-o-clock class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-text-secondary">{{ __('dashboard.pending_reviews') }}</p>
                    <p class="text-2xl font-bold text-text-primary">{{ number_format($pendingReviews) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Under Review (In Progress) --}}
        <div class="p-6 bg-card rounded-xl shadow-sm border border-border">
            <div class="flex items-center">
                <div class="p-3 bg-primary-container rounded-full text-primary">
                    <x-heroicon-o-document-magnifying-glass class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-text-secondary">{{ __('dashboard.under_review') }}</p>
                    <p class="text-2xl font-bold text-text-primary">{{ number_format($underReview) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Appraised Value --}}
        <div class="p-6 bg-card rounded-xl shadow-sm border border-border">
            <div class="flex items-center">
                <div class="p-3 bg-primary-container rounded-full text-primary">
                    <x-heroicon-o-currency-yen class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-text-secondary">{{ __('dashboard.total_appraised') }}</p>
                    <p class="text-2xl font-bold text-text-primary">¥{{ number_format($totalValue) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Table Section --}}
    <div class="bg-card rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-background">
            <h3 class="text-lg font-semibold text-text-primary">{{ __('dashboard.recent_submissions') }}</h3>
            <a href="{{ route('appraisals.index') }}"
                class="text-sm font-medium text-primary hover:text-primary/80 hover:underline">
                {{ __('dashboard.view_all') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-background">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('dashboard.owner') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('dashboard.car_details') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('dashboard.date') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('dashboard.status') }}</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            {{ __('dashboard.action') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    @forelse($recentRequests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="shrink-0 h-10 w-10 rounded-full bg-surface-variant flex items-center justify-center text-text-secondary font-bold uppercase">
                                        {{ substr($request->user->name ?? '?', 0, 2) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-text-primary">
                                            {{ $request->user->name ?? __('common.unknown_user') }}</div>
                                        <div class="text-sm text-text-secondary">{{ $request->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-text-primary">{{ $request->vehicle_brand }}
                                    {{ $request->vehicle_model }}</div>
                                <div class="text-sm text-text-secondary">
                                    {{ __('dashboard.year_label', ['year' => $request->year_manufacture]) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match ($request->status) {
                                        'submitted' => 'bg-warning-light text-warning-dark',
                                        'under_review' => 'bg-primary-container text-primary',
                                        'completed' => 'bg-success-light text-success-dark',
                                        'draft' => 'bg-surface-variant text-text-secondary',
                                        default => 'bg-surface-variant text-text-secondary',
                                    };
                                    $statusLabel = __('appraisals.status_' . str_replace('-', '_', $request->status));
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('appraisals.edit', $request->id) }}"
                                    class="text-primary hover:text-primary/70 mr-3">{{ __('common.review') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-text-secondary">
                                <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-text-tertiary" />
                                <p class="mt-2 text-sm font-medium">{{ __('dashboard.no_requests') }}</p>
                                <p class="text-xs">{{ __('dashboard.waiting_submissions') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.admin>
