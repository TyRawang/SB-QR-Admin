<x-admin-layout title="QR Code Generation">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">QR Codes</li>
    </x-slot:breadcrumbs>

    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">QR Code Generation</h1>

        {{-- Generate Form --}}
        <div class="rounded-lg bg-white shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Generate Sticker Sheets</h2>
            <form method="POST" action="{{ route('admin.qr.generate') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 max-w-lg">
                    <div>
                        <label for="count" class="block text-sm font-medium text-gray-700">QR Codes per PDF</label>
                        <input type="number" name="count" id="count" min="1" max="20" value="10" required
                               class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">1-20 codes per sheet</p>
                    </div>
                    <div>
                        <label for="pdf_count" class="block text-sm font-medium text-gray-700">Number of PDFs</label>
                        <input type="number" name="pdf_count" id="pdf_count" min="1" max="10" value="1" required
                               class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">1-10 PDFs</p>
                    </div>
                </div>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Generate
                </button>
            </form>

            {{-- Show results --}}
            @if(session('pdf_results'))
            <div class="mt-6 border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Generated PDFs</h3>
                <ul class="space-y-2">
                    @foreach(session('pdf_results') as $result)
                    <li>
                        <a href="{{ $result['url'] ?? '#' }}" target="_blank"
                           class="text-blue-600 hover:text-blue-900 text-sm">
                            Download PDF ({{ $result['count'] ?? '?' }} codes)
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Export History --}}
        @if(!empty($exports))
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Previous Exports</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($exports as $export)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $export['name'] ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ isset($export['created_at']) ? \Carbon\Carbon::parse($export['created_at'])->format('M j, Y g:i A') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Unclaimed QR Inventory --}}
        <div class="rounded-lg bg-white shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Unclaimed QR Codes ({{ count($unclaimedBoxes) }})</h2>
            </div>
            @if(count($unclaimedBoxes) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">QR UUID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($unclaimedBoxes as $box)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $box['qr_uuid'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ ($box['created_at'] ?? null) ? \Carbon\Carbon::parse($box['created_at'])->format('M j, Y g:i A') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No unclaimed QR codes.</p>
            @endif
        </div>
    </div>
</x-admin-layout>
