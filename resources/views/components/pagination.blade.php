@props(['page', 'totalPages', 'total', 'perPage', 'baseUrl' => ''])

@if($totalPages > 1)
<nav class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6" aria-label="Pagination">
    <div class="hidden sm:block">
        <p class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ ($page - 1) * $perPage + 1 }}</span>
            to <span class="font-medium">{{ min($page * $perPage, $total) }}</span>
            of <span class="font-medium">{{ $total }}</span> results
        </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end gap-2">
        @if($page > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}"
               class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Previous
            </a>
        @endif
        @if($page < $totalPages)
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
               class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Next
            </a>
        @endif
    </div>
</nav>
@endif
