<x-admin-layout title="Dashboard">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Total Users" :value="$userCount" color="blue"
                icon="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />

            <x-stat-card label="Total Boxes" :value="$boxCount" color="indigo"
                icon="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />

            <x-stat-card label="Claimed Boxes" :value="$claimedBoxCount" color="green"
                icon="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

            <x-stat-card label="Unclaimed Boxes" :value="$unclaimedBoxCount" color="yellow"
                icon="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Total Items" :value="$itemCount" color="purple"
                icon="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />

            <x-stat-card label="Images" :value="$imageCount" color="pink"
                icon="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />

            <x-stat-card label="Locations" :value="$locationCount" color="red"
                icon="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />

            <x-stat-card label="Feedback" :value="$feedbackCount" color="gray"
                icon="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
        </div>

        {{-- Claim Rate Chart --}}
        @if($claimedBoxCount > 0 || $unclaimedBoxCount > 0)
        <div class="rounded-lg bg-white shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Box Claim Rate</h2>
            <div class="max-w-xs mx-auto">
                <canvas id="claimChart" width="250" height="250"></canvas>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Chart(document.getElementById('claimChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Claimed', 'Unclaimed'],
                        datasets: [{
                            data: [{{ $claimedBoxCount }}, {{ $unclaimedBoxCount }}],
                            backgroundColor: ['#22c55e', '#eab308'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            });
        </script>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Recent Claimed Boxes --}}
            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Recently Claimed Boxes</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentBoxes as $box)
                        <li class="px-4 py-3">
                            <a href="{{ route('admin.boxes.show', $box['id']) }}" class="hover:text-blue-600">
                                <p class="text-sm font-medium text-gray-900">{{ $box['name'] ?? 'Unnamed Box' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ ($box['profiles'] ?? null) ? ($box['profiles']['display_name'] ?? $box['profiles']['email'] ?? 'Unknown') : 'Unknown' }}
                                    &middot; {{ ($box['claimed_at'] ?? null) ? \Carbon\Carbon::parse($box['claimed_at'])->diffForHumans() : '' }}
                                </p>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500">No recently claimed boxes.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Recent Users --}}
            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Newest Users</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentUsers as $user)
                        <li class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user['id']) }}" class="hover:text-blue-600">
                                <p class="text-sm font-medium text-gray-900">{{ $user['display_name'] ?? 'No name' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $user['email'] ?? 'No email' }}
                                    &middot; {{ ($user['created_at'] ?? null) ? \Carbon\Carbon::parse($user['created_at'])->diffForHumans() : '' }}
                                </p>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500">No users yet.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Recent Feedback --}}
            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Recent Feedback</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentFeedback as $fb)
                        <li class="px-4 py-3">
                            <p class="text-sm text-gray-900 line-clamp-2">{{ $fb['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($fb['rating'])
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $fb['rating'] ? 'text-yellow-400' : 'text-gray-300' }}">&#9733;</span>
                                    @endfor
                                    &middot;
                                @endif
                                {{ ($fb['created_at'] ?? null) ? \Carbon\Carbon::parse($fb['created_at'])->diffForHumans() : '' }}
                            </p>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500">No feedback yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Storage Buckets --}}
        @if(!empty($buckets))
        <div class="rounded-lg bg-white shadow">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Storage Buckets</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Public</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($buckets as $bucket)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $bucket['name'] ?? $bucket['id'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                @if($bucket['public'] ?? false)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">Public</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">Private</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ isset($bucket['created_at']) ? \Carbon\Carbon::parse($bucket['created_at'])->format('M j, Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-admin-layout>
