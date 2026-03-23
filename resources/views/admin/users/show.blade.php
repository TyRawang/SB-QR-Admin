<x-admin-layout title="User Detail">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2"><a href="{{ route('admin.users.index') }}" class="hover:text-gray-700">Users</a></li>
        <li class="before:content-['/'] before:mx-2">{{ $profile['display_name'] ?? $profile['email'] ?? 'User' }}</li>
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        {{-- Profile Header --}}
        <div class="rounded-lg bg-white shadow p-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $profile['display_name'] ?? 'No name' }}</h1>
                    <p class="text-sm text-gray-500">{{ $profile['email'] ?? 'No email' }}</p>
                    @if($profile['phone'])
                        <p class="text-sm text-gray-500">{{ $profile['phone'] }}</p>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0 flex gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $profile['role'] === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                        {{ ucfirst($profile['role']) }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $profile['is_active'] ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ $profile['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Joined</dt>
                    <dd class="text-sm text-gray-900">{{ ($profile['created_at'] ?? null) ? \Carbon\Carbon::parse($profile['created_at'])->format('M j, Y g:i A') : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Seen</dt>
                    <dd class="text-sm text-gray-900">{{ ($profile['last_seen_at'] ?? null) ? \Carbon\Carbon::parse($profile['last_seen_at'])->diffForHumans() : 'Never' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Onboarding</dt>
                    <dd class="text-sm text-gray-900">{{ $profile['onboarding_complete'] ? 'Complete' : 'Pending' }}</dd>
                </div>
            </dl>

            {{-- Actions --}}
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-4">
                <form method="POST" action="{{ route('admin.users.toggle-role', $profile['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Switch to {{ $profile['role'] === 'admin' ? 'Customer' : 'Admin' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.toggle-active', $profile['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        {{ $profile['is_active'] ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                @if($profile['email'])
                <form method="POST" action="{{ route('admin.users.reset-password', $profile['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-yellow-50 px-3 py-2 text-sm font-semibold text-yellow-700 hover:bg-yellow-100">
                        Send Password Reset
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Auth Info --}}
        @if($authUser)
        <div class="rounded-lg bg-white shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Auth Info</h2>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Provider</dt>
                    <dd class="text-sm text-gray-900">{{ $authUser['app_metadata']['provider'] ?? 'Unknown' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email Confirmed</dt>
                    <dd class="text-sm text-gray-900">{{ ($authUser['email_confirmed_at'] ?? null) ? 'Yes' : 'No' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Sign In</dt>
                    <dd class="text-sm text-gray-900">{{ isset($authUser['last_sign_in_at']) ? \Carbon\Carbon::parse($authUser['last_sign_in_at'])->diffForHumans() : 'Never' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        {{-- User's Boxes --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Boxes ({{ count($boxes) }})</h2>
            </div>
            @if(count($boxes) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Claimed</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($boxes as $box)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $box['name'] ?? 'Unnamed' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $box['locations']['name'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $box['claimed_at'] ? \Carbon\Carbon::parse($box['claimed_at'])->format('M j, Y') : '-' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.boxes.show', $box['id']) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No boxes.</p>
            @endif
        </div>

        {{-- User's Locations --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Locations ({{ count($locations) }})</h2>
            </div>
            @if(count($locations) > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($locations as $loc)
                <li class="px-6 py-3 text-sm text-gray-900">{{ $loc['name'] }}</li>
                @endforeach
            </ul>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No locations.</p>
            @endif
        </div>

        {{-- User's Feedback --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Feedback ({{ count($feedback) }})</h2>
            </div>
            @if(count($feedback) > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($feedback as $fb)
                <li class="px-6 py-4">
                    <p class="text-sm text-gray-900">{{ $fb['message'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($fb['rating'])
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $fb['rating'] ? 'text-yellow-400' : 'text-gray-300' }}">&#9733;</span>
                            @endfor
                            &middot;
                        @endif
                        {{ ($fb['created_at'] ?? null) ? \Carbon\Carbon::parse($fb['created_at'])->format('M j, Y') : '' }}
                    </p>
                </li>
                @endforeach
            </ul>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No feedback.</p>
            @endif
        </div>
    </div>
</x-admin-layout>
