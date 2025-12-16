<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AssetGuard - Asset Management System">
    <title>{{ config('app.name', 'AssetGuard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    {{-- <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 bg-blue-600 text-white px-4 py-2 z-50">
        Skip to main content
    </a> --}}

    <!-- Navigation -->
    <nav class="bg-white shadow mb-4 sm:mb-8" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Desktop Navigation -->
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ auth()->check() ? (Gate::allows('admin') ? route('dashboard') : route('my-assets')) : route('login') }}"
                       class="text-lg sm:text-xl font-bold text-blue-600 hover:text-blue-700">
                        AssetGuard
                    </a>

                    @auth
                        <!-- Desktop Menu -->
                        <div class="hidden md:flex md:items-center md:space-x-6 md:ml-8">
                            @can('admin')
                                <a href="{{ route('dashboard') }}"
                                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                                   @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                                    Dashboard
                                </a>
                                <a href="{{ route('assets.index') }}"
                                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('assets.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                                   @if(request()->routeIs('assets.*')) aria-current="page" @endif>
                                    Assets
                                </a>
                                <a href="{{ route('users.index') }}"
                                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('users.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                                   @if(request()->routeIs('users.*')) aria-current="page" @endif>
                                    Users
                                </a>
                                <a href="{{ route('categories.index') }}"
                                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('categories.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                                   @if(request()->routeIs('categories.*')) aria-current="page" @endif>
                                    Categories
                                </a>
                                <a href="{{ route('assets.create') }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    + Add Asset
                                </a>
                            @else
                                <a href="{{ route('my-assets') }}"
                                   class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('my-assets') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                                   @if(request()->routeIs('my-assets')) aria-current="page" @endif>
                                    My Assets
                                </a>
                            @endcan
                        </div>
                    @endauth
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600 hidden sm:block">
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                            <x-badge :type="auth()->user()->role" size="sm" class="ml-1">
                                {{ ucfirst(auth()->user()->role) }}
                            </x-badge>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm sm:text-base">
                                Logout
                            </button>
                        </form>
                        <!-- Mobile menu button -->
                        <button type="button"
                                id="mobile-menu-btn"
                                class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-controls="mobile-menu"
                                aria-expanded="false"
                                aria-label="Toggle navigation menu">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Login
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            @auth
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    @can('admin')
                        <a href="{{ route('dashboard') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
                           @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                            Dashboard
                        </a>
                        <a href="{{ route('assets.index') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('assets.*') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
                           @if(request()->routeIs('assets.*')) aria-current="page" @endif>
                            Assets
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('users.*') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
                           @if(request()->routeIs('users.*')) aria-current="page" @endif>
                            Users
                        </a>
                        <a href="{{ route('categories.index') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('categories.*') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
                           @if(request()->routeIs('categories.*')) aria-current="page" @endif>
                            Categories
                        </a>
                        <a href="{{ route('assets.create') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium bg-blue-600 text-white text-center">
                            + Add Asset
                        </a>
                    @else
                        <a href="{{ route('my-assets') }}"
                           class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('my-assets') ? 'bg-blue-100 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
                           @if(request()->routeIs('my-assets')) aria-current="page" @endif>
                            My Assets
                        </a>
                    @endcan
                    <a href="{{ route('profile.edit') }}"
                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                        Profile ({{ auth()->user()->name }})
                    </a>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8" role="main">
        <!-- Flash Messages -->
        @if (session('success'))
            <x-alert type="success" dismissible>
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" dismissible>
                {{ session('error') }}
            </x-alert>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    const isExpanded = mobileMenu.classList.toggle('hidden');
                    mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
                });
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
