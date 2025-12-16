@extends('layouts.app')

@section('content')
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Asset Inventory</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Manage all company assets from this dashboard</p>
            </div>
            <a href="{{ route('assets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-center text-sm sm:text-base">
                + Add New Asset
            </a>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block lg:hidden space-y-4">
        @foreach ($assets as $asset)
            <x-card :padding="true">
                <div class="flex items-start space-x-4">
                    @if($asset->image_path)
                        <img src="{{ asset('storage/' . $asset->image_path) }}" alt="{{ $asset->name }}"
                            class="w-16 h-16 object-cover rounded-md flex-shrink-0">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center flex-shrink-0" aria-hidden="true">
                            <span class="text-gray-400 text-xs">No image</span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 truncate">{{ $asset->name }}</h3>
                                <p class="text-xs text-gray-500 font-mono">{{ $asset->serial_number }}</p>
                            </div>
                            <x-badge :type="$asset->status" variant="light" size="sm">
                                {{ ucfirst($asset->status) }}
                            </x-badge>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $asset->category->name }}</p>
                        <div class="flex items-center space-x-4 mt-3">
                            <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</a>
                            <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($assets as $asset)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset->image_path)
                                    <img src="{{ asset('storage/' . $asset->image_path) }}" alt="{{ $asset->name }}"
                                        class="w-16 h-16 object-cover rounded-md">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center" aria-hidden="true">
                                        <span class="text-gray-400 text-xs">No image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $asset->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-600">{{ $asset->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $asset->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $asset->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :type="$asset->status" variant="light" size="sm">
                                    {{ ucfirst($asset->status) }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this asset?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $assets->links() }}
    </div>
@endsection
