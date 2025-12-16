@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Categories
        </a>
    </div>

    <div class="max-w-2xl mx-auto bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Category</h2>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    <p class="mt-1 text-xs text-gray-500">Current slug: {{ $category->slug }}</p>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                    Update Category
                </button>
            </div>
        </form>
    </div>
@endsection
