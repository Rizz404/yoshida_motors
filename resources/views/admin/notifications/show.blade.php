<x-layouts.admin title="Notification Details">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-neutral-800">Notification Details</h1>
        <a href="{{ route('notifications.index') }}">
            <x-forms.button variant="secondary">
                <span class="flex items-center">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                    Back to List
                </span>
            </x-forms.button>
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-neutral-200 p-6">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-neutral-900">{{ $notification->title }}</h2>
                <span class="text-sm text-neutral-500">{{ $notification->created_at->format('d M Y, H:i') }}</span>
            </div>
            <p class="text-neutral-700 text-lg">{{ $notification->body }}</p>
        </div>

        @if ($notification->data)
            <div class="mt-6 border-t border-neutral-200 pt-6">
                <h3 class="text-lg font-medium text-neutral-900 mb-4">Additional Data</h3>
                <div class="bg-neutral-50 rounded-md p-4 overflow-x-auto">
                    <pre class="text-sm text-neutral-700 font-mono">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        @endif

        <div class="mt-8 flex justify-end space-x-3">
            @if (isset($notification->data['appraisal_id']))
                <a href="{{ route('appraisals.edit', $notification->data['appraisal_id']) }}">
                    <x-forms.button variant="primary">
                        <span class="flex items-center">
                            <x-heroicon-o-document-text class="w-5 h-5 mr-1" />
                            View Appraisal
                        </span>
                    </x-forms.button>
                </a>
            @endif

            <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this notification?');">
                @csrf
                @method('DELETE')
                <x-forms.button type="submit" variant="secondary">
                    <span class="flex items-center">
                        <x-heroicon-o-trash class="w-5 h-5 mr-1" />
                        Delete
                    </span>
                </x-forms.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
