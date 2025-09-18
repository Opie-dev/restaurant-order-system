<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900" x-data="{ flash: null }" x-init="window.addEventListener('flash', e => flash = e.detail)">
    <div class="border-b bg-white">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-semibold">Restaurant Admin</a>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('admin.menu.index') }}" class="hover:underline">Menu</a>
                    <a href="{{ route('admin.categories.index') }}" class="hover:underline">Categories</a>
                    <a href="{{ route('admin.orders.index') }}" class="hover:underline">Orders</a>
                    <a href="{{ route('admin.customers.index') }}" class="hover:underline">Customers</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="hover:underline">Register</a>
                @endauth
            </nav>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <template x-if="flash">
        <div class="fixed bottom-4 right-4 bg-black text-white px-4 py-2 rounded" x-text="flash"></div>
    </template>
    @livewireScripts
</body>
</html>
