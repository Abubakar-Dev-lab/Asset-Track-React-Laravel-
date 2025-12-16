@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-4 flex items-center space-x-2 text-sm">
        <a href="{{ route('assets.index') }}" class="text-blue-600 hover:text-blue-800">All Assets</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('assets.show', $asset) }}" class="text-blue-600 hover:text-blue-800">{{ $asset->name }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-600">Edit</span>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Asset: {{ $asset->name }}</h2>

        <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- 🟢 METHOD SPOOFING: Laravel requires this hidden input for PUT/PATCH requests --}}
            @method('PUT')

            <div class="space-y-6">
                <!-- Asset Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Asset Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number (Unique Check will ignore this asset's ID) -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number
                        (Unique)</label>
                    <input type="text" name="serial_number" id="serial_number"
                        value="{{ old('serial_number', $asset->serial_number) }}" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    @error('serial_number')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Category ID (Foreign Key Dropdown) -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            @foreach (['available', 'assigned', 'maintenance', 'broken'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $asset->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Image Display -->
                @if($asset->image_path)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <img src="{{ asset('storage/' . $asset->image_path) }}" alt="{{ $asset->name }}"
                        class="w-48 h-48 object-cover rounded-lg border border-gray-300">
                </div>
                @endif

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">
                        {{ $asset->image_path ? 'Replace Image (Optional)' : 'Upload Image (Optional)' }}
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: JPEG, PNG, WEBP. Max size: 2MB</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                    Update Asset
                </button>

                <a href="{{ route('assets.show', $asset) }}" class="text-gray-600 hover:underline py-2 px-4">Cancel</a>
            </div>
        </form>
    </div>
@endsection
