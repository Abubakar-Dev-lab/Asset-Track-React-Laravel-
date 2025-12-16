@props(['href', 'active' => false])

@php
$classes = $active
    ? 'bg-blue-700 text-white px-4 py-2 rounded-lg font-medium'
    : 'text-blue-100 hover:bg-blue-700 hover:text-white px-4 py-2 rounded-lg font-medium transition-colors';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => $classes]) }}
   @if($active) aria-current="page" @endif>
    {{ $slot }}
</a>
