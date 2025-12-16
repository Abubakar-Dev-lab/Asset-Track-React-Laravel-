@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Navigation -->
    <nav aria-label="Breadcrumb" class="mb-4">
        <a href="{{ route('assets.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to All Assets
        </a>
    </nav>

    <x-card class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Add New Asset</h1>

        <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <x-form-input
                    name="name"
                    label="Asset Name (e.g., MacBook Pro M2)"
                    :required="true"
                />

                <x-form-input
                    name="serial_number"
                    label="Serial Number (Unique)"
                    :required="true"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <x-form-select
                        name="category_id"
                        label="Category"
                        :options="$categories->pluck('name', 'id')"
                        placeholder="-- Select Category --"
                        :required="true"
                    />

                    <x-form-select
                        name="status"
                        label="Initial Status"
                        :options="['available' => 'Available', 'maintenance' => 'Maintenance', 'broken' => 'Broken']"
                        :placeholder="false"
                        :required="true"
                    />
                </div>

                <!-- Image Upload (custom - not using component due to file type) -->
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Asset Image (Optional)
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                        aria-describedby="image-help image-error">
                    <p id="image-help" class="mt-1 text-xs text-gray-500">Accepted formats: JPEG, PNG, WEBP. Max size: 2MB</p>
                    @error('image')
                        <p id="image-error" class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <x-button type="submit" variant="success">
                    Save Asset
                </x-button>
            </div>
        </form>
    </x-card>
@endsection
