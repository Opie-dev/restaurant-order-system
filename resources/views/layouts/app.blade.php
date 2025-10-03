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
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.menu.index') }}" class="hover:underline">Menu</a>
                        <a href="{{ route('admin.categories.index') }}" class="hover:underline">Categories</a>
                        <a href="{{ route('admin.orders.index') }}" class="hover:underline">Orders</a>
                        <a href="{{ route('admin.customers.index') }}" class="hover:underline">Customers</a>
                        
                        <!-- Store Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center gap-1 hover:underline"
                            >
                                <span x-text="'{{ app(\App\Services\Admin\StoreService::class)->getCurrentStore()?->name ?? 'Select Store' }}'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                            >
                                @php
                                    $stores = app(\App\Services\Admin\StoreService::class)->getUserStores();
                                    $currentStore = app(\App\Services\Admin\StoreService::class)->getCurrentStore();
                                @endphp
                                
                                @foreach($stores as $store)
                                    <a 
                                        href="{{ route('admin.stores.select') }}?store={{ $store->id }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentStore && $currentStore->id === $store->id ? 'bg-blue-50 text-blue-700' : '' }}"
                                    >
                                        {{ $store->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="hover:underline">Register</a>
                    <a href="{{ route('subscribe') }}" class="hover:underline">Subscribe</a>
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
