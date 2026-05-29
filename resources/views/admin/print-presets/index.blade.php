<x-admin-layout title="Print Presets">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Print Presets</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Print Presets</h1>
                <p class="mt-1 text-sm text-gray-500">Layout and image settings used when generating sticker sheets.</p>
            </div>
            <a href="{{ route('admin.print-presets.create') }}"
               class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                New Preset
            </a>
        </div>

        <div class="rounded-lg bg-white shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Logo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Background</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($presets as $preset)
                    <tr>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $preset->name }}</span>
                                @if($preset->is_default)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Default</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $preset->page_size }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $preset->cols }} × {{ $preset->rows }} ({{ $preset->cols * $preset->rows }})</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $preset->logo_url ? 'Yes' : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $preset->background_url ? 'Yes' : '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            @if(!$preset->is_default)
                                <form method="POST" action="{{ route('admin.print-presets.set-default', $preset) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900">Set Default</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.print-presets.edit', $preset) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form method="POST" action="{{ route('admin.print-presets.destroy', $preset) }}" class="inline"
                                  onsubmit="return confirm('Delete preset &quot;{{ $preset->name }}&quot;?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            No presets yet. <a href="{{ route('admin.print-presets.create') }}" class="text-blue-600 hover:text-blue-900">Create one</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
