<div class="p-6 space-y-6 max-w-7xl mx-auto px-6 py-8" x-data>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Your Cart</h1>
        <a href="{{ route('menu.store', ['store' => $store]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Continue Ordering
        </a>
    </div>

    @if(empty($this->lines))
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-sm text-gray-500">Add items from the menu to get started.</p>
        </div>
    @else
        <!-- Order Items -->
        <div class="space-y-3">
            @foreach($this->lines as $line)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 relative">
                    <button 
                        wire:click="remove({{ $line['id'] }})" 
                        onclick="return confirm('Remove this item from your cart?')"
                        class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-500 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $line['name'] }}</h4>
                                <span class="text-xs text-gray-500">×{{ $line['qty'] }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                RM{{ number_format($line['price'], 2) }} each
                            </div>
                            
                            @if(!empty($line['selections']))
                                @php
                                    $selections = $line['selections'];
                                @endphp
                                
                                @if(!empty($selections['options']) || !empty($selections['addons']))
                                    <div class="mt-2 space-y-1">
                                        @if(!empty($selections['options']))
                                            @foreach($selections['options'] as $optionGroup)
                                                @if(!empty($optionGroup['options']))
                                                    <div class="flex items-start space-x-2">
                                                        <span class="text-xs font-medium text-gray-600 tracking-wide min-w-0 flex-shrink-0">
                                                            {{ $optionGroup['name'] }}:
                                                        </span>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($optionGroup['options'] as $option)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                    {{ $option['name'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        
                                        @if(!empty($selections['addons']))
                                            @foreach($selections['addons'] as $addonGroup)
                                                @if(!empty($addonGroup['options']))
                                                    <div class="flex items-start space-x-2">
                                                        <span class="text-xs font-medium text-gray-600 tracking-wide min-w-0 flex-shrink-0">
                                                            {{ $addonGroup['name'] }}:
                                                        </span>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($addonGroup['options'] as $addon)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $addon['name'] }}
                                                                    @if(isset($addon['price']) && $addon['price'] > 0)
                                                                        (+RM {{ number_format($addon['price'], 2) }})
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-2 text-xs text-gray-400 italic">No special selections</div>
                                @endif
                            @else
                                <div class="mt-2 text-xs text-gray-400 italic">No special selections</div>
                            @endif

                            <!-- Quantity Controls -->
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <button 
                                        wire:click="decrement({{ $line['id'] }})" 
                                        class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="min-w-[1.5rem] text-center font-medium text-sm px-2 py-0.5 rounded bg-gray-50">{{ $line['qty'] }}</span>
                                    <button 
                                        wire:click="increment({{ $line['id'] }})" 
                                        class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        RM{{ number_format($line['line_total'], 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-gray-900">RM {{ number_format($this->totals['subtotal'], 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tax (8%)</span>
                    <span class="text-gray-900">RM {{ number_format($this->totals['tax'], 2) }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2">
                    <div class="flex justify-between text-base font-semibold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">RM {{ number_format($this->totals['total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <button 
                @click="if (confirm('Are you sure you want to clear all items from your cart?')) { $wire.clear() }" 
                class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear Cart
            </button>
            <a 
                href="{{ route('checkout') }}" 
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
                Proceed to Checkout
            </a>
        </div>
    @endif
</div>