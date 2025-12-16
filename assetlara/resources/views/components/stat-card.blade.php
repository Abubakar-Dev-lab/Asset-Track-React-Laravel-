@props(['title', 'value', 'icon' => null, 'color' => 'blue'])

@php
$colors = [
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'red' => 'bg-red-500',
    'yellow' => 'bg-yellow-500',
    'purple' => 'bg-purple-500',
    'gray' => 'bg-gray-500',
];
$bgColor = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6']) }}>
    <div class="flex items-center">
        @if($icon)
            <div class="{{ $bgColor }} rounded-full p-3 mr-4">
                {{ $icon }}
            </div>
        @endif
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
        </div>
    </div>
    @if($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
