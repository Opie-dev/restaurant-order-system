
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
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 relative">
                            <button wire:click="remove({{ $line['item']->id }}, {{ $line['id'] }})" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @php 
                                $sel = $line['selections'] ?? []; 
                                $optionNames = collect($sel['options'] ?? [])->flatMap(fn($g) => collect($g['options'] ?? [])->pluck('name'))->filter()->values()->implode(', ');
                                $basePrice = ($line['item']->type === 'set') ? (float)($line['item']->base_price ?? 0) : (float)($line['item']->price ?? 0);
                            @endphp

                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900">{{ $line['item']->name }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Base Price: RM {{ number_format($basePrice, 2) }}</div>

                                    @if(!empty($sel['options']))
                                        <div class="mt-2">
                                            <div class="text-xs text-gray-500 mb-1">Selected:</div>
                                            <ul class="text-xs text-gray-800 space-y-0.5">
                                                @foreach($sel['options'] as $g)
                                                    @foreach(($g['options'] ?? []) as $opt)
                                                        <li>• {{ $opt['name'] }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!empty($sel['addons']))
                                        <div class="mt-2">
                                            <div class="text-xs text-gray-500 mb-1">Addons:</div>
                                            <ul class="text-xs text-gray-800 space-y-0.5">
                                                @foreach($sel['addons'] as $g)
                                                    @foreach(($g['options'] ?? []) as $opt)
                                                        @php $p = (float)($opt['price'] ?? 0); @endphp
                                                        <li class="flex items-center justify-between">
                                                            <span>• {{ $opt['name'] }}</span>
                                                            @if($p > 0)
                                                                <span class="ml-2 px-2 py-0.5 rounded bg-gray-100 text-gray-700">+RM {{ number_format($p, 2) }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between mt-3">
                                        @php $outOfStock = isset($line['item']->stock) && ((int)$line['item']->stock <= 0); @endphp
                                        <div>
                                            @if($outOfStock)
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Not available</span>
                                            @else
                                                <div class="flex items-center gap-3">
                                                    <button wire:click="decrement({{ $line['item']->id }}, {{ $line['id'] }})" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors" @disabled(isset($line['item']->stock) && $line['qty'] <= 1)>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    </button>
                                                    <span class="min-w-[1.5rem] text-center font-medium text-sm px-2 py-0.5 rounded bg-gray-50">{{ $line['qty'] }}</span>
                                                    <button wire:click="increment({{ $line['item']->id }}, {{ $line['id'] }})" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors" @disabled(isset($line['item']->stock) && ((int)$line['item']->stock <= 0 || $line['qty'] >= (int)$line['item']->stock))>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        
                                    </div>

                                    <div class="border-t border-gray-200 mt-3 pt-2 flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Total</span>
                                        <span class="font-bold text-gray-900">RM {{ number_format($line['line_total'], 2) }}</span>
                                    </div>
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
                <button x-data @click.prevent="if (confirm('Clear all items from your order?')) { $wire.clear() }" class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors cursor-pointer">
                    Clear Order
                </button>
            </div>
        </div>
        @endif
    </div>
   

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

