@props([
    'name',
    'label',
    'options' => [],
    'selected' => '',
    'placeholder' => '-- Select --',
    'required' => false,
    'disabled' => false,
])

<div {{ $attributes->only('class')->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->except('class')->merge(['class' => 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500' . ($disabled ? ' bg-gray-100' : '')]) }}
        aria-describedby="{{ $name }}-error"
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $label)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
    @enderror
</div>
