<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900" 
      x-data="{ 
          sidebarOpen: true, 
          sidebarCollapsed: false,
          flash: null,
        flashType: null 
      }" 
      x-init="
          if (window.innerWidth < 1024) {
              sidebarOpen = false;
          }
          
          window.addEventListener('resize', () => {
              if (window.innerWidth < 1024) {
                  sidebarOpen = false;
              } else {
                  sidebarOpen = true;
              }
          });
          
          window.addEventListener('flash', e => {
              flash = e.detail.message || e.detail;
              flashType = e.detail.type || 'success';
              setTimeout(() => flash = null, 3000);
          });
          
          $watch('sidebarOpen', value => {
              if (value && window.innerWidth < 1024) {
                  document.body.classList.add('overflow-hidden');
              } else {
                  document.body.classList.remove('overflow-hidden');
              }
          });
      ">
    
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-opacity-75 lg:hidden"
         @click="sidebarOpen = false">
    </div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 bg-white shadow-lg transform transition-all duration-300 ease-in-out"
         :class="sidebarOpen ? (window.innerWidth < 1024 ? 'translate-x-0 w-64' : 'translate-x-0 w-64') : (window.innerWidth < 1024 ? '-translate-x-full w-64' : 'translate-x-0 w-16')">
        
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                @php
                    $store = app(\App\Services\Admin\StoreService::class)->getCurrentStore();
                    $storeName = $store?->name ?? 'Restaurant Admin';
                    $storeLogo = $store?->logo_path;
                @endphp

                @if($storeLogo)
                    <div class="flex-shrink-0">
                        <img src="{{ Storage::url($storeLogo) }}" alt="{{ $storeName }}" class="h-8 w-8 object-contain rounded">
                    </div>
                @else
                    <div class="flex-shrink-0 h-8 w-8 bg-purple-100 rounded flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1 min-w-0">
                    <h1 x-show="sidebarOpen" class="text-lg font-bold text-gray-900 leading-tight break-words">{{ $storeName }}</h1>
                    {{-- <h1 x-show="!sidebarOpen" class="text-lg font-bold text-gray-900">{{ substr($storeName, 0, 2) }}</h1> --}}
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-5 px-2 space-y-1">
            <a href="{{ route('admin.dashboard') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'Dashboard'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('admin.menu.index') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.menu.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'Menu'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="sidebarOpen">Menu</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'Categories'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span x-show="sidebarOpen">Categories</span>
            </a>

            <a href="{{ route('admin.orders.pending') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.orders.pending') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'Pending Orders'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="sidebarOpen">Kitchen</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.orders.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'All Orders'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span x-show="sidebarOpen">Orders</span>
            </a>

            <a href="{{ route('admin.customers.index') }}" 
               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customers.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="sidebarOpen ? '' : 'Customers'">
                <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span x-show="sidebarOpen">Customers</span>
            </a>

            <!-- Settings with submenu -->
            <div x-data="{ settingsOpen: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                <button @click="settingsOpen = !settingsOpen" 
                        class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                        :title="sidebarOpen ? '' : 'Settings'">
                    <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen">Settings</span>
                    <svg x-show="sidebarOpen" class="ml-auto h-4 w-4 transition-transform duration-200" :class="settingsOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                
                <!-- Submenu -->
                <div x-show="settingsOpen && sidebarOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('admin.settings.store-details') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.store-details') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-4 w-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Store Details
                    </a>
                    <a href="{{ route('admin.settings.store-media') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.store-media') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-4 w-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 12h18M3 19h18" />
                        </svg>
                        Store Media
                    </a>
                    <a href="{{ route('admin.settings.store-address') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.store-address') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-4 w-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4" />
                        </svg>
                        Store Address
                    </a>
                    <a href="{{ route('admin.settings.store-hours') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.store-hours') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="h-4 w-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Opening Hours
                    </a>
                <a href="{{ route('admin.settings.security') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.security') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="h-4 w-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Security
                </a>
                </div>
            </div>

            <!-- Logout -->
            <div class="border-t border-gray-200 pt-4 mt-4">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" 
                            class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                            :title="sidebarOpen ? '' : 'Logout'">
                        <svg class="h-5 w-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span x-show="sidebarOpen">Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main content -->
    <div class="flex flex-col min-h-screen" :class="sidebarOpen ? (window.innerWidth < 1024 ? 'ml-0' : 'ml-64') : (window.innerWidth < 1024 ? 'ml-0' : 'ml-16')">
        <!-- Topbar -->
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            <!-- Sidebar toggle button -->
            <button type="button" 
                    class="-m-2.5 p-2.5 text-gray-700"
                    @click="sidebarOpen = !sidebarOpen">
                <span class="sr-only">Toggle sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                <div class="flex items-center">
                    {{-- <h1 class="text-lg font-semibold text-gray-900">Restaurant Admin</h1> --}}
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-x-4 lg:gap-x-6">
                <!-- Fullscreen Button -->
                <button type="button" 
                        onclick="toggleFullscreen()"
                        class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                        title="Toggle Fullscreen">
                    <span class="sr-only">Toggle Fullscreen</span>
                    <svg id="fullscreen-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>

                <!-- Notifications -->
                <button type="button" 
                        class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">View notifications</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>

                <!-- Store Switcher -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" 
                            class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 flex items-center"
                            @click="open = !open">
                        <span class="sr-only">Switch store</span>
                        <svg class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="hidden lg:block text-sm font-medium text-gray-700">
                            @php
                                $currentStore = app(\App\Services\Admin\StoreService::class)->getCurrentStore();
                            @endphp
                            {{ $currentStore?->name ?? 'Select Store' }}
                        </span>
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                        @php
                            $stores = app(\App\Services\Admin\StoreService::class)->getUserStores();
                            $currentStore = app(\App\Services\Admin\StoreService::class)->getCurrentStore();
                        @endphp
                        
                        @if($stores->count() > 0)
                            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Switch Store
                            </div>
                            @foreach($stores as $store)
                                <a href="{{ route('admin.stores.select') }}?store={{ $store->id }}" 
                                   class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ $currentStore && $currentStore->id === $store->id ? 'bg-blue-50 text-blue-700' : '' }}">
                                    <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium truncate">{{ $store->name }}</div>
                                        @if($store->address)
                                            <div class="text-xs text-gray-500 truncate">{{ Str::limit($store->address, 30) }}</div>
                                        @endif
                                    </div>
                                    @if($currentStore && $currentStore->id === $store->id)
                                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-sm text-gray-500">
                                No stores available
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('admin.stores.create') }}" 
                               class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Store
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Separator -->
                <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                <!-- Profile dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" 
                            class="-m-1.5 flex items-center p-1.5"
                            @click="open = !open">
                        <span class="sr-only">Open user menu</span>
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden lg:flex lg:items-center">
                            <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{ auth()->user()->name }}</span>
                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full px-3 py-1 text-left text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional Navigation Bar -->
        {{-- 
            Usage in Livewire components:
            
            return view('your-view', [
                'navigationBar' => true,                    // Enable navigation bar
                'showBackButton' => true,                   // Show back button
                'pageTitle' => 'Your Page Title',           // Page title
                'breadcrumbs' => [                          // Breadcrumb navigation
                    ['label' => 'Home', 'url' => '/admin'],
                    ['label' => 'Current Page']             // Last item has no URL
                ],
                'actionButtons' => [                        // Action buttons on the right
                    [
                        'type' => 'link',                   // or 'button'
                        'label' => 'Button Text',
                        'url' => '/some-url',              // For links
                        'onclick' => 'someFunction()',     // For buttons
                        'icon' => '<path d="..."></path>'   // Optional SVG path
                    ]
                ]
            ]);
        --}}
        @if(isset($navigationBar) && $navigationBar)
            <div class="border-b border-gray-200 bg-white px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <!-- Left side - Back button, breadcrumbs, etc. -->
                    <div class="flex items-center space-x-4">
                        @if(isset($showBackButton) && $showBackButton)
                            <button onclick="history.back()" 
                                    class="flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back
                            </button>
                        @endif
                        
                        @if(isset($breadcrumbs) && is_array($breadcrumbs))
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2">
                                    @foreach($breadcrumbs as $index => $breadcrumb)
                                        @if($index > 0)
                                            <li>
                                                <svg class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                                </svg>
                                            </li>
                                        @endif
                                        <li>
                                            @if(isset($breadcrumb['url']) && !$loop->last)
                                                <a href="{{ $breadcrumb['url'] }}" class="text-sm text-gray-500 hover:text-gray-700">
                                                    {{ $breadcrumb['label'] }}
                                                </a>
                                            @else
                                                <span class="text-sm font-medium text-gray-900">{{ $breadcrumb['label'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        @endif
                        
                        @if(isset($pageTitle) && $pageTitle)
                            <h2 class="text-lg font-semibold text-gray-900">{{ $pageTitle }}</h2>
                        @endif
                    </div>
                    
                    <!-- Right side - Action buttons -->
                    <div class="flex items-center space-x-2">
                        @if(isset($actionButtons) && is_array($actionButtons))
                            @foreach($actionButtons as $button)
                                @if(isset($button['type']) && $button['type'] === 'link')
                                    <a href="{{ $button['url'] }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        @if(isset($button['icon']))
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $button['icon'] !!}
                                            </svg>
                                        @endif
                                        {{ $button['label'] }}
                                    </a>
                                @elseif(isset($button['type']) && $button['type'] === 'button')
                                    <button type="button" 
                                            onclick="{{ $button['onclick'] ?? '' }}"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        @if(isset($button['icon']))
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $button['icon'] !!}
                                            </svg>
                                        @endif
                                        {{ $button['label'] }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Mobile header with menu button -->
        <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between transition-opacity duration-300"
             :class="sidebarOpen && window.innerWidth < 1024 ? 'opacity-50' : 'opacity-100'">
            <h1 class="text-lg font-semibold text-gray-900">Restaurant Admin</h1>
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Main content area -->
        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 transition-opacity duration-300"
              :class="sidebarOpen && window.innerWidth < 1024 ? 'opacity-50' : 'opacity-100'">
            <div class="max-w-none">
                {{ $slot }}
            </div>
        </main>
    </div>

    
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
<script>
    function toggleFullscreen() {
        const fullscreenIcon = document.getElementById('fullscreen-icon');
        
        if (!document.fullscreenElement) {
            // Enter fullscreen
            document.documentElement.requestFullscreen().then(() => {
                // Update icon to exit fullscreen
                fullscreenIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M15 9h4.5M15 9v4.5M15 9l5.5-5.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 15h4.5M15 15v-4.5M15 15l5.5 5.5" />';
            }).catch(err => {
                console.log('Error attempting to enable fullscreen:', err);
            });
        } else {
            // Exit fullscreen
            document.exitFullscreen().then(() => {
                // Update icon to enter fullscreen
                fullscreenIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />';
            }).catch(err => {
                console.log('Error attempting to exit fullscreen:', err);
            });
        }
    }

    // Listen for fullscreen changes to update button state
    document.addEventListener('fullscreenchange', function() {
        const fullscreenIcon = document.getElementById('fullscreen-icon');
        
        if (document.fullscreenElement) {
            fullscreenIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M15 9h4.5M15 9v4.5M15 9l5.5-5.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 15h4.5M15 15v-4.5M15 15l5.5 5.5" />';
        } else {
            fullscreenIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />';
        }
    });
    </script>
</html>
