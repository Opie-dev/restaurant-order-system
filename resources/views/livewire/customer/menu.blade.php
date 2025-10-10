<div class="min-h-screen bg-gray-50 flex flex-col" x-data="menuScroll()">
    <!-- Main Content Area -->
    <div class="overflow-y-auto" id="menu-scroll">
        <div class="@if($this->items->isEmpty())  @else fixed  @endif top-0 left-0 right-0 z-10">
            @include('livewire.customer._baner')

            <!-- Category Filters -->
            @php 
                $rootCategories = $this->categories->whereNull('parent_id');
            @endphp

            <div id="categories" class="flex rounded-lg bg-white items-center shadow-sm gap-2 lg:gap-3 py-2 overflow-x-auto px-4">
                <button type="button" 
                    @click="handleCategoryClick('all', 'all')"
                    data-category="all"
                    data-category-slug="all"
                    :class="currentCategory === 'all' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="px-3 lg:px-4 py-2 rounded-full text-sm font-medium transition-colors cursor-pointer hover:bg-purple-600 hover:text-white whitespace-nowrap">
                    All
                </button>
                @foreach($rootCategories as $cat)
                    <button type="button" 
                        @click="handleCategoryClick({{ $cat->id }}, '{{ Str::slug($cat->name) }}')"
                        data-category="{{ $cat->id }}"
                        data-category-slug="{{ Str::slug($cat->name) }}"
                        :class="currentCategory === '{{ Str::slug($cat->name) }}' ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-3 lg:px-4 py-2 cursor-pointer hover:bg-purple-600 hover:text-white rounded-full text-sm font-medium transition-colors whitespace-nowrap">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Store Closed Notice -->
            @if($store && !$store->isCurrentlyOpen())
                <div class="mx-4 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Store is currently closed</h3>
                            <p class="text-sm text-red-600 mt-1">
                                You can browse the menu, but ordering is not available right now.
                                @if($store->getNextOpeningTime())
                                    {{ $store->getNextOpeningTime() }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Category Filters -->
        <div class="@if($this->items->isEmpty()) mt-[3rem] lg:mt-[3rem] @elseif(!$store->cover_path) mt-[14rem] @else mt-[15rem] lg:mt-[25rem] @endif"> <!-- Add top padding to avoid overlap with fixed cart button on mobile -->
            <!-- Search Bar -->
            @if(!$this->items->isEmpty())
                <div id="search" class="relative mb-4 lg:mb-6 px-4">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search menu..." 
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                </div>
            @endif

            <!-- Menu Items -->
            @php
                // Get categories in position order
                $orderedCategories = $this->categories->whereNull('parent_id')->sortBy('position');
                
                // Group items by category ID to preserve position order
                $itemsByCategoryId = $this->items->groupBy('category_id');
                
                // Create ordered items by category
                $orderedItemsByCategory = collect();
                foreach ($orderedCategories as $category) {
                    if ($itemsByCategoryId->has($category->id)) {
                        $orderedItemsByCategory->put($category->name, $itemsByCategoryId->get($category->id));
                    }
                }
                
                // Add uncategorized items at the end
                $uncategorizedItems = $itemsByCategoryId->get(null, collect());
                if ($uncategorizedItems->isNotEmpty()) {
                    $orderedItemsByCategory->put('Uncategorized', $uncategorizedItems);
                }
            @endphp

            @forelse($orderedItemsByCategory as $categoryName => $items)
                @php
                    $categoryId = $orderedCategories->where('name', $categoryName)->first()?->id ?? 'uncategorized';
                    $categorySlug = Str::slug($categoryName);
                @endphp
                <div id="category-{{ $categorySlug }}" class="mb-6 lg:mb-8 px-4">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-3 lg:mb-4">{{ $categoryName }}</h2>
                    <!-- Responsive Grid: 1 item on mobile, 2 on tablet, 3 on desktop -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                        @foreach($items as $item)
                            <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow {{ (int)($item->stock ?? 0) <= 0 ? 'opacity-60' : '' }}">
                                @php
                                    $src = Str::startsWith($item->image_path, ['http://','https://']) ? $item->image_path : ($item->image_path ? asset('storage/' . $item->image_path) : null);
                                @endphp
                                
                                <!-- Item Image -->
                                <div class="relative bg-gray-100 overflow-hidden h-48 sm:h-56 md:h-64 lg:h-72 xl:h-80 {{ (int)($item->stock ?? 0) <= 0 ? 'grayscale' : '' }}">
                                    @if($src)
                                        <img 
                                            src="{{ $src }}" 
                                            alt="{{ $item->name }}" 
                                            class="absolute inset-0 w-full h-full object-cover object-center"
                                        />
                                    @else
                                        <div class="w-full h-full grid place-content-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Item Label from tag -->
                                    @if($item->tag)
                                        <div class="absolute top-2 right-2">
                                            <span class="px-2 py-1 text-xs font-semibold text-white rounded-full bg-purple-600">
                                                {{ ucfirst($item->tag) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Item Details -->
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $item->name }}</h3>
                                        <span class="text-lg font-bold text-purple-600">
                                            @if($item->type === 'set' && $item->base_price)
                                                RM {{ number_format($item->base_price, 2) }}
                                            @elseif($item->type === 'ala_carte' && $item->price)
                                                RM {{ number_format($item->price, 2) }}
                                            @else
                                                Price on request
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($item->description)
                                        <p class="text-gray-600 text-sm mb-3">{{ $item->description }}</p>
                                    @endif

                                    <!-- Add Button -->
                                    <div class="w-full">
                                        @if($store && !$store->isCurrentlyOpen())
                                            <button disabled class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg text-sm font-medium cursor-not-allowed flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                                Store Closed
                                            </button>
                                        @else
                                            @auth
                                                <button wire:click="addToCart({{ $item->id }})" 
                                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 cursor-pointer {{ (int)($item->stock ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                                    @disabled(($item->stock ?? 0) <= 0)>
                                                    @if((int)($item->stock ?? 0) > 0)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    @endif
                                                    {{ (int)($item->stock ?? 0) <= 0 ? 'Out of stock' : 'Add to Order' }}
                                                </button>
                                            @else
                                                <a href="{{ route('menu.store.login', ['store' => $store->slug]) }}" 
                                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 cursor-pointer {{ (int)($item->stock ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    {{ (int)($item->stock ?? 0) <= 0 ? 'Out of stock' : 'Add to Order' }}
                                                </a>
                                            @endauth
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            @endforelse
    
        </div>

        <!-- Mobile Cart Button (Fixed Bottom) -->
        <div class="fixed bottom-4 right-4 z-50">
            <a href="{{ route('menu.store.cart', ['store' => $store->slug]) }}" class="bg-purple-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <span class="font-semibold">View Cart</span>
                <span class="bg-white text-purple-600 px-2 py-1 rounded-full text-xs font-bold">{{ $this->cartCount }}</span>
            </a>
        </div>

        <footer class="mt-12 border-t border-gray-200 pt-8 pb-6 bg-white">
            @php
                $facebook = $store->social_facebook ?? null;
                $instagram = $store->social_instagram ?? null;
                $tiktok = $store->social_tiktok ?? null;
                $youtube = $store->social_youtube ?? null;
                $other = $store->social_other ?? null;
                $googleMap = $store->social_google_map ?? null;
                $hasSocial = $facebook || $instagram || $tiktok || $youtube || $other || $googleMap;
            @endphp
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center {{ $hasSocial ? 'justify-between' : 'justify-end' }} gap-4">
                @if($hasSocial)
                    <div class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                @endif
                <div class="flex items-center gap-6">
                    @if($hasSocial)
                        <!-- Social Media Links -->
                        <div class="flex items-center gap-3">
                            @if($facebook)
                                <a href="https://facebook.com/{{ ltrim($facebook, '/') }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.127 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.127 22 17 22 12z"/></svg>
                                </a>
                            @endif
                            @if($instagram)
                                <a href="https://instagram.com/{{ ltrim($instagram, '@/') }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="Instagram">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5a4.25 4.25 0 0 0 4.25-4.25v-8.5A4.25 4.25 0 0 0 16.25 3.5zm4.25 3.25a5.25 5.25 0 1 1-5.25 5.25A5.25 5.25 0 0 1 12 6.75zm0 1.5a3.75 3.75 0 1 0 3.75 3.75A3.75 3.75 0 0 0 12 8.25zm5.25-.75a1.25 1.25 0 1 1-1.25 1.25A1.25 1.25 0 0 1 17.25 7.5z"/></svg>
                                </a>
                            @endif
                            @if($tiktok)
                                <a href="https://tiktok.com/@{{ ltrim($tiktok, '@/') }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="Tiktok">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 8.306c-.638 0-1.262-.062-1.867-.183v6.61c0 3.41-2.77 6.187-6.18 6.187-1.65 0-3.2-.642-4.37-1.81A6.13 6.13 0 0 1 2.5 13.92c0-3.41 2.77-6.187 6.18-6.187.13 0 .26.003.39.01v2.07a4.13 4.13 0 0 0-.39-.02c-2.27 0-4.12 1.85-4.12 4.127 0 2.277 1.85 4.127 4.12 4.127 2.277 0 4.127-1.85 4.127-4.127V2.5h2.06c.19 1.13.98 2.07 2.01 2.47V8.3z"/></svg>
                                </a>
                            @endif
                            @if($youtube)
                                <a href="https://youtube.com/{{ ltrim($youtube, '@/') }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="YouTube">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21.8 8.001a2.75 2.75 0 0 0-1.94-1.94C18.13 5.5 12 5.5 12 5.5s-6.13 0-7.86.56a2.75 2.75 0 0 0-1.94 1.94C2.5 9.73 2.5 12 2.5 12s0 2.27.56 3.999a2.75 2.75 0 0 0 1.94 1.94C5.87 18.5 12 18.5 12 18.5s6.13 0 7.86-.56a2.75 2.75 0 0 0 1.94-1.94C21.5 14.27 21.5 12 21.5 12s0-2.27-.56-3.999zM10.75 15.02V8.98l6.25 3.02-6.25 3.02z"/></svg>
                                </a>
                            @endif
                            @if($googleMap)
                                <a href="{{ $googleMap }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="Google Map">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 14.5 9 2.5 2.5 0 0 1 12 11.5z"/></svg>
                                </a>
                            @endif
                            @if($other)
                                <a href="{{ (str_starts_with($other, 'http') ? $other : 'https://' . ltrim($other, '/')) }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-purple-600" aria-label="Website">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8 0-1.657.672-3.157 1.757-4.243l10.486 10.486A7.963 7.963 0 0 1 12 20zm6.243-3.757L7.757 5.757A7.963 7.963 0 0 1 20 12c0 1.657-.672 3.157-1.757 4.243z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                    @if(auth()->check() && auth()->user()->role !== 'admin')
                        <div class="flex items-center gap-4 text-sm ml-6">
                            <a href="{{ route('menu.store.addresses', ['store' => $store->slug]) }}" class="text-purple-600 hover:underline">Addresses</a>
                            <a href="{{ route('menu.store.orders', ['store' => $store->slug]) }}" class="text-purple-600 hover:underline">My Orders</a>
                        </div>
                    @endif
                </div>
                @if(!$hasSocial)
                    <div class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                @endif
            </div>
        </footer>
       
        <!-- Config Modal -->
        <div x-data="{ open: false }" 
            x-on:open-config.window="open = true" 
            x-on:close-config.window="open = false; $wire.resetConfig()">
            <div x-show="open" class="fixed inset-0 bg-black/40 z-50" @click="open=false; $wire.resetConfig()"></div>
            <div x-show="open" class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 md:p-4">
                <div class="bg-white w-full h-full md:h-auto md:max-w-lg md:max-h-[90vh] md:rounded-lg shadow-xl overflow-hidden flex flex-col" @click.stop>
                    <!-- Header with close button -->
                    <div class="flex items-center justify-end p-4 border-b">
                        <button @click="open=false; $wire.resetConfig()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Scrollable content -->
                    <div class="flex-1 overflow-y-auto">
                        @php $item = $this->items->firstWhere('id', $this->configItemId); @endphp
                        @if($item)
                            <!-- Item Image -->
                            <div class="relative h-48 md:h-64 bg-gray-100">
                                @php
                                    $src = Str::startsWith($item->image_path ?? '', ['http://','https://']) ? $item->image_path : (($item->image_path ?? null) ? asset('storage/' . $item->image_path) : null);
                                @endphp
                                @if($src)
                                    <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $item->name }}" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Item Info -->
                            <div class="p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $item->name }}</h2>
                                        @if($item->description)
                                            <p class="text-gray-600 text-sm">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="text-lg font-bold text-gray-900">
                                            RM {{ number_format(($item->type === 'set' ? $item->base_price : $item->price) ?? 0, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">Base price</div>
                                    </div>
                                </div>

                                <!-- Options Section (for all items) -->
                                @if(!empty($item->options))
                                    <div class="space-y-2 mb-2">
                                        @foreach(($item->options ?? []) as $gIndex => $group)
                                            @if(($group['enabled'] ?? true))
                                            <div class="border rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h3 class="font-semibold text-gray-900">{{ $group['name'] }}</h3>
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                        Pick {{ ($group['rules'][1] ?? 'one') === 'multiple' ? 'Multiple' : '1' }}
                                                    </span>
                                                </div>
                                                @if(($group['rules'][0] ?? 'required') === 'optional')
                                                    <div class="text-xs text-gray-500 mb-3">optional</div>
                                                @else
                                                    <div class="mb-3"></div>
                                                @endif
                                                <div class="space-y-3">
                                                    @foreach($group['options'] as $oIndex => $opt)
                                                        @php $multiple = ($group['rules'][1] ?? 'one') === 'multiple'; @endphp
                                                        <label class="flex items-center gap-3 p-3 border rounded-lg {{ !($opt['enabled'] ?? true) ? 'opacity-60 bg-gray-50' : 'hover:bg-gray-50' }}">
                                                            @if($multiple)
                                                                <input type="checkbox" @change="
                                                                    let list = $wire.config.options[{{ $gIndex }}]?.options || [];
                                                                    if ($event.target.checked) { list.push({{ json_encode($opt) }}); } else { list = list.filter(o => o.name !== '{{ $opt['name'] }}'); }
                                                                    $wire.set('config.options.{{ $gIndex }}.name', '{{ $group['name'] }}');
                                                                    $wire.set('config.options.{{ $gIndex }}.options', list);
                                                                " {{ !($opt['enabled'] ?? true) ? 'disabled' : '' }} class="w-4 h-4 text-purple-600" />
                                                            @else
                                                                <input type="radio" name="optionGroup{{ $gIndex }}" @change="
                                                                    $wire.set('config.options.{{ $gIndex }}', { name: '{{ $group['name'] }}', options: [{{ json_encode($opt) }}] });
                                                                " {{ !($opt['enabled'] ?? true) ? 'disabled' : '' }} class="w-4 h-4 text-purple-600" />
                                                            @endif
                                                            <span class="flex-1 font-medium">{{ $opt['name'] }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Addons Section (for all items) -->
                                @if(!empty($item->addons))
                                    <div class="space-y-2">
                                        @foreach(($item->addons ?? []) as $gIndex => $group)
                                            @if(($group['enabled'] ?? true))
                                            <div class="border rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h3 class="font-semibold text-gray-900">{{ $group['name'] }}</h3>
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                        Pick {{ ($group['rules'][1] ?? 'one') === 'multiple' ? 'Multiple' : '1' }}
                                                    </span>
                                                </div>
                                                @if(($group['rules'][0] ?? 'required') === 'optional')
                                                    <div class="text-xs text-gray-500 mb-3">optional</div>
                                                @else
                                                    <div class="mb-3"></div>
                                                @endif
                                                <div class="space-y-3">
                                                    @foreach($group['options'] as $oIndex => $opt)
                                                        @php $multiple = ($group['rules'][1] ?? 'one') === 'multiple'; @endphp
                                                        <label class="flex items-center gap-3 p-3 border rounded-lg {{ !($opt['enabled'] ?? true) ? 'opacity-60 bg-gray-50' : 'hover:bg-gray-50' }}">
                                                            @if($multiple)
                                                                <input type="checkbox" @change="
                                                                    let list = $wire.config.addons[{{ $gIndex }}]?.options || [];
                                                                    if ($event.target.checked) { list.push({{ json_encode($opt) }}); } else { list = list.filter(o => o.name !== '{{ $opt['name'] }}'); }
                                                                    $wire.set('config.addons.{{ $gIndex }}.name', '{{ $group['name'] }}');
                                                                    $wire.set('config.addons.{{ $gIndex }}.options', list);
                                                                " {{ !($opt['enabled'] ?? true) ? 'disabled' : '' }} class="w-4 h-4 text-purple-600" />
                                                            @else
                                                                <input type="radio" name="group{{ $gIndex }}" @change="
                                                                    $wire.set('config.addons.{{ $gIndex }}', { name: '{{ $group['name'] }}', options: [{{ json_encode($opt) }}] });
                                                                " {{ !($opt['enabled'] ?? true) ? 'disabled' : '' }} class="w-4 h-4 text-purple-600" />
                                                            @endif
                                                            @php $price = (float)($opt['price'] ?? 0); @endphp
                                                            <span class="flex-1 font-medium flex items-center justify-between">
                                                                <span>{{ $opt['name'] }}</span>
                                                                <span class="text-sm text-gray-700">@if($price > 0)+RM {{ number_format($price, 2) }}@endif</span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Fixed bottom button -->
                    <div class="border-t bg-white p-4">
                        <button @click="$wire.addConfiguredToCart()" 
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg transition-colors">
                            Add to Basket - RM {{ number_format(($this->getTotalPrice() ?? 0), 2) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('menuScroll', () => ({
        currentCategory: 'all',
        isScrolling: false,
        scrollTimeout: null,
        programmaticScroll: false,
        
        init() {
            this.setupScrollDetection();
        },
        
        setupScrollDetection() {
            // Track window scroll so highlighting works regardless of which element scrolls
            window.addEventListener('scroll', () => {
                this.handleScroll();
            }, { passive: true });
        },
        
        handleScroll() {
            if (this.isScrolling || this.programmaticScroll) return;
            
            this.isScrolling = true;
            clearTimeout(this.scrollTimeout);
            
            this.scrollTimeout = setTimeout(() => {
                this.updateActiveCategory();
                this.isScrolling = false;
            }, 100);
        },
        
        updateActiveCategory() {
            const categories = document.querySelectorAll('[id^="category-"]');
            if (categories.length === 0) return;

            // Determine a sticky header offset (height of categories bar + margin)
            const header = document.getElementById('categories');
            const headerHeight = header ? Math.max(header.getBoundingClientRect().height, 48) : 48;
            const offset = headerHeight + 24; // extra padding

            let activeCategory = null;
            let minDistance = Infinity;

            categories.forEach(category => {
                const rect = category.getBoundingClientRect();
                const distance = Math.abs(rect.top - offset);
                if (distance < minDistance) {
                    minDistance = distance;
                    activeCategory = category.id.replace('category-', '');
                }
            });

            if (activeCategory && activeCategory !== this.currentCategory) {
                this.currentCategory = activeCategory;
                this.highlightCategoryButton(activeCategory);
            }
        },
        
        highlightCategoryButton(categorySlug) {
            const categoryButtons = document.querySelectorAll('[data-category-slug]');
            
            categoryButtons.forEach(button => {
                const buttonSlug = button.getAttribute('data-category-slug');
                
                if (buttonSlug === categorySlug) {
                    // Add active class
                    button.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                    button.classList.add('bg-purple-600', 'text-white');
                    // Auto-scroll the categories strip to reveal the active pill
                    try {
                        button.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    } catch (e) {}
                } else {
                    // Remove active class
                    button.classList.remove('bg-purple-600', 'text-white');
                    button.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-50');
                }
            });
        },
        
        handleCategoryClick(categoryId, categorySlug) {
            // Disable scroll-driven highlighting during click navigation
            this.programmaticScroll = true;
            // Only scroll to category, don't filter items
            if (categorySlug !== 'all') {
                this.scrollToCategory(categorySlug);
            } else {
                // Scroll to top for "All" category
                const scrollContainer = document.getElementById('menu-scroll');
                if (scrollContainer) {
                    scrollContainer.scrollTo({ top: 0, behavior: 'auto' });
                }
            }
            
            // Update visual appearance
            this.highlightCategoryButton(categorySlug);
            this.currentCategory = categorySlug;
            // Re-enable scroll-driven highlighting shortly after jump
            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.programmaticScroll = false;
            }, 200);
        },
        
        scrollToCategory(categorySlug) {
            const categoryElement = document.getElementById(`category-${categorySlug}`);
            if (categoryElement) {
                const header = document.getElementById('categories');
                const headerHeight = header ? Math.max(header.getBoundingClientRect().height, 48) : 48;
                const offset = headerHeight + 24;
                const rect = categoryElement.getBoundingClientRect();
                const currentY = window.scrollY;
                const target = currentY + rect.top - offset;
                // Instant jump (no animation) when clicking a category
                window.scrollTo({ top: target, behavior: 'auto' });
                // Allow scroll events to settle before resuming tracking
                clearTimeout(this.scrollTimeout);
                this.scrollTimeout = setTimeout(() => {
                    this.programmaticScroll = false;
                }, 500);
            }
        }
    }));
});
</script>

