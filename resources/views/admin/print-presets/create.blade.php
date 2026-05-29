<x-admin-layout title="New Print Preset">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2"><a href="{{ route('admin.print-presets.index') }}" class="hover:text-gray-700">Print Presets</a></li>
        <li class="before:content-['/'] before:mx-2">New</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <h1 class="text-2xl font-bold text-gray-900">New Print Preset</h1>
        @include('admin.print-presets._form', ['preset' => $preset, 'action' => route('admin.print-presets.store')])
    </div>
</x-admin-layout>
