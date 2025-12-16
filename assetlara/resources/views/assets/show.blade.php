@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-4">
        <a href="{{ route('assets.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to All Assets
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow sm:rounded-lg p-6">

        <div class="flex justify-between items-start">
            <h1 class="text-3xl font-bold text-gray-800">{{ $asset->name }} (SN: {{ $asset->serial_number }})</h1>
            <x-badge :type="$asset->status" size="lg">
                {{ ucfirst($asset->status) }}
            </x-badge>
        </div>

        <div class="mt-4 border-t border-gray-200 pt-4">
            <p class="text-gray-600">Category: <span class="font-medium text-gray-800">{{ $asset->category->name }}</span></p>

            @if($asset->image_path)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Asset Image</label>
                <img src="{{ asset('storage/' . $asset->image_path) }}" alt="{{ $asset->name }}"
                    class="w-64 h-64 object-cover rounded-lg border border-gray-300 shadow-sm">
            </div>
            @endif
        </div>

        {{-- 🟢 CONDITIONAL ACTION BLOCK --}}
        <div class="mt-8 p-4 border border-gray-300 rounded-lg">

            @if ($asset->status === 'available')
                <h3 class="text-xl font-semibold mb-3">Assign Asset</h3>
                <form action="{{ route('assets.assign', $asset) }}" method="POST" class="flex gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Select Employee</label>
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option value="">-- Select Active Employee --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        Check Out
                    </button>
                </form>
            @elseif ($asset->status === 'assigned')
                @php
                    // Find the currently active assignment
                    $currentAssignment = $asset->assignments()->whereNull('returned_at')->first();
                @endphp
                @if($currentAssignment)
                    <h3 class="text-xl font-semibold mb-3 text-red-700">Currently Assigned</h3>
                    <p class="text-gray-800">Holder: <span class="font-bold">{{ $currentAssignment->user->name }}</span></p>
                    <p class="text-gray-600">Assigned Since: {{ $currentAssignment->assigned_at->diffForHumans() }}</p>

                    <form action="{{ route('assets.return', $asset) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure the asset has been physically returned?')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow">
                            Check In (Mark as Available)
                        </button>
                    </form>
                @else
                    <p class="text-xl text-yellow-600">Asset is marked as assigned but no active assignment found.</p>
                @endif
            @else
                <p class="text-xl text-gray-500">No actions available for {{ $asset->status }} asset.</p>
            @endif

        </div>

        {{-- 🟢 HISTORY LOG --}}
        <h3 class="text-2xl font-bold mt-10 mb-4 text-gray-800">Assignment History</h3>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned
                            At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned
                            At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned
                            By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($asset->assignments()->orderByDesc('assigned_at')->get() as $assignment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->assigned_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($assignment->returned_at)
                                    {{ $assignment->returned_at->format('Y-m-d') }}
                                @else
                                    <span class="text-blue-500 font-semibold">CURRENT</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $assignment->admin?->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No assignment history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
