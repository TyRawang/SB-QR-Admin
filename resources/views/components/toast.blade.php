@php
    $hasMessage = session('success') || session('error');
    $toastMessage = session('success') ?? session('error') ?? '';
    $toastType = session('error') ? 'error' : 'success';
@endphp

<div x-data="{ show: @json($hasMessage), message: @json($toastMessage), type: @json($toastType) }"
     x-show="show" x-cloak
     x-init="if(show) setTimeout(() => show = false, 5000)"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-4 right-4 z-50 max-w-sm">
    <div :class="type === 'error' ? 'bg-red-50 border-red-400 text-red-800' : 'bg-green-50 border-green-400 text-green-800'"
         class="rounded-lg border p-4 shadow-lg">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium" x-text="message"></p>
            <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
