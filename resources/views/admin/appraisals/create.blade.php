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
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-neutral-900 border-b pb-2">Vehicle Photos</h3>
                    <p class="text-sm text-neutral-500 -mt-4">Upload up to 3 initial photos. Supported formats: JPG,
                        PNG.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Photo Slot 1 --}}
                        <div class="p-4 bg-neutral-50 rounded border border-neutral-200">
                            <h4 class="text-sm font-bold text-neutral-700 mb-3">Photo #1</h4>
                            <x-forms.input name="photo_labels[]" label="Label" placeholder="e.g. Front View"
                                class="mb-3" />
                            <x-forms.input type="file" name="photos[]" accept="image/*" />
                        </div>

                        {{-- Photo Slot 2 --}}
                        <div class="p-4 bg-neutral-50 rounded border border-neutral-200">
                            <h4 class="text-sm font-bold text-neutral-700 mb-3">Photo #2</h4>
                            <x-forms.input name="photo_labels[]" label="Label" placeholder="e.g. Side View"
                                class="mb-3" />
                            <x-forms.input type="file" name="photos[]" accept="image/*" />
                        </div>

                        {{-- Photo Slot 3 --}}
                        <div class="p-4 bg-neutral-50 rounded border border-neutral-200">
                            <h4 class="text-sm font-bold text-neutral-700 mb-3">Photo #3</h4>
                            <x-forms.input name="photo_labels[]" label="Label" placeholder="e.g. Interior"
                                class="mb-3" />
                            <x-forms.input type="file" name="photos[]" accept="image/*" />
                        </div>
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
