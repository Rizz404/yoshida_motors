<x-layouts.admin :title="__('notifications.show_title')">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-text-primary">{{ __('notifications.show_heading') }}</h1>
        <a href="{{ route('notifications.index') }}">
            <x-forms.button variant="secondary">
                <span class="flex items-center">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                    {{ __('notifications.back_to_list') }}
                </span>
            </x-forms.button>
        </a>
    </div>

    <div class="bg-card overflow-hidden shadow-sm sm:rounded-lg border border-border p-6">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-text-primary">{{ $notification->title }}</h2>
                <span class="text-sm text-text-secondary">{{ $notification->created_at->format('d M Y, H:i') }}</span>
            </div>
            <p class="text-text-primary text-lg">{{ $notification->body }}</p>
        </div>

        @if ($notification->data)
            <div class="mt-6 border-t border-border pt-6">
                <h3 class="text-lg font-medium text-text-primary mb-4">{{ __('notifications.additional_data') }}</h3>
                <div class="bg-background rounded-md p-4 overflow-x-auto">
                    <pre class="text-sm text-text-secondary font-mono">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        @endif

        <div class="mt-8 flex justify-end space-x-3">
            @if (isset($notification->data['appraisal_id']))
                <a href="{{ route('appraisals.edit', $notification->data['appraisal_id']) }}">
                    <x-forms.button variant="primary">
                        <span class="flex items-center">
                            <x-heroicon-o-document-text class="w-5 h-5 mr-1" />
                            {{ __('notifications.view_appraisal') }}
                        </span>
                    </x-forms.button>
                </a>
            @endif

            <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                onsubmit="return confirm('{{ __('notifications.delete_confirm') }}');">
                @csrf
                @method('DELETE')
                <x-forms.button type="submit" variant="secondary">
                    <span class="flex items-center">
                        <x-heroicon-o-trash class="w-5 h-5 mr-1" />
                        {{ __('notifications.delete') }}
                    </span>
                </x-forms.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
