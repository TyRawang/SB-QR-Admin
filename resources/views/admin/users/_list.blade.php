<div class="overflow-hidden rounded-lg bg-white shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Onboarding</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Last Seen</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Joined</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($users as $user)
            <tr>
                <td class="whitespace-nowrap px-6 py-4">
                    <a href="{{ route('admin.users.show', $user['id']) }}" class="hover:text-blue-600">
                        <div class="text-sm font-medium text-gray-900">{{ $user['display_name'] ?? 'No name' }}</div>
                        <div class="text-sm text-gray-500">{{ $user['email'] ?? 'No email' }}</div>
                    </a>
                </td>
                <td class="whitespace-nowrap px-6 py-4" id="role-{{ $user['id'] }}">
                    @include('admin.users._role_badge')
                </td>
                <td class="whitespace-nowrap px-6 py-4" id="active-{{ $user['id'] }}">
                    @include('admin.users._active_badge')
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    @if($user['onboarding_complete'])
                        <span class="text-green-600">Complete</span>
                    @else
                        <span class="text-gray-400">Pending</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    {{ ($user['last_seen_at'] ?? null) ? \Carbon\Carbon::parse($user['last_seen_at'])->diffForHumans() : 'Never' }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    {{ ($user['created_at'] ?? null) ? \Carbon\Carbon::parse($user['created_at'])->format('M j, Y') : '-' }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                    <a href="{{ route('admin.users.show', $user['id']) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <x-pagination :page="$page" :total-pages="$totalPages" :total="$total" :per-page="$perPage" />
</div>
