<x-admin-layout title="Box Detail">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2"><a href="{{ route('admin.boxes.index') }}" class="hover:text-gray-700">Boxes</a></li>
        <li class="before:content-['/'] before:mx-2">{{ $box['name'] ?? 'Unnamed Box' }}</li>
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        {{-- Box Header --}}
        <div class="rounded-lg bg-white shadow p-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $box['name'] ?? 'Unnamed Box' }}</h1>
                    <p class="text-sm font-mono text-gray-500 mt-1">QR: {{ $box['qr_uuid'] }}</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    @if($box['is_claimed'])
                        <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Claimed</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-sm font-medium text-yellow-700">Unclaimed</span>
                    @endif
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Owner</dt>
                    <dd class="text-sm text-gray-900">
                        @if($box['profiles'])
                            <a href="{{ route('admin.users.show', $box['profiles']['id']) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $box['profiles']['display_name'] ?? $box['profiles']['email'] ?? 'Unknown' }}
                            </a>
                        @else
                            None
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                    <dd class="text-sm text-gray-900">{{ $box['locations']['name'] ?? 'None' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-900">{{ ($box['created_at'] ?? null) ? \Carbon\Carbon::parse($box['created_at'])->format('M j, Y g:i A') : '-' }}</dd>
                </div>
            </dl>

            @if($box['comments'])
            <div class="mt-4">
                <dt class="text-sm font-medium text-gray-500">Comments</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $box['comments'] }}</dd>
            </div>
            @endif

            {{-- Actions --}}
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-4">
                @if($box['is_claimed'])
                <form method="POST" action="{{ route('admin.boxes.unclaim', $box['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-yellow-50 px-3 py-2 text-sm font-semibold text-yellow-700 hover:bg-yellow-100"
                            onclick="return confirm('Unclaim this box?')">
                        Unclaim
                    </button>
                </form>
                @endif

                {{-- Transfer --}}
                <div x-data="{ showTransfer: false }">
                    <button @click="showTransfer = !showTransfer" class="rounded-md bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">
                        Transfer
                    </button>
                    <form x-show="showTransfer" x-cloak method="POST" action="{{ route('admin.boxes.transfer', $box['id']) }}" class="mt-2 flex gap-2 items-center">
                        @csrf
                        <input type="email" name="email" placeholder="Target email" required
                               class="rounded-md border-0 py-1.5 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                        <button type="submit" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-500">Go</button>
                    </form>
                </div>

                <x-confirm-modal :action="route('admin.boxes.destroy', $box['id'])" method="DELETE"
                    title="Delete Box" message="This will permanently delete the box, its items, and images."
                    buttonText="Delete" buttonColor="red">
                    <x-slot:trigger>
                        <button class="rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Delete</button>
                    </x-slot:trigger>
                </x-confirm-modal>
            </div>
        </div>

        {{-- Items --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Items ({{ count($items) }})</h2>
            </div>
            @if(count($items) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['item_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item['quantity'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item['notes'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ ($item['created_at'] ?? null) ? \Carbon\Carbon::parse($item['created_at'])->format('M j, Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No items in this box.</p>
            @endif
        </div>

        {{-- Images --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Images ({{ count($images) }})</h2>
            </div>
            @if(count($images) > 0)
            <div class="p-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($images as $image)
                <div class="group relative">
                    @if($image['signed_url'])
                        <img src="{{ $image['signed_url'] }}" alt="{{ $image['filename'] }}"
                             class="aspect-square w-full rounded-lg object-cover bg-gray-100">
                    @else
                        <div class="aspect-square w-full rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-sm">No preview</div>
                    @endif
                    <p class="mt-1 text-xs text-gray-500 truncate">{{ $image['filename'] }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No images for this box.</p>
            @endif
        </div>
    </div>
</x-admin-layout>
