<x-layouts.admin :title="__('appraisals.edit_title')">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-text-primary">
                {{ __('appraisals.edit_heading', ['id' => $appraisal->id]) }}
                <span class="text-base font-normal text-text-secondary ml-2">({{ $appraisal->vehicle_brand }}
                    {{ $appraisal->vehicle_model }})</span>
            </h1>
            <a href="{{ route('appraisals.index') }}" class="text-sm text-text-secondary hover:text-primary">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <form action="{{ route('appraisals.update', $appraisal) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN: Main Info --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card: Vehicle Details (Read-Only — submitted by customer) --}}
                    <div class="bg-card shadow-sm rounded-lg border border-border p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-text-primary">
                                {{ __('appraisals.vehicle_details') }}
                            </h3>
                            <span
                                class="inline-flex items-center gap-1 text-xs text-text-secondary bg-surface-variant px-2.5 py-1 rounded-full font-medium">
                                <x-heroicon-o-lock-closed class="w-3.5 h-3.5" />
                                {{ __('common.readonly') }}
                            </span>
                        </div>
                        <p class="text-xs text-text-secondary mb-4">{{ __('appraisals.vehicle_readonly_hint') }}</p>

                        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">{{ __('appraisals.brand') }}
                                </dt>
                                <dd class="text-text-primary font-semibold">{{ $appraisal->vehicle_brand }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">{{ __('appraisals.model') }}
                                </dt>
                                <dd class="text-text-primary font-semibold">{{ $appraisal->vehicle_model }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">{{ __('appraisals.year') }}
                                </dt>
                                <dd class="text-text-primary">{{ $appraisal->year_manufacture }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">
                                    {{ __('appraisals.license_plate_label') }}</dt>
                                <dd class="text-text-primary font-mono">
                                    {{ $appraisal->license_plate ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">
                                    {{ __('appraisals.mileage_label') }}</dt>
                                <dd class="text-text-primary">
                                    @if ($appraisal->mileage)
                                        {{ number_format($appraisal->mileage) }} km
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-secondary mb-0.5">
                                    {{ __('appraisals.description') }}</dt>
                                <dd class="text-text-primary col-span-2 whitespace-pre-wrap">
                                    {{ $appraisal->description ?? '—' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Card: Photos Management --}}
                    <div class="bg-card shadow-sm rounded-lg border border-border p-6">
                        <h3 class="text-lg font-semibold text-text-primary mb-4">{{ __('appraisals.manage_photos') }}
                        </h3>

                        {{-- Existing Photos --}}
                        @if ($appraisal->photos->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                @foreach ($appraisal->photos as $photo)
                                    <div class="relative group border border-border rounded-lg overflow-hidden">
                                        {{-- Image Preview --}}
                                        <img src="{{ asset('storage/' . $photo->image_path) }}"
                                            alt="{{ $photo->category_name }}"
                                            class="w-full h-32 object-cover bg-surface-variant"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />

                                        {{-- Local fallback (no internet needed) --}}
                                        <div
                                            class="hidden w-full h-32 bg-surface-variant items-center justify-center text-text-tertiary text-xs text-center p-2">
                                            {{ __('common.image_not_available') }}
                                        </div>

                                        {{-- Label --}}
                                        <div
                                            class="p-2 bg-surface text-xs font-bold text-center text-text-primary truncate border-t border-border">
                                            {{ $photo->category_name }}
                                        </div>

                                        {{-- Delete Overlay --}}
                                        <div class="absolute top-2 right-2">
                                            <label
                                                class="flex items-center space-x-1 bg-surface/90 px-2 py-1 rounded shadow-sm border border-error/30 cursor-pointer hover:bg-error-light">
                                                <input type="checkbox" name="delete_photos[]"
                                                    value="{{ $photo->id }}"
                                                    class="text-error rounded focus:ring-error">
                                                <span
                                                    class="text-xs text-error font-semibold">{{ __('common.delete_photo') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-text-secondary mb-6">{{ __('appraisals.delete_photos_hint') }}</p>
                        @else
                            <div class="p-4 bg-background text-text-secondary text-sm text-center rounded mb-6">
                                {{ __('appraisals.no_photos') }}
                            </div>
                        @endif

                        {{-- Upload New Photos --}}
                        <div class="border-t border-border pt-4" x-data="{
                            photos: [],
                            handleFiles(event) {
                                const files = Array.from(event.target.files);
                                if (files.length > 7) {
                                    alert('{{ __('appraisals.max_photos_alert') }}');
                                    event.target.value = '';
                                    this.photos = [];
                                    return;
                                }
                                this.photos = files.map(f => ({
                                    name: f.name,
                                    preview: URL.createObjectURL(f)
                                }));
                            }
                        }">
                            <h4 class="text-sm font-bold text-text-primary mb-3">{{ __('appraisals.add_new_photos') }}
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-text-primary mb-1">{{ __('appraisals.select_photos') }}</label>
                                    <input type="file" name="new_photos[]" accept="image/jpeg,image/png,image/jpg"
                                        multiple @change="handleFiles"
                                        class="block w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-primary hover:file:bg-primary-container/80 cursor-pointer border border-border rounded-lg p-2 bg-surface" />
                                    <p class="text-xs text-text-tertiary mt-1">{{ __('appraisals.max_photos_each') }}
                                    </p>
                                </div>

                                {{-- New Photo Preview Grid --}}
                                <div x-show="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div
                                            class="flex flex-col gap-2 p-3 bg-background border border-border rounded-lg">
                                            <img :src="photo.preview" :alt="photo.name"
                                                class="w-full h-28 object-cover rounded bg-surface-variant" />
                                            <input type="text" name="new_photo_labels[]"
                                                :placeholder="'{{ __('appraisals.label_placeholder_engine') }}'"
                                                class="w-full text-sm border border-border rounded px-2 py-1.5 bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" />
                                            <span x-text="photo.name" class="text-xs text-text-tertiary truncate"
                                                :title="photo.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Admin Actions & Status --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Card: Appraisal Result --}}
                    <div class="bg-primary-container/40 shadow-sm rounded-lg border border-primary/20 p-6">
                        <h3 class="text-lg font-bold text-primary mb-4">{{ __('appraisals.appraisal_result') }}
                        </h3>

                        <div class="space-y-4">
                            {{-- Status --}}
                            <div>
                                <x-forms.select name="status" :label="__('appraisals.current_status')">
                                    @foreach (['draft', 'submitted', 'under_review', 'completed', 'rejected'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $appraisal->status === $status ? 'selected' : '' }}>
                                            {{ __('appraisals.status_' . $status) }}
                                        </option>
                                    @endforeach
                                </x-forms.select>
                            </div>

                            {{-- Final Price --}}
                            <div>
                                <x-forms.input type="number" name="final_price" :label="__('appraisals.final_price')" :value="$appraisal->final_price"
                                    placeholder="0" />
                            </div>

                            {{-- Valid Until --}}
                            <div>
                                <x-forms.input type="date" name="price_valid_until" :label="__('appraisals.price_valid_until')"
                                    :value="$appraisal->price_valid_until?->format('Y-m-d')" />
                            </div>

                            {{-- Admin Note --}}
                            <div>
                                <x-forms.textarea name="admin_note" :label="__('appraisals.admin_note')" rows="4" :value="$appraisal->admin_note"
                                    :placeholder="__('appraisals.admin_note_placeholder')" />
                            </div>
                        </div>
                    </div>

                    {{-- Card: User Info (Read Only) --}}
                    <div class="bg-card shadow-sm rounded-lg border border-border p-6">
                        <h3 class="text-sm font-bold text-text-secondary uppercase tracking-wider mb-3">
                            {{ __('appraisals.customer_info') }}</h3>
                        <div class="text-sm">
                            <p class="font-semibold text-text-primary">{{ $appraisal->user->name }}</p>
                            <p class="text-text-secondary">{{ $appraisal->user->email }}</p>
                            <p class="text-text-secondary mt-1">
                                {{ $appraisal->user->phone_number ?? __('common.no_phone') }}</p>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="pt-2">
                        <x-forms.button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('appraisals.save_changes') }}
                        </x-forms.button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
