<x-admin-layout title="Edit Print Preset">
    <x-slot:breadcrumbs>
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a></li>
        <li class="before:content-['/'] before:mx-2"><a href="{{ route('admin.print-presets.index') }}" class="hover:text-gray-700">Print Presets</a></li>
        <li class="before:content-['/'] before:mx-2">{{ $preset->name }}</li>
    </x-slot:breadcrumbs>

    <div class="space-y-4">
        <h1 class="text-2xl font-bold text-gray-900">Edit Preset: {{ $preset->name }}</h1>
        @include('admin.print-presets._form', [
            'preset' => $preset,
            'action' => route('admin.print-presets.update', $preset),
            'method' => 'PATCH',
        ])
    </div>
</x-admin-layout>
