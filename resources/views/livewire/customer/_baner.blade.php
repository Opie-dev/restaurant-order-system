 <!-- Store Cover Image (mobile/desktop logic) -->
@php
    $mobileCover = $store && $store->cover_path ? Storage::url($store->cover_path) : null;
@endphp
    
@if($store)
    <div class="relative shadow {{ $mobileCover ? 'h-64 lg:h-80 overflow-hidden' : 'bg-gray-900' }}">
        {{-- Background --}}
        @if($mobileCover)
            <div class="inset-0 h-full w-full">
                <img src="{{ $mobileCover }}" alt="{{ $store->name }} cover"
                    class="w-full h-full object-cover block">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
            </div>
        @endif

        {{-- Navigation --}}
        <div class="absolute top-4 right-4">
            <nav class="flex items-center space-x-4">
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 text-white hover:bg-opacity-20 px-3 py-2 rounded-lg transition-colors font-medium backdrop-blur-sm focus:outline-none">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg py-1 z-20">
                            <a href="{{ route('menu.store.index', $store->slug) }}" class="dropdown-item">Menu</a>
                            <a href="{{ route('menu.store.cart', $store->slug) }}" class="dropdown-item">My Cart</a>
                            <a href="{{ route('menu.store.orders', $store->slug) }}" class="dropdown-item">My Orders</a>
                            <a href="{{ route('menu.store.addresses', $store->slug) }}" class="dropdown-item">My Addresses</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100 text-sm">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('menu.store.login', $store->slug) }}"
                    class="flex items-center gap-2 text-white hover:bg-dark hover:bg-opacity-20 px-3 py-2 rounded-lg transition-colors font-medium backdrop-blur-sm">
                        Login
                    </a>
                @endauth
            </nav>
        </div>

        {{-- Store Info --}}
        <div class="{{ $mobileCover ? 'absolute bottom-0 left-0 right-0 p-6 text-white' : 'relative flex items-center space-x-6 p-6 w-full' }}"
            style="{{ $mobileCover ? 'text-shadow: 2px 2px 4px rgba(0,0,0,0.8)' : '' }}">
            @if($store->logo_path)
                <img src="{{ Storage::url($store->logo_path) }}" alt="{{ $store->name }}"
                    class="hidden sm:block h-16 w-16 bg-white bg-opacity-90 p-2 shadow-md object-contain rounded-lg ">
            @else
                <div class="{{ $mobileCover ? 'sm:block h-16 w-16 bg-white bg-opacity-90' : 'h-20 w-20' }} rounded-lg flex items-center justify-center shadow-md">
                    <svg class="{{ $mobileCover ? 'h-8 w-8' : 'h-10 w-10' }} text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 8h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            @endif

            <div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">
                        {{ $store->name }}
                    </h1>
                    @if($store->isCurrentlyOpen())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white self-start"> <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path> </svg> Open Now </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white self-start"> <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8 7l7 7" clip-rule="evenodd"></path> </svg> Closed </span>
                    @endif
                </div>
                <p class="text-white text-sm">{{ $store->address }}</p>
                @if(!$store->isCurrentlyOpen() && $store->getNextOpeningTime())
                    <p class="text-white text-xs mt-1">{{ $store->getNextOpeningTime() }}</p>
                @endif
            </div>
        </div>
    </div>
@endif



