<x-admin-layout title="Boxes">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Boxes</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4" x-data="{ selectedBoxes: [], selectAll: false }">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Boxes</h1>
            <p class="text-sm text-gray-500">{{ $total }} total</p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-search-input placeholder="Search by name or QR UUID..." :value="$search"
                    hx-target="#box-list" :hx-url="route('admin.boxes.index')" />
            </div>
            <div>
                <select name="claimed"
                        hx-get="{{ route('admin.boxes.index') }}"
                        hx-target="#box-list"
                        hx-include="[name='search']"
                        class="rounded-md border-0 py-2 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                    <option value="">All Boxes</option>
                    <option value="true" {{ $claimedFilter === 'true' ? 'selected' : '' }}>Claimed</option>
                    <option value="false" {{ $claimedFilter === 'false' ? 'selected' : '' }}>Unclaimed</option>
                </select>
            </div>
        </div>

        {{-- Bulk Actions --}}
        <div x-show="selectedBoxes.length > 0" x-cloak class="flex items-center gap-3 rounded-lg bg-blue-50 px-4 py-3">
            <span class="text-sm font-medium text-blue-700" x-text="selectedBoxes.length + ' selected'"></span>
            <form method="POST" action="{{ route('admin.boxes.bulk-action') }}" x-ref="bulkForm">
                @csrf
                <input type="hidden" name="action" x-ref="bulkAction">
                <template x-for="id in selectedBoxes" :key="id">
                    <input type="hidden" name="box_ids[]" :value="id">
                </template>
                <button type="button" @click="$refs.bulkAction.value = 'unclaim'; $refs.bulkForm.submit()"
                        class="rounded bg-yellow-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-yellow-500">Unclaim</button>
                <button type="button" @click="if(confirm('Delete selected boxes?')) { $refs.bulkAction.value = 'delete'; $refs.bulkForm.submit() }"
                        class="rounded bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">Delete</button>
            </form>
        </div>

        {{-- Box List --}}
        <div id="box-list">
            @include('admin.boxes._list')
        </div>
    </div>
</x-admin-layout>
