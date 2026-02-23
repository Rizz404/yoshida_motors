<x-layouts.admin title="Create Appraisal Request">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">New Appraisal Request</h1>
            <a href="{{ route('appraisals.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; Back to List
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
            {{-- Enctype multipart/form-data wajib buat upload file --}}
            <form action="{{ route('appraisals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- SECTION 1: VEHICLE INFORMATION --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-neutral-900 border-b pb-2">Vehicle Information</h3>

                    {{-- User Selection --}}
                    <div>
                        <x-forms.select name="user_id" label="Customer (Owner)" required>
                            <option value="">-- Select Customer --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    {{-- Brand & Model --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input name="vehicle_brand" label="Brand" placeholder="e.g. Honda" required />
                        <x-forms.input name="vehicle_model" label="Model" placeholder="e.g. Jazz RS" required />
                    </div>

                    {{-- Year --}}
                    <div class="w-full md:w-1/3">
                        <x-forms.input type="number" name="year_manufacture" label="Year of Manufacture"
                            placeholder="2020" min="1900" max="{{ date('Y') + 1 }}" required />
                    </div>

                    {{-- Description --}}
                    <x-forms.textarea name="description" label="Vehicle Description / Condition" rows="3"
                        placeholder="Describe scratches, modifications, or specific conditions..." />

                    {{-- Status --}}
                    <div class="w-full md:w-1/3">
                        <x-forms.select name="status" label="Initial Status">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted
                            </option>
                            <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>Under
                                Review</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
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
                            alert('Maximum 7 photos at once.');
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
                    <h3 class="text-lg font-semibold text-neutral-900 border-b pb-2">Vehicle Photos</h3>
                    <p class="text-sm text-neutral-500 -mt-4">Select up to 7 photos at once, then label each one.
                        Supported formats: JPG, PNG. Max 2MB per photo.</p>

                    {{-- Multiple File Input --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Select Photos</label>
                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/jpg" multiple
                            @change="handleFiles"
                            class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-neutral-300 rounded-lg p-2 bg-white" />
                        <p class="text-xs text-neutral-400 mt-1">Maximum 7 photos.</p>
                    </div>

                    {{-- Photo Preview Grid --}}
                    <div x-show="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="(photo, index) in photos" :key="index">
                            <div class="flex flex-col gap-2 p-3 bg-neutral-50 border border-neutral-200 rounded-lg">
                                <img :src="photo.preview" :alt="photo.name"
                                    class="w-full h-28 object-cover rounded bg-neutral-100" />
                                <input type="text" name="photo_labels[]" placeholder="Label (e.g. Front View)"
                                    class="w-full text-sm border border-neutral-300 rounded px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                <span x-text="photo.name" class="text-xs text-neutral-400 truncate"
                                    :title="photo.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-neutral-200">
                    <x-forms.button type="submit" variant="primary">
                        Create Request
                    </x-forms.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
