<x-layouts.admin title="Edit Appraisal">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-neutral-800">
                Edit Request #{{ $appraisal->id }}
                <span class="text-base font-normal text-neutral-500 ml-2">({{ $appraisal->vehicle_brand }}
                    {{ $appraisal->vehicle_model }})</span>
            </h1>
            <a href="{{ route('appraisals.index') }}" class="text-sm text-neutral-600 hover:text-primary-600">
                &larr; Back to List
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
                        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Vehicle Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <x-forms.input name="vehicle_brand" label="Brand" :value="$appraisal->vehicle_brand" required />
                            <x-forms.input name="vehicle_model" label="Model" :value="$appraisal->vehicle_model" required />
                        </div>

                        <div class="mb-4">
                            <x-forms.input type="number" name="year_manufacture" label="Year" :value="$appraisal->year_manufacture"
                                required />
                        </div>

                        <x-forms.textarea name="description" label="Description" rows="4" :value="$appraisal->description" />
                    </div>

                    {{-- Card: Photos Management --}}
                    <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
                        <h3 class="text-lg font-semibold text-neutral-900 mb-4">Manage Photos</h3>

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
                                            Image not available
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
                                                <span class="text-xs text-secondary-700 font-semibold">Delete</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-neutral-500 mb-6">* Check "Delete" and save to remove photos.</p>
                        @else
                            <div class="p-4 bg-neutral-50 text-neutral-500 text-sm text-center rounded mb-6">
                                No photos uploaded yet.
                            </div>
                        @endif

                        {{-- Upload New Photos --}}
                        <div class="border-t border-neutral-100 pt-4">
                            <h4 class="text-sm font-bold text-neutral-700 mb-3">Add New Photos</h4>
                            <div class="space-y-4">
                                {{-- Slot 1 --}}
                                <div class="flex flex-col md:flex-row gap-3">
                                    <div class="md:w-1/3">
                                        <x-forms.input name="new_photo_labels[]" placeholder="Label (e.g. Engine)" />
                                    </div>
                                    <div class="md:w-2/3">
                                        <x-forms.input type="file" name="new_photos[]" accept="image/*" />
                                    </div>
                                </div>
                                {{-- Slot 2 --}}
                                <div class="flex flex-col md:flex-row gap-3">
                                    <div class="md:w-1/3">
                                        <x-forms.input name="new_photo_labels[]" placeholder="Label" />
                                    </div>
                                    <div class="md:w-2/3">
                                        <x-forms.input type="file" name="new_photos[]" accept="image/*" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Admin Actions & Status --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Card: Appraisal Result --}}
                    <div class="bg-primary-50 shadow-sm rounded-lg border border-primary-100 p-6">
                        <h3 class="text-lg font-bold text-primary-900 mb-4">Appraisal Result</h3>

                        <div class="space-y-4">
                            {{-- Status --}}
                            <div>
                                <x-forms.select name="status" label="Current Status">
                                    @foreach (['draft', 'submitted', 'under_review', 'completed'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $appraisal->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </x-forms.select>
                            </div>

                            {{-- Final Price --}}
                            <div>
                                <x-forms.input type="number" name="final_price" label="Final Price ($)"
                                    :value="$appraisal->final_price" placeholder="0" step="1000" />
                            </div>

                            {{-- Valid Until --}}
                            <div>
                                <x-forms.input type="date" name="price_valid_until" label="Price Valid Until"
                                    :value="$appraisal->price_valid_until?->format('Y-m-d')" />
                            </div>

                            {{-- Admin Note --}}
                            <div>
                                <x-forms.textarea name="admin_note" label="Internal/Admin Notes" rows="4"
                                    :value="$appraisal->admin_note" placeholder="Notes for user or internal team..." />
                            </div>
                        </div>
                    </div>

                    {{-- Card: User Info (Read Only) --}}
                    <div class="bg-white shadow-sm rounded-lg border border-neutral-200 p-6">
                        <h3 class="text-sm font-bold text-neutral-500 uppercase tracking-wider mb-3">Customer Info</h3>
                        <div class="text-sm">
                            <p class="font-semibold text-neutral-900">{{ $appraisal->user->name }}</p>
                            <p class="text-neutral-600">{{ $appraisal->user->email }}</p>
                            <p class="text-neutral-500 mt-1">{{ $appraisal->user->phone_number ?? 'No Phone' }}</p>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="pt-2">
                        <x-forms.button type="submit" variant="primary" class="w-full justify-center">
                            Save Changes
                        </x-forms.button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
