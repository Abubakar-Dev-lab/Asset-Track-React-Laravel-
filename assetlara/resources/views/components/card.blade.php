@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white shadow sm:rounded-lg']) }}>
    @if($title)
        <div class="px-4 py-3 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        </div>
    @endif

    <div @class(['p-4 sm:p-6' => $padding])>
        {{ $slot }}
    </div>
</div>
