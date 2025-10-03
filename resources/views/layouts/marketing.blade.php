<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant') }} – Early Access</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-b from-white to-gray-50 text-gray-900">
    <header class="border-b bg-white/80 backdrop-blur">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded bg-blue-600 text-white">R</span>
                <span>{{ config('app.name', 'Restaurant') }}</span>
            </a>
            <nav class="text-sm">
                <a href="{{ route('menu') }}" class="hover:underline">Menu</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t bg-white">
        <div class="max-w-5xl mx-auto px-4 py-6 text-sm text-gray-600 flex items-center justify-between">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Restaurant') }}. All rights reserved.</p>
            <a href="{{ route('subscribe') }}" class="hover:underline">Early Access</a>
        </div>
    </footer>
    @livewireScripts
</body>
</html>


