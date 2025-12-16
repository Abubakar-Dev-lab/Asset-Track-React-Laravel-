@extends('layouts.app')

@section('content')
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1 text-sm sm:text-base">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8" role="region" aria-label="Dashboard Statistics">
        <!-- Total Assets -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-2 sm:p-3">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Assets</dt>
                        <dd class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalAssets }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Available Assets -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-2 sm:p-3">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Available</dt>
                        <dd id="available-count" class="text-xl sm:text-2xl font-bold text-gray-900">{{ $availableAssets }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Assigned Assets -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2 sm:p-3">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Assigned</dt>
                        <dd id="assigned-count" class="text-xl sm:text-2xl font-bold text-gray-900">{{ $assignedAssets }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Employees -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-2 sm:p-3">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Employees</dt>
                        <dd class="text-xl sm:text-2xl font-bold text-gray-900">{{ $totalEmployees }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <x-card class="mb-6 sm:mb-8">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <a href="{{ route('assets.index') }}"
               class="flex items-center p-3 sm:p-4 border border-gray-300 rounded-lg hover:border-blue-500 hover:shadow-md transition">
                <svg class="h-6 w-6 sm:h-8 sm:w-8 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base">View All Assets</h3>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Browse and manage inventory</p>
                </div>
            </a>

            <a href="{{ route('assets.create') }}"
               class="flex items-center p-3 sm:p-4 border border-gray-300 rounded-lg hover:border-green-500 hover:shadow-md transition">
                <svg class="h-6 w-6 sm:h-8 sm:w-8 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base">Add New Asset</h3>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Register new equipment</p>
                </div>
            </a>

            <a href="{{ route('users.index') }}"
               class="flex items-center p-3 sm:p-4 border border-gray-300 rounded-lg hover:border-purple-500 hover:shadow-md transition sm:col-span-2 lg:col-span-1">
                <svg class="h-6 w-6 sm:h-8 sm:w-8 text-purple-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base">Manage Users</h3>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">View and manage employees</p>
                </div>
            </a>
        </div>
    </x-card>

    <!-- Recent Activity -->
    <x-card>
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">Recent Assignments</h2>
        <div id="recent-assignments-list">
            @if($recentAssignments->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach($recentAssignments as $assignment)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-gray-50 rounded-lg gap-2 sm:gap-4">
                            <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                                <div class="flex-shrink-0">
                                    @if($assignment->returned_at)
                                        <x-badge type="available" variant="light" size="sm">Returned</x-badge>
                                    @else
                                        <x-badge type="assigned" variant="light" size="sm">Active</x-badge>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $assignment->asset->name }}
                                        <span class="hidden sm:inline text-gray-500">({{ $assignment->asset->serial_number }})</span>
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-600 truncate">
                                        Assigned to <span class="font-medium">{{ $assignment->user->name }}</span>
                                        <span class="hidden sm:inline">by {{ $assignment->admin->name }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right ml-10 sm:ml-0 flex-shrink-0">
                                <p class="text-xs sm:text-sm text-gray-600">{{ $assignment->assigned_at->diffForHumans() }}</p>
                                @if($assignment->returned_at)
                                    <p class="text-xs text-gray-500">Returned {{ $assignment->returned_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-6 sm:py-8 text-sm sm:text-base">No assignment history yet.</p>
            @endif
        </div>
    </x-card>

    @push('scripts')
    <script>
        // Real-time dashboard updates
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                window.Echo.channel('dashboard')
                    .listen('.AssetAssigned', (e) => {
                        console.log('Dashboard: Asset assigned', e);
                        updateCounts('assigned');
                        addNewAssignment(e, 'assigned');
                    })
                    .listen('.AssetReturned', (e) => {
                        console.log('Dashboard: Asset returned', e);
                        updateCounts('returned');
                    });
            }
        });

        function updateCounts(action) {
            const availableEl = document.getElementById('available-count');
            const assignedEl = document.getElementById('assigned-count');

            if (availableEl && assignedEl) {
                let available = parseInt(availableEl.textContent);
                let assigned = parseInt(assignedEl.textContent);

                if (action === 'assigned') {
                    availableEl.textContent = available - 1;
                    assignedEl.textContent = assigned + 1;
                } else if (action === 'returned') {
                    availableEl.textContent = available + 1;
                    assignedEl.textContent = assigned - 1;
                }

                // Add a brief highlight effect
                availableEl.classList.add('text-green-600');
                assignedEl.classList.add('text-yellow-600');
                setTimeout(() => {
                    availableEl.classList.remove('text-green-600');
                    assignedEl.classList.remove('text-yellow-600');
                }, 1000);
            }
        }

        function addNewAssignment(data, status) {
            const list = document.getElementById('recent-assignments-list');
            if (!list) return;

            const statusBadge = status === 'assigned'
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Active</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Returned</span>';

            const newItem = document.createElement('div');
            newItem.className = 'flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400 gap-2 sm:gap-4';
            newItem.innerHTML = `
                <div class="flex items-start sm:items-center space-x-3 sm:space-x-4">
                    <div class="flex-shrink-0">${statusBadge}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">${data.asset_name}</p>
                        <p class="text-xs sm:text-sm text-gray-600">Assigned to <span class="font-medium">${data.user_name}</span></p>
                    </div>
                </div>
                <div class="text-left sm:text-right ml-10 sm:ml-0 flex-shrink-0">
                    <p class="text-xs sm:text-sm text-gray-600">Just now</p>
                    <p class="text-xs text-green-600 font-medium">NEW</p>
                </div>
            `;

            // Insert at the top
            const firstChild = list.querySelector('.space-y-3, .space-y-4');
            if (firstChild) {
                firstChild.insertBefore(newItem, firstChild.firstChild);
                // Remove the last item if more than 5
                const items = firstChild.querySelectorAll('.flex.flex-col, .flex.items-center');
                if (items.length > 5) {
                    items[items.length - 1].remove();
                }
            }

            // Remove highlight after 3 seconds
            setTimeout(() => {
                newItem.classList.remove('bg-yellow-50', 'border-l-4', 'border-yellow-400');
                newItem.classList.add('bg-gray-50');
                const newBadge = newItem.querySelector('.text-green-600.font-medium');
                if (newBadge) newBadge.remove();
            }, 3000);
        }
    </script>
    @endpush
@endsection
