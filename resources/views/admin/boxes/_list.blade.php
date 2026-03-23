<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">
                    <input type="checkbox" @change="selectAll = $event.target.checked; selectedBoxes = selectAll ? @json(array_column($boxes, 'id')) : []"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">QR UUID</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Owner</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($boxes as $box)
            <tr>
                <td class="px-6 py-4">
                    <input type="checkbox" :value="@json($box['id'])"
                           x-model="selectedBoxes"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <a href="{{ route('admin.boxes.show', $box['id']) }}" class="hover:text-blue-600">
                        {{ $box['name'] ?? 'Unnamed Box' }}
                    </a>
                </td>
                <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ Str::limit($box['qr_uuid'] ?? '', 12) }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @if($box['profiles'] ?? null)
                        <a href="{{ route('admin.users.show', $box['profiles']['id']) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $box['profiles']['display_name'] ?? $box['profiles']['email'] ?? 'Unknown' }}
                        </a>
                    @else
                        <span class="text-gray-400">None</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $box['locations']['name'] ?? '-' }}</td>
                <td class="px-6 py-4">
                    @if($box['is_claimed'] ?? false)
                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Claimed</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700">Unclaimed</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    {{ ($box['created_at'] ?? null) ? \Carbon\Carbon::parse($box['created_at'])->format('M j, Y') : '-' }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                    <a href="{{ route('admin.boxes.show', $box['id']) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">No boxes found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <x-pagination :page="$page" :total-pages="$totalPages" :total="$total" :per-page="$perPage" />
</div>
