<x-layouts.admin :title="__('appraisals.edit_title')">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">
                {{ __('appraisals.edit_heading', ['id' => $appraisal->id]) }}
                <span class="text-base font-normal text-neutral-500 ml-2">({{ $appraisal->vehicle_brand }}
                    {{ $appraisal->vehicle_model }})</span>
            </h1>
            <a href="{{ route('appraisals.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <form action="{{ route('appraisals.update', $appraisal) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN: Main Info --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card: Vehicle Details --}}
                    <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-900 mb-4">{{ __('appraisals.vehicle_details') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <x-forms.input name="vehicle_brand" :label="__('appraisals.brand')" :value="$appraisal->vehicle_brand" required />
                            <x-forms.input name="vehicle_model" :label="__('appraisals.model')" :value="$appraisal->vehicle_model" required />
                        </div>

                        <div class="mb-4">
                            <x-forms.input type="number" name="year_manufacture" :label="__('appraisals.year')" :value="$appraisal->year_manufacture"
                                required />
                        </div>

                        <x-forms.textarea name="description" :label="__('appraisals.description')" rows="4" :value="$appraisal->description" />
                    </div>

                    {{-- Card: Photos Management --}}
                    <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-900 mb-4">{{ __('appraisals.manage_photos') }}
                        </h3>

                        {{-- Existing Photos --}}
                        @if ($appraisal->photos->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                @foreach ($appraisal->photos as $photo)
                                    <div class="relative group border border-neutral-200 rounded-lg overflow-hidden">
                                        {{-- Image Preview --}}
                                        <img src="{{ asset('storage/' . $photo->image_path) }}"
                                            alt="{{ $photo->category_name }}"
                                            class="w-full h-32 object-cover bg-neutral-100"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />

                                        {{-- Local fallback (no internet needed) --}}
                                        <div
                                            class="hidden w-full h-32 bg-neutral-100 items-center justify-center text-neutral-400 text-xs text-center p-2">
                                            {{ __('common.image_not_available') }}
                                        </div>

                                        {{-- Label --}}
                                        <div
                                            class="p-2 bg-white text-xs font-bold text-center text-neutral-700 truncate border-t border-neutral-200">
                                            {{ $photo->category_name }}
                                        </div>

                                        {{-- Delete Overlay --}}
                                        <div class="absolute top-2 right-2">
                                            <label
                                                class="flex items-center space-x-1 bg-white/90 px-2 py-1 rounded shadow-sm border border-secondary-200 cursor-pointer hover:bg-secondary-50">
                                                <input type="checkbox" name="delete_photos[]"
                                                    value="{{ $photo->id }}"
                                                    class="text-secondary-600 rounded focus:ring-secondary-500">
                                                <span
                                                    class="text-xs text-secondary-700 font-semibold">{{ __('common.delete_photo') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-neutral-500 mb-6">{{ __('appraisals.delete_photos_hint') }}</p>
                        @else
                            <div class="p-4 bg-neutral-50 text-neutral-500 text-sm text-center rounded mb-6">
                                {{ __('appraisals.no_photos') }}
                            </div>
                        @endif

                        {{-- Upload New Photos --}}
                        <div class="border-t border-neutral-100 pt-4" x-data="{
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
                            <h4 class="text-sm font-bold text-neutral-700 mb-3">{{ __('appraisals.add_new_photos') }}
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-neutral-700 mb-1">{{ __('appraisals.select_photos') }}</label>
                                    <input type="file" name="new_photos[]" accept="image/jpeg,image/png,image/jpg"
                                        multiple @change="handleFiles"
                                        class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-neutral-300 rounded-lg p-2 bg-white" />
                                    <p class="text-xs text-neutral-400 mt-1">{{ __('appraisals.max_photos_each') }}</p>
                                </div>

                                {{-- New Photo Preview Grid --}}
                                <div x-show="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div
                                            class="flex flex-col gap-2 p-3 bg-neutral-50 border border-neutral-200 rounded-lg">
                                            <img :src="photo.preview" :alt="photo.name"
                                                class="w-full h-28 object-cover rounded bg-neutral-100" />
                                            <input type="text" name="new_photo_labels[]"
                                                :placeholder="'{{ __('appraisals.label_placeholder_engine') }}'"
                                                class="w-full text-sm border border-neutral-300 rounded px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                            <span x-text="photo.name" class="text-xs text-neutral-400 truncate"
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
                    <div class="bg-primary-50 shadow-sm rounded-lg border border-primary-100 p-6">
                        <h3 class="text-lg font-bold text-primary-900 mb-4">{{ __('appraisals.appraisal_result') }}
                        </h3>

                        <div class="space-y-4">
                            {{-- Status --}}
                            <div>
                                <x-forms.select name="status" :label="__('appraisals.current_status')">
                                    @foreach (['draft', 'submitted', 'under_review', 'completed'] as $status)
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
                                    placeholder="0" step="1000" />
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
                    <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
                        <h3 class="text-sm font-bold text-neutral-500 uppercase tracking-wider mb-3">
                            {{ __('appraisals.customer_info') }}</h3>
                        <div class="text-sm">
                            <p class="font-semibold text-neutral-900">{{ $appraisal->user->name }}</p>
                            <p class="text-neutral-600">{{ $appraisal->user->email }}</p>
                            <p class="text-neutral-500 mt-1">
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
