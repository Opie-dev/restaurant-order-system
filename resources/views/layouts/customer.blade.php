<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 flex flex-col" x-data="{ flash: null, flashType: null }" x-init="window.addEventListener('flash', e => { let d = e.detail; // Livewire may wrap payloads as [payload] when not using named args
    if (d && typeof d === 'object' && d !== null && !('message' in d) && 0 in d) { d = d[0]; }
    if (typeof d === 'object' && d !== null) { flash = d.message ?? ''; flashType = d.type ?? null; } else { flash = d ?? ''; flashType = null; } })">
    <!-- Header -->
    @if(!request()->routeIs('menu.store'))
        <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo and Brand -->
                
                <a href="{{ route('menu') }}" class="flex items-center gap-3 group">
                    <svg class="w-8 h-8 text-purple-600 group-hover:text-purple-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800 group-hover:text-gray-900 transition-colors">Gourmet Express</h1>
                </a>

                <!-- Navigation -->
                <nav class="flex items-center gap-6 text-sm">
                    <a href="{{ route('menu') }}" class="text-gray-600 hover:text-gray-800 transition-colors font-medium">Menu</a>  
                    @php
                        try {
                            $cartCount = app(\App\Services\CartService::class)->current()->items()->sum('qty');
                        } catch (\Throwable $e) {
                            $cartCount = 0;
                        }
                    @endphp
                    <a href="{{ route('cart') }}" class="relative flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        <span class="font-medium hidden sm:inline">Cart</span>
                        @php $displayCount = ($cartCount ?? 0) > 99 ? '99+' : ($cartCount ?? 0); @endphp
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-2 -right-3 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold leading-none text-white bg-purple-600 rounded-full ring-2 ring-white shadow">{{ $displayCount }}</span>
                        @endif
                    </a>
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-medium">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                                    <div class="font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-gray-500">{{ Auth::user()->email }}</div>
                                </div>
                                <a href="{{ route('orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        My Orders
                                    </div>
                                </a>
                                <a href="{{ route('addresses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2M9 20H4v-2a3 3 0 013-3h2m7-4a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        My Addresses
                                    </div>
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 transition-colors font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors font-medium">Register</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>
    @endif

    <!-- Main Content -->
    <main class="flex-1 overflow-hidden">
        {{ $slot ?? '' }}
        @yield('content')
    </main>


    <template x-if="flash">
        <div class="fixed bottom-4 right-4 text-white px-4 py-3 rounded-lg z-50 shadow-lg min-w-64"
             :class="flashType === 'error' ? 'bg-red-600' : 'bg-black'"
             x-data="{ 
                 progress: 100,
                 duration: 3000,
                 startTime: Date.now()
             }"
             x-init="
                 const timer = setInterval(() => {
                     const elapsed = Date.now() - startTime;
                     const remaining = Math.max(0, duration - elapsed);
                     progress = (remaining / duration) * 100;
                     if (remaining <= 0) {
                         clearInterval(timer);
                         flash = null;
                     }
                 }, 50);
                 setTimeout(() => flash = null, duration);
             "
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2">
            
            <!-- Toast Content -->
            <div class="flex items-center justify-between mb-2">
                <span x-text="flash" class="text-sm font-medium"></span>
                <button @click="flash = null" class="text-gray-300 hover:text-white ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full rounded-full h-1" :class="flashType === 'error' ? 'bg-red-700' : 'bg-gray-700'">
                <div class="bg-white h-1 rounded-full transition-all duration-50 ease-linear" 
                     :style="`width: ${progress}%`"></div>
            </div>
        </div>
    </template>
    @livewireScripts
</body>
</html>
