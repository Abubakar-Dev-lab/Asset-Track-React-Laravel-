@extends('layouts.app')

@section('content')
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">User Management</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Manage system users and their roles</p>
            </div>
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-center text-sm sm:text-base">
                + Add New User
            </a>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block lg:hidden space-y-4">
        @foreach ($users as $user)
            <x-card class="{{ $user->is(auth()->user()) ? 'ring-2 ring-blue-500' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-medium text-gray-900">{{ $user->name }}</h3>
                            @if($user->is(auth()->user()))
                                <span class="text-xs text-blue-600">(You)</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <x-badge :type="$user->role" variant="light" size="sm">
                            {{ ucfirst($user->role) }}
                        </x-badge>
                        <x-badge :type="$user->is_active ? 'active' : 'inactive'" variant="light" size="sm">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <span class="text-xs text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</span>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</a>
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                        @can('delete-user', $user)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                            </form>
                        @endcan
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr class="{{ $user->is(auth()->user()) ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                {{ $user->name }}
                                @if($user->is(auth()->user()))
                                    <span class="text-xs text-blue-600 ml-1">(You)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :type="$user->role" variant="light" size="sm">
                                    {{ ucfirst($user->role) }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-badge :type="$user->is_active ? 'active' : 'inactive'" variant="light" size="sm">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $user->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                @can('delete-user', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
