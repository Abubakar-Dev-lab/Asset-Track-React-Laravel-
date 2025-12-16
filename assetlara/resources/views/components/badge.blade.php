@props(['type' => 'default', 'size' => 'md', 'variant' => 'solid'])

@php
$solidColors = [
    'available' => 'bg-green-600 text-white',
    'assigned' => 'bg-blue-600 text-white',
    'broken' => 'bg-red-600 text-white',
    'maintenance' => 'bg-yellow-600 text-white',
    'admin' => 'bg-purple-600 text-white',
    'employee' => 'bg-gray-600 text-white',
    'active' => 'bg-green-600 text-white',
    'inactive' => 'bg-red-600 text-white',
    'default' => 'bg-gray-600 text-white',
];

$lightColors = [
    'available' => 'bg-green-100 text-green-800',
    'assigned' => 'bg-blue-100 text-blue-800',
    'broken' => 'bg-red-100 text-red-800',
    'maintenance' => 'bg-yellow-100 text-yellow-800',
    'admin' => 'bg-purple-100 text-purple-800',
    'employee' => 'bg-gray-100 text-gray-800',
    'active' => 'bg-green-100 text-green-800',
    'inactive' => 'bg-red-100 text-red-800',
    'default' => 'bg-gray-100 text-gray-800',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-3 py-1 text-lg',
];

$colors = $variant === 'light' ? $lightColors : $solidColors;
$color = $colors[$type] ?? $colors['default'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full $color $sizeClass"]) }}>
    {{ $slot }}
</span>
