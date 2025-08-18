<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KickPredict') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="text-white font-bold text-xl">KickPredict</a>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('home') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Home</a>
                            @auth
                                @if(auth()->user() && auth()->user()->is_admin)
                                    <a href="{{ route('admin') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Admin</a>
                                    <a href="{{ route('manage-results') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Manage Results</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                @auth
                    @if(auth()->user()->is_admin)
                        <div class="hidden md:block">
                            <div class="ml-4 flex items-center md:ml-6">
                                <div class="text-gray-300 text-sm mr-4">
                                    Admin: {{ auth()->user()->name }}
                                </div>
                                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    @livewire('footer')

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html> 