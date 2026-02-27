<!-- resources/views/components/ui/notification.blade.php -->
<div x-data="{
    show: false,
    type: 'success', // success, error, warning, info
    message: '',
    title: '',
    details: null,
    showDetails: false,
    timeout: null,

    init() {
        window.addEventListener('notify', event => {
            this.trigger(event.detail);
        });

        @if(session()->has('notify'))
        this.trigger({{ json_encode(session('notify')) }});
        @endif
    },

    trigger(data) {
        this.show = true;
        this.type = data.type || 'success';
        this.message = data.message || '';
        this.title = data.title || '';
        this.details = data.details || null;
        this.showDetails = false;

        if (this.timeout) clearTimeout(this.timeout);
        if (this.type !== 'error') {
            this.timeout = setTimeout(() => { this.close() }, 5000);
        }
    },

    close() {
        this.show = false;
    },

    get colors() {
        const colors = {
            success: {
                bg: 'bg-success-light',
                border: 'border-success',
                text: 'text-success-dark',
                icon: 'text-success',
                btn: 'hover:bg-success-light focus:ring-success'
            },
            error: {
                bg: 'bg-error-light',
                border: 'border-error',
                text: 'text-error-dark',
                icon: 'text-error',
                btn: 'hover:bg-error-light focus:ring-error'
            },
            warning: {
                bg: 'bg-warning-light',
                border: 'border-warning',
                text: 'text-warning-dark',
                icon: 'text-warning',
                btn: 'hover:bg-warning-light focus:ring-warning'
            },
            info: {
                bg: 'bg-info-light',
                border: 'border-info',
                text: 'text-info-dark',
                icon: 'text-info',
                btn: 'hover:bg-info-light focus:ring-info'
            }
        };
        return colors[this.type] || colors.info;
    }
}" x-show="show" x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start" style="display: none;">
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">

        <!-- Notification Card -->
        <div class="max-w-md w-full pointer-events-auto rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
            :class="[colors.bg, colors.border, 'border']">
            <div class="p-4">
                <div class="flex items-start">

                    <!-- Icon -->
                    <div class="shrink-0">
                        <!-- Success Icon -->
                        <div x-show="type === 'success'">
                            <x-heroicon-o-check-circle class="h-6 w-6" ::class="colors.icon" />
                        </div>
                        <!-- Error Icon -->
                        <div x-show="type === 'error'">
                            <x-heroicon-o-x-circle class="h-6 w-6" ::class="colors.icon" />
                        </div>
                        <!-- Warning Icon -->
                        <div x-show="type === 'warning'">
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6" ::class="colors.icon" />
                        </div>
                        <!-- Info Icon -->
                        <div x-show="type === 'info'">
                            <x-heroicon-o-information-circle class="h-6 w-6" ::class="colors.icon" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p x-show="title" class="text-sm font-semibold mb-1" :class="colors.text" x-text="title"></p>
                        <p class="text-sm font-medium opacity-90" :class="colors.text" x-text="message"></p>

                        <!-- Detail Toggle -->
                        <div x-show="details" class="mt-2">
                            <button @click="showDetails = !showDetails"
                                class="text-xs font-semibold underline focus:outline-none" :class="colors.text">
                                <span x-text="showDetails ? 'Hide Details' : 'Show Details'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="ml-4 shrink-0 flex">
                        <button @click="close()"
                            class="rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                            :class="colors.btn">
                            <span class="sr-only">Close</span>
                            <x-heroicon-s-x-mark class="h-5 w-5" ::class="colors.text" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail / Stack Trace Section -->
            <div x-show="showDetails && details" x-collapse
                class="bg-surface border-t border-border p-4 max-h-60 overflow-y-auto">
                <pre class="text-xs text-text-secondary font-mono whitespace-pre-wrap break-all" x-text="details"></pre>
            </div>
        </div>
    </div>
</div>
