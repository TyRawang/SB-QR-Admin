<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Owner</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Boxes</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($locations as $location)
            <tr x-data="{ editing: false, name: @json($location['name'] ?? '') }">
                <td class="px-6 py-4 text-sm text-gray-900">
                    <span x-show="!editing" x-text="name"></span>
                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.locations.update', $location['id']) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="name" x-model="name"
                               class="rounded-md border-0 py-1 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                        <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">Save</button>
                        <button type="button" @click="editing = false" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</button>
                    </form>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @if($location['profiles'] ?? null)
                        <a href="{{ route('admin.users.show', $location['profiles']['id']) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $location['profiles']['display_name'] ?? $location['profiles']['email'] ?? 'Unknown' }}
                        </a>
                    @else
                        <span class="text-gray-400">Unknown</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $location['box_count'] }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ ($location['created_at'] ?? null) ? \Carbon\Carbon::parse($location['created_at'])->format('M j, Y') : '-' }}</td>
                <td class="px-6 py-4 text-right text-sm space-x-2">
                    <button @click="editing = true" x-show="!editing" class="text-blue-600 hover:text-blue-900">Edit</button>
                    @if($location['box_count'] === 0)
                    <form method="POST" action="{{ route('admin.locations.destroy', $location['id']) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900"
                                onclick="return confirm('Delete this location?')">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No locations found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <x-pagination :page="$page" :total-pages="$totalPages" :total="$total" :per-page="$perPage" />
</div>
