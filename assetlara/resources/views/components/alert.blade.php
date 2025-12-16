@props(['type' => 'info', 'dismissible' => false])

@php
$styles = [
    'success' => 'bg-green-100 border-green-500 text-green-700',
    'error' => 'bg-red-100 border-red-500 text-red-700',
    'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
    'info' => 'bg-blue-100 border-blue-500 text-blue-700',
];
$style = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-3 sm:p-4 mb-4 $style", 'role' => 'alert']) }}>
    <div class="flex items-center justify-between">
        <p>{{ $slot }}</p>
        @if($dismissible)
            <button type="button" onclick="this.parentElement.parentElement.remove()"
                    class="ml-4 text-current opacity-70 hover:opacity-100"
                    aria-label="Dismiss alert">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
