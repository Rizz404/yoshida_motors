<x-layouts.admin :title="__('appraisals.create_title')">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-text-primary">{{ __('appraisals.create_heading') }}</h1>
            <a href="{{ route('appraisals.index') }}" class="text-sm text-text-secondary hover:text-primary">
                &larr; {{ __('common.back_to_list') }}
            </a>
        </div>

        <div class="bg-card shadow-sm rounded-lg border border-border p-6">
            {{-- Enctype multipart/form-data wajib buat upload file --}}
            <form action="{{ route('appraisals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- SECTION 1: VEHICLE INFORMATION --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-text-primary border-b pb-2">
                        {{ __('appraisals.section_vehicle') }}</h3>

                    {{-- User Selection --}}
                    <div>
                        <x-forms.select name="user_id" :label="__('appraisals.customer_owner')" required>
                            <option value="">{{ __('appraisals.select_customer') }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    {{-- Brand & Model --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input name="vehicle_brand" :label="__('appraisals.brand')" placeholder="e.g. Honda" required />
                        <x-forms.input name="vehicle_model" :label="__('appraisals.model')" placeholder="e.g. Jazz RS" required />
                    </div>

                    {{-- Year & License Plate --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input type="number" name="year_manufacture" :label="__('appraisals.year_of_manufacture')" placeholder="2020"
                            min="1900" max="{{ date('Y') + 1 }}" required />
                        <x-forms.input type="text" name="license_plate" :label="__('appraisals.license_plate')" :placeholder="__('appraisals.license_plate_placeholder')" />
                    </div>

                    {{-- Mileage --}}
                    <div class="w-full md:w-1/3">
                        <x-forms.input type="number" name="mileage" :label="__('appraisals.mileage')" placeholder="0" min="0" />
                    </div>

                    {{-- Description --}}
                    <x-forms.textarea name="description" :label="__('appraisals.vehicle_description')" rows="3" :placeholder="__('appraisals.description_placeholder')" />

                    {{-- Status --}}
                    <div class="w-full md:w-1/3">
                        <x-forms.select name="status" :label="__('appraisals.initial_status')">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                {{ __('appraisals.status_draft') }}</option>
                            <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>
                                {{ __('appraisals.status_submitted') }}
                            </option>
                            <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>
                                {{ __('appraisals.status_under_review') }}</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                {{ __('appraisals.status_completed') }}
                            </option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                {{ __('appraisals.status_rejected') }}
                            </option>
                        </x-forms.select>
                    </div>
                </div>

                {{-- SECTION 2: PHOTOS --}}
                <div class="space-y-6" x-data="{
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
                    <h3 class="text-lg font-semibold text-text-primary border-b pb-2">
                        {{ __('appraisals.section_photos') }}</h3>
                    <p class="text-sm text-text-secondary -mt-4">{{ __('appraisals.photos_hint') }}</p>

                    {{-- Multiple File Input --}}
                    <div>
                        <label
                            class="block text-sm font-medium text-text-primary mb-2">{{ __('appraisals.select_photos') }}</label>
                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/jpg" multiple
                            @change="handleFiles"
                            class="block w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-primary hover:file:bg-primary-container/80 cursor-pointer border border-border rounded-lg p-2 bg-surface" />
                        <p class="text-xs text-text-tertiary mt-1">{{ __('appraisals.max_photos') }}</p>
                    </div>

                    {{-- Photo Preview Grid --}}
                    <div x-show="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="(photo, index) in photos" :key="index">
                            <div class="flex flex-col gap-2 p-3 bg-background border border-border rounded-lg">
                                <img :src="photo.preview" :alt="photo.name"
                                    class="w-full h-28 object-cover rounded bg-surface-variant" />
                                <input type="text" name="photo_labels[]"
                                    :placeholder="'{{ __('appraisals.label_placeholder') }}'"
                                    class="w-full text-sm border border-border rounded px-2 py-1.5 bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" />
                                <span x-text="photo.name" class="text-xs text-text-tertiary truncate"
                                    :title="photo.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-border">
                    <x-forms.button type="submit" variant="primary">
                        {{ __('appraisals.create_button') }}
                    </x-forms.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
