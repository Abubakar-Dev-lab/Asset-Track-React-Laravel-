@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">My Current Assets</h1>
            <p class="text-gray-600 mt-1">View all assets currently assigned to you</p>
        </div>

        <x-card :padding="false">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Since</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($myAssets as $asset)
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
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $asset->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-600">{{ $asset->serial_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $asset->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $asset->active_assignment->assigned_at->format('Y-m-d') }}
                                    <span class="text-gray-400 text-xs block">
                                        ({{ $asset->active_assignment->assigned_at->diffForHumans() }})
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    You do not have any active assets checked out.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@endsection
