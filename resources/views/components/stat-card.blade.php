@props(['label', 'value', 'icon' => null, 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'yellow' => 'bg-yellow-50 text-yellow-600',
        'red' => 'bg-red-50 text-red-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'gray' => 'bg-gray-50 text-gray-600',
        'pink' => 'bg-pink-50 text-pink-600',
    ];
    $cls = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="overflow-hidden rounded-lg bg-white shadow">
    <div class="p-5">
        <div class="flex items-center">
            @if($icon)
                <div class="flex-shrink-0">
                    <div class="rounded-md p-3 {{ $cls }}">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                    </div>
                </div>
            @endif
            <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
                <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">{{ $label }}</dt>
                    <dd class="text-2xl font-semibold text-gray-900">{{ $value }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
