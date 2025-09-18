<div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row lg:h-screen">
        <!-- Left Panel - Menu -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-4 lg:px-6 lg:py-6">
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

                                        <!-- Add Button -->
                                        <div class="w-full">
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
                                                <a href="{{ route('login') }}" 
                                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 cursor-pointer {{ (int)($item->stock ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    {{ (int)($item->stock ?? 0) <= 0 ? 'Out of stock' : 'Add to Order' }}
                                                </a>
                                            @endauth
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

        <!-- Mobile Cart Button (Fixed Bottom) -->
        @if($this->cartCount > 0)
            <div class="lg:hidden fixed bottom-4 right-4 z-50">
                <a href="{{ route('cart') }}" class="bg-purple-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    <span class="font-semibold">View Cart</span>
                    <span class="bg-white text-purple-600 px-2 py-1 rounded-full text-xs font-bold">{{ $this->cartCount }}</span>
                </a>
            </div>
        @endif

        <!-- Right Panel - Your Order (Desktop Only) -->
        <div class="hidden lg:flex lg:w-96 bg-gray-50 border-l border-gray-200 flex-col">
            <!-- Order Header -->
            <div class="p-6 border-b border-gray-200 bg-white">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800">Your Order</h2>
                </div>
            </div>

            <!-- Order Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                   @if($this->cartCount > 0)
                       @foreach($this->cartLines as $line)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="flex items-center gap-4">
                                <!-- Item Image -->
                                <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @php
                                        $src = Str::startsWith($line['item']->image_path, ['http://','https://']) ? $line['item']->image_path : ($line['item']->image_path ? asset('storage/' . $line['item']->image_path) : null);
                                    @endphp
                                    @if($src)
                                        <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $line['item']->name }}" />
                                    @else
                                        <div class="w-full h-full grid place-content-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Item Details -->
                                    <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800 text-base mb-2">{{ $line['item']->name }}</h3>

                                    @php $outOfStock = isset($line['item']->stock) && ((int)$line['item']->stock <= 0); @endphp
                                    <!-- Quantity Controls or Out-of-Stock Notice -->
                                    @if($outOfStock)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Not available</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <button wire:click="decrement({{ $line['item']->id }})" 
                                                class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors"
                                                @disabled(isset($line['item']->stock) && $line['qty'] <= 1)>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="w-6 text-center font-medium text-sm">{{ $line['qty'] }}</span>
                                            <button wire:click="increment({{ $line['item']->id }})" 
                                                class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors"
                                                @disabled(isset($line['item']->stock) && ((int)$line['item']->stock <= 0 || $line['qty'] >= (int)$line['item']->stock))>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Price and Remove Button -->
                                <div class="flex flex-col items-end gap-2 pr-2 pb-2">
                                    <span class="font-bold text-gray-800 text-base">RM {{ number_format($line['line_total'], 2) }}</span>
                                    
                                    <!-- Remove Button -->
                                    <button wire:click="remove({{ $line['item']->id }})" 
                                        class="w-6 h-6 rounded bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Your order is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Add items from the menu to get started.</p>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
                   @if($this->cartCount > 0)
                       <div class="border-t border-gray-200 bg-white p-6 space-y-4">
                           <div class="space-y-3">
                               <div class="flex justify-between text-base">
                                   <span class="text-gray-600">Subtotal</span>
                                   <span class="text-gray-800 font-medium">RM {{ number_format($this->cartTotals['subtotal'], 2) }}</span>
                               </div>
                               <div class="flex justify-between text-base">
                                   <span class="text-gray-600">Tax (8%)</span>
                                   <span class="text-gray-800 font-medium">RM {{ number_format($this->cartTotals['tax'], 2) }}</span>
                               </div>
                               <div class="border-t border-gray-200 pt-3">
                                   <div class="flex justify-between text-xl font-bold">
                                       <span class="text-gray-800">Subtotal</span>
                                       <span class="text-gray-800">RM {{ number_format($this->cartTotals['total'], 2) }}</span>
                                   </div>
                               </div>
                           </div>

                <!-- Action Buttons -->
                <div class="space-y-3 pt-2">
                    <button wire:click="proceedToCheckout" class="w-full px-6 py-3 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Proceed to Checkout
                    </button>
                    <button wire:click="clear" class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors cursor-pointer">
                        Clear Order
                    </button>
                </div>
            </div>
        @endif
        </div>
    </div>
</div>


