<x-admin-layout title="Storage">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Storage</li>
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Image & Storage Management</h1>

        {{-- Buckets Overview --}}
        @if(!empty($buckets))
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($buckets as $b)
            <div class="rounded-lg bg-white shadow p-4">
                <h3 class="text-sm font-semibold text-gray-900">{{ $b['name'] ?? $b['id'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                    {{ ($b['public'] ?? false) ? 'Public' : 'Private' }}
                    &middot; Created {{ isset($b['created_at']) ? \Carbon\Carbon::parse($b['created_at'])->format('M j, Y') : '-' }}
                </p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Filters --}}
        <div class="rounded-lg bg-white shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Browse Images</h2>
            <form method="GET" action="{{ route('admin.storage.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="box_id" class="block text-sm font-medium text-gray-700">Box ID</label>
                    <input type="text" name="box_id" id="box_id" value="{{ $boxId }}" placeholder="Filter by box UUID"
                           class="mt-1 block w-full rounded-md border-0 py-2 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                    <input type="text" name="user_id" id="user_id" value="{{ $userId }}" placeholder="Filter by user UUID"
                           class="mt-1 block w-full rounded-md border-0 py-2 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                </div>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Filter</button>
                <a href="{{ route('admin.storage.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
            </form>
        </div>

        {{-- Image Grid --}}
        <div x-data="{ selectedImages: [] }">
            {{-- Bulk delete --}}
            <div x-show="selectedImages.length > 0" x-cloak class="mb-4 flex items-center gap-3 rounded-lg bg-red-50 px-4 py-3">
                <span class="text-sm font-medium text-red-700" x-text="selectedImages.length + ' selected'"></span>
                <form method="POST" action="{{ route('admin.storage.destroy') }}" x-ref="deleteForm">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedImages" :key="id">
                        <input type="hidden" name="image_ids[]" :value="id">
                    </template>
                    <button type="submit" onclick="return confirm('Delete selected images?')"
                            class="rounded bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">Delete Selected</button>
                </form>
            </div>

            @if(count($images) > 0)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach($images as $image)
                <div class="relative group">
                    <div class="absolute top-2 left-2 z-10">
                        <input type="checkbox" :value="'{{ $image['id'] }}'" x-model="selectedImages"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    </div>
                    @if($image['signed_url'])
                        <img src="{{ $image['signed_url'] }}" alt="{{ $image['filename'] }}"
                             class="aspect-square w-full rounded-lg object-cover bg-gray-100">
                    @else
                        <div class="aspect-square w-full rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No preview</div>
                    @endif
                    <div class="mt-1">
                        <p class="text-xs text-gray-700 truncate">{{ $image['filename'] }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $image['file_size'] ? number_format($image['file_size'] / 1024, 1) . ' KB' : '' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="rounded-lg bg-white shadow px-6 py-8 text-center text-sm text-gray-500">No images found.</div>
            @endif
        </div>
    </div>
</x-admin-layout>
