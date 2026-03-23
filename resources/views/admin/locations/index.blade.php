<x-admin-layout title="Locations">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Locations</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Locations</h1>
            <p class="text-sm text-gray-500">{{ $total }} total</p>
        </div>

        <div class="flex-1 max-w-md">
            <x-search-input placeholder="Search locations..." :value="$search"
                hx-target="#location-list" :hx-url="route('admin.locations.index')" />
        </div>

        <div id="location-list">
            @include('admin.locations._list')
        </div>
    </div>
</x-admin-layout>
