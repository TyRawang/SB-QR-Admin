<x-admin-layout title="System Health">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">System</li>
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">System Health</h1>

        {{-- Connection Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-white shadow p-6">
                <div class="flex items-center gap-3">
                    <div class="rounded-full p-2 {{ $supabaseConnected ? 'bg-green-100' : 'bg-red-100' }}">
                        @if($supabaseConnected)
                            <svg class="size-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @else
                            <svg class="size-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Supabase REST API</h3>
                        <p class="text-sm {{ $supabaseConnected ? 'text-green-600' : 'text-red-600' }}">
                            {{ $supabaseConnected ? 'Connected' : 'Disconnected' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow p-6">
                <div class="flex items-center gap-3">
                    <div class="rounded-full p-2 {{ $storageConnected ? 'bg-green-100' : 'bg-red-100' }}">
                        @if($storageConnected)
                            <svg class="size-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @else
                            <svg class="size-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Supabase Storage</h3>
                        <p class="text-sm {{ $storageConnected ? 'text-green-600' : 'text-red-600' }}">
                            {{ $storageConnected ? 'Connected' : 'Disconnected' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Environment Info --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Environment</h2>
            </div>
            <dl class="divide-y divide-gray-200">
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $envInfo['php_version'] }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $envInfo['laravel_version'] }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Supabase URL</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-mono">{{ $envInfo['supabase_url'] }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Service Key</dt>
                    <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0 {{ $envInfo['service_key_set'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $envInfo['service_key_set'] ? 'Configured' : 'Not Set' }}
                    </dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">App Environment</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $envInfo['app_env'] }}</dd>
                </div>
                <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-gray-500">Debug Mode</dt>
                    <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0 {{ $envInfo['app_debug'] ? 'text-yellow-600' : 'text-green-600' }}">
                        {{ $envInfo['app_debug'] ? 'Enabled' : 'Disabled' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Storage Buckets --}}
        @if(!empty($buckets))
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Storage Buckets</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Bucket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Public</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">File Size Limit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Allowed Types</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($buckets as $b)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $b['name'] ?? $b['id'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ ($b['public'] ?? false) ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ isset($b['file_size_limit']) ? number_format($b['file_size_limit'] / 1024 / 1024, 1) . ' MB' : 'Default' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ (isset($b['allowed_mime_types']) && is_array($b['allowed_mime_types'])) ? implode(', ', $b['allowed_mime_types']) : 'Any' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-admin-layout>
