<x-layouts.admin :title="__('dashboard.page_title')">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: Total Vehicles --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                <div class="p-3 bg-neutral-100 rounded-full text-neutral-600">
                    <x-heroicon-o-archive-box class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">{{ __('dashboard.total_vehicles') }}</p>
                    <p class="text-2xl font-bold text-neutral-800">{{ number_format($totalVehicles) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Pending Reviews (Submitted) --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                {{-- Pake Accent (Orange) buat notifikasi pending --}}
                <div class="p-3 bg-accent-100 rounded-full text-accent-600">
                    <x-heroicon-o-clock class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">{{ __('dashboard.pending_reviews') }}</p>
                    <p class="text-2xl font-bold text-neutral-800">{{ number_format($pendingReviews) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Under Review (In Progress) --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                {{-- Pake Primary (Blue) buat progress --}}
                <div class="p-3 bg-primary-100 rounded-full text-primary-600">
                    <x-heroicon-o-document-magnifying-glass class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">{{ __('dashboard.under_review') }}</p>
                    <p class="text-2xl font-bold text-neutral-800">{{ number_format($underReview) }}</p>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Appraised Value --}}
        <div class="p-6 bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="flex items-center">
                {{-- Pake Primary (Blue) lagi karena Green ga ada di palette --}}
                <div class="p-3 bg-primary-100 rounded-full text-primary-600">
                    <x-heroicon-o-currency-yen class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-neutral-500">{{ __('dashboard.total_appraised') }}</p>
                    {{-- Format uang biar cantik --}}
                    <p class="text-2xl font-bold text-neutral-800">¥{{ number_format($totalValue) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-200 flex justify-between items-center bg-neutral-50">
            <h3 class="text-lg font-semibold text-neutral-800">{{ __('dashboard.recent_submissions') }}</h3>
            <a href="{{ route('appraisals.index') }}"
                class="text-sm font-medium text-primary-600 hover:text-primary-800 hover:underline">
                {{ __('dashboard.view_all') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('dashboard.owner') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('dashboard.car_details') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('dashboard.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('dashboard.status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                            {{ __('dashboard.action') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                    @forelse($recentRequests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500 font-bold uppercase">
                                        {{ substr($request->user->name ?? '?', 0, 2) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ $request->user->name ?? __('common.unknown_user') }}</div>
                                        <div class="text-sm text-neutral-500">{{ $request->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-neutral-900">{{ $request->vehicle_brand }}
                                    {{ $request->vehicle_model }}</div>
                                <div class="text-sm text-neutral-500">
                                    {{ __('dashboard.year_label', ['year' => $request->year_manufacture]) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match ($request->status) {
                                        'submitted' => 'bg-accent-100 text-accent-800', // Orange
                                        'under_review' => 'bg-primary-100 text-primary-800', // Blue
                                        'completed'
                                            => 'bg-primary-100 text-primary-800 ring-1 ring-primary-600', // Blue + Ring (biar beda dikit hehe)
                                        'draft' => 'bg-neutral-100 text-neutral-800', // Gray
                                        default => 'bg-neutral-100 text-neutral-800',
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
                                    class="text-primary-600 hover:text-primary-900 mr-3">{{ __('common.review') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-500">
                                <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-neutral-400" />
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
