@props(['id', 'title' => 'Confirm Action', 'message' => 'Are you sure?', 'action', 'method' => 'POST', 'buttonText' => 'Confirm', 'buttonColor' => 'red'])

<div x-data="{ open: false }" x-cloak>
    <span @click="open = true">
        {{ $trigger }}
    </span>

    <div x-show="open" class="relative z-50" aria-modal="true">
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="open = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" x-transition class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <form method="POST" action="{{ $action }}">
                            @csrf
                            @if($method === 'DELETE')
                                @method('DELETE')
                            @elseif($method === 'PATCH')
                                @method('PATCH')
                            @endif
                            {{ $hiddenFields ?? '' }}
                            <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:w-auto {{ $buttonColor === 'red' ? 'bg-red-600 hover:bg-red-500' : 'bg-blue-600 hover:bg-blue-500' }}">
                                {{ $buttonText }}
                            </button>
                        </form>
                        <button type="button" @click="open = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
