<div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row lg:h-screen">
    <!-- Left Panel - Menu -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-4 lg:px-6 lg:py-6">
            <!-- Store Header -->
            @if($currentStore)
                <div class="mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        @if($currentStore->logo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($currentStore->logo_path) }}" alt="{{ $currentStore->name }}" class="h-12 w-12 object-contain rounded-lg">
                        @else
                            <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $currentStore->name }}</h1>
                            @if($currentStore->description)
                                <p class="text-gray-600 text-sm">{{ $currentStore->description }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Preview Mode - No cart functionality</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Category Filters -->
            @php 
                $rootCategories = $this->categories->whereNull('parent_id');
            @endphp
            <div class="flex items-center gap-2 lg:gap-3 mb-4 lg:mb-6 overflow-x-auto pb-2">
                <button type="button" 
                    wire:click="$set('categoryId', null)" 
                    class="px-3 lg:px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap {{ !$categoryId ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    All Items
                </button>
                @foreach($rootCategories as $cat)
                    <button type="button" 
                        wire:click="$set('categoryId', {{ $cat->id }})" 
                        class="px-3 lg:px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap {{ (int)$categoryId === (int)$cat->id ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Search Bar -->
            <div class="relative mb-4 lg:mb-6">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search menu..." 
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
            </div>

            <!-- Menu Items -->
            @php
                $itemsByCategory = $this->items->groupBy(function($item) {
                    return $item->category?->name ?? 'Uncategorized';
                });
            @endphp

            @forelse($itemsByCategory as $categoryName => $items)
                <div class="mb-6 lg:mb-8">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-3 lg:mb-4">{{ $categoryName }}</h2>
                    <!-- Responsive Grid: 1 item on mobile, 2 on tablet, 3 on desktop -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                        @foreach($items as $item)
                            <div class="bg-white rounded-lg lg:rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow {{ (int)($item->stock ?? 0) <= 0 ? 'opacity-60' : '' }}">
                                @php
                                    $src = Str::startsWith($item->image_path, ['http://','https://']) ? $item->image_path : ($item->image_path ? asset('storage/' . $item->image_path) : null);
                                @endphp
                                
                                <!-- Item Image -->
                                <div class="aspect-video bg-gray-100 overflow-hidden relative {{ (int)($item->stock ?? 0) <= 0 ? 'grayscale' : '' }}">
                                    @if($src)
                                        <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $item->name }}" />
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
                                        <span class="text-lg font-bold text-purple-600">RM {{ number_format($item->price, 2) }}</span>
                                    </div>
                                    
                                    @if($item->description)
                                        <p class="text-gray-600 text-sm mb-3">{{ $item->description }}</p>
                                    @endif

                                    <!-- Preview Button (replaces Add to Cart) -->
                                    <div class="w-full">
                                        <button disabled
                                            class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg text-sm font-medium cursor-not-allowed flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview Mode
                                        </button>
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
    </div>

    <!-- Right Panel - Info -->
    <div class="lg:w-80 bg-white border-l border-gray-200 p-6">
        <div class="sticky top-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Preview</h3>
            
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Preview Mode</h4>
                            <p class="text-sm text-blue-700 mt-1">This is how customers will see your menu. Cart and checkout functionality is disabled.</p>
                        </div>
                    </div>
                </div>

                @if($currentStore)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Store Information</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div><strong>Name:</strong> {{ $currentStore->name }}</div>
                            <div><strong>Slug:</strong> {{ $currentStore->slug }}</div>
                            <div><strong>Customer URL:</strong> 
                                <a href="{{ route('menu.store', ['store' => $currentStore->slug]) }}" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 underline">
                                    {{ config('app.url') }}/menu/{{ $currentStore->slug }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-green-900">View Live Menu</h4>
                            <p class="text-sm text-green-700 mt-1">Click the button above to see the customer menu with full functionality.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>