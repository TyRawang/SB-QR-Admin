<x-admin-layout title="Feedback">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Feedback</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Feedback</h1>
            <p class="text-sm text-gray-500">{{ $total }} total</p>
        </div>

        <div>
            <select name="rating"
                    hx-get="{{ route('admin.feedback.index') }}"
                    hx-target="#feedback-list"
                    class="rounded-md border-0 py-2 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ $ratingFilter === (string) $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                @endfor
                <option value="unrated" {{ $ratingFilter === 'unrated' ? 'selected' : '' }}>Unrated</option>
            </select>
        </div>

        <div id="feedback-list">
            @include('admin.feedback._list')
        </div>
    </div>
</x-admin-layout>
