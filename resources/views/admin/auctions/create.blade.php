<x-layouts.admin :title="__('auctions.create_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('auctions.create_heading') }}</h1>
        <a href="{{ route('auctions.index') }}" class="btn-secondary">
            <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
            {{ __('common.back_to_list') }}
        </a>
    </div>

    @if (session('notify'))
        <x-ui.notification :type="session('notify')['type']" :title="session('notify')['title']"
            :message="session('notify')['message']" :details="session('notify')['details'] ?? null" />
    @endif

    <div class="bg-card rounded-xl shadow-sm border border-border p-6 max-w-2xl">
        <form action="{{ route('auctions.store') }}" method="POST">
            @csrf

            {{-- Vehicle Selection --}}
            <div class="mb-5">
                <label for="appraisal_request_id" class="block text-sm font-medium text-text-primary mb-1">
                    {{ __('auctions.select_vehicle') }} <span class="text-error">*</span>
                </label>
                <select name="appraisal_request_id" id="appraisal_request_id"
                    class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary @error('appraisal_request_id') border-error @enderror">
                    <option value="">-- {{ __('auctions.select_vehicle_placeholder') }} --</option>
                    @foreach ($appraisals as $appraisal)
                        <option value="{{ $appraisal->id }}" {{ old('appraisal_request_id') == $appraisal->id ? 'selected' : '' }}>
                            #{{ $appraisal->id }} — {{ $appraisal->year_manufacture }} {{ $appraisal->vehicle_brand }} {{ $appraisal->vehicle_model }}
                        </option>
                    @endforeach
                </select>
                @error('appraisal_request_id')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
                @if ($appraisals->isEmpty())
                    <p class="mt-1 text-xs text-text-secondary">{{ __('auctions.no_eligible_vehicles') }}</p>
                @endif
            </div>

            {{-- Auction Start Time --}}
            <div class="mb-5">
                <label for="start_time" class="block text-sm font-medium text-text-primary mb-1">
                    {{ __('auctions.start_time') }} <span class="text-error">*</span>
                </label>
                <input type="datetime-local" name="start_time" id="start_time"
                    value="{{ old('start_time') }}"
                    class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary @error('start_time') border-error @enderror">
                @error('start_time')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Auction End Time --}}
            <div class="mb-5">
                <label for="end_time" class="block text-sm font-medium text-text-primary mb-1">
                    {{ __('auctions.end_time') }} <span class="text-error">*</span>
                </label>
                <input type="datetime-local" name="end_time" id="end_time"
                    value="{{ old('end_time') }}"
                    class="w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary @error('end_time') border-error @enderror">
                @error('end_time')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reserve Price (Admin Only) --}}
            <div class="mb-6">
                <label for="reserve_price" class="block text-sm font-medium text-text-primary mb-1">
                    {{ __('auctions.reserve_price') }}
                    <span class="text-xs text-text-secondary font-normal">({{ __('auctions.reserve_price_hint') }})</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-sm">¥</span>
                    <input type="number" name="reserve_price" id="reserve_price"
                        value="{{ old('reserve_price') }}"
                        min="0" step="1000"
                        class="w-full rounded-lg border border-border bg-surface pl-8 pr-3 py-2 text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary @error('reserve_price') border-error @enderror"
                        placeholder="{{ __('auctions.reserve_price_placeholder') }}">
                </div>
                @error('reserve_price')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <x-forms.button type="submit" variant="primary">
                    {{ __('auctions.publish_button') }}
                </x-forms.button>
                <a href="{{ route('auctions.index') }}" class="text-sm text-text-secondary hover:text-text-primary transition">
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-layouts.admin>
