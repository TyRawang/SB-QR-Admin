<x-admin-layout title="Users">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2">Users</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <p class="text-sm text-gray-500">{{ $total }} total</p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-search-input placeholder="Search by name or email..." :value="$search"
                    hx-target="#user-list" :hx-url="route('admin.users.index')" />
            </div>
            <div>
                <select name="role"
                        hx-get="{{ route('admin.users.index') }}"
                        hx-target="#user-list"
                        hx-include="[name='search'],[name='active']"
                        class="rounded-md border-0 py-2 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                    <option value="">All Roles</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ $roleFilter === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div>
                <select name="active"
                        hx-get="{{ route('admin.users.index') }}"
                        hx-target="#user-list"
                        hx-include="[name='search'],[name='role']"
                        class="rounded-md border-0 py-2 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600">
                    <option value="">All Status</option>
                    <option value="true" {{ $activeFilter === 'true' ? 'selected' : '' }}>Active</option>
                    <option value="false" {{ $activeFilter === 'false' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        {{-- User List --}}
        <div id="user-list">
            @include('admin.users._list')
        </div>
    </div>
</x-admin-layout>
