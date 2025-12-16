@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Users
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- User Info Card -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                </div>
                <div class="flex space-x-2">
                    <x-badge :type="$user->role" variant="light">
                        {{ $user->role === 'admin' ? 'Administrator' : 'Employee' }}
                    </x-badge>
                    <x-badge :type="$user->is_active ? 'active' : 'inactive'" variant="light">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
            </div>

            <div class="border-t pt-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">User ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registered</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->diffForHumans() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Assignments</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->assignments->count() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 flex space-x-4">
                <a href="{{ route('users.edit', $user) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit User
                </a>
                @can('delete-user', $user)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">
                            Delete User
                        </x-button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Assignment History -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Asset Assignment History</h2>

            @if($user->assignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->assignments()->latest('assigned_at')->get() as $assignment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('assets.show', $assignment->asset) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $assignment->asset->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $assignment->asset->serial_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $assignment->assigned_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $assignment->returned_at ? $assignment->returned_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-badge :type="$assignment->returned_at ? 'available' : 'assigned'" variant="light" size="sm">
                                            {{ $assignment->returned_at ? 'Returned' : 'Active' }}
                                        </x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 text-center py-8">No assignment history for this user.</p>
            @endif
        </div>
    </div>
@endsection
