<div class="mx-auto px-6 py-8 overflow-y-auto">
    <div class="fixed top-0 left-0 right-0 z-10">
        @include('livewire.customer._baner')
    </div>
    <div class="mt-[16rem] lg:mt-[20rem]">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Checkout</h1>

    @auth
        <!-- Delivery Option -->
        <div class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Delivery</h2>
            <div class="flex items-center gap-6">
                <label class="inline-flex items-center gap-2">
                    <input type="radio" class="text-purple-600 focus:ring-purple-500" wire:model.live="deliver" value="0">
                    <span class="text-gray-800">No, self-pickup</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" class="text-purple-600 focus:ring-purple-500" wire:model.live="deliver" value="1">
                    <span class="text-gray-800">Yes, deliver to me</span>
                </label>
            </div>
        </div>

        <!-- Address Section (Conditional) -->
        <div class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6" x-data>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Delivery Address</h2>
                <a href="{{ route('menu.store.addresses', ['store' => $store->slug]) }}" class="text-sm text-purple-600 hover:text-purple-700">Manage addresses</a>
            </div>
            @if($deliver)
                @php 
                    $userAddresses = $this->userAddresses;
                    $default = auth()->user()->defaultAddress; 
                @endphp
                
                @if($userAddresses->isEmpty())
                    <!-- No addresses at all -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">No delivery addresses found</h3>
                                <p class="text-sm text-red-700 mt-1">You need to add a delivery address before placing a delivery order.</p>
                                <a href="{{ route('menu.store.addresses', ['store' => $store->slug]) }}" 
                                   class="inline-flex items-center mt-2 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Delivery Address
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif(!$default)
                    <!-- Has addresses but no default -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">No default address set</h3>
                                <p class="text-sm text-yellow-700 mt-1">Please set a default address or select one from your saved addresses.</p>
                                <a href="{{ route('menu.store.addresses', ['store' => $store->slug]) }}" 
                                   class="inline-flex items-center mt-2 px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                                    Manage Addresses
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Default address available -->
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-gray-900">{{ $default->recipient_name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Default</span>
                        </div>
                        <div class="text-sm text-gray-700">{{ $default->line1 }}@if($default->line2), {{ $default->line2 }}@endif</div>
                        <div class="text-sm text-gray-700">{{ $default->postal_code }} {{ $default->city }}@if($default->state), {{ $default->state }}@endif, {{ $default->country }}</div>
                        @if($default->phone)<div class="text-sm text-gray-600">{{ $default->phone }}</div>@endif
                    </div>
                @endif
            @else
                <div class="text-gray-600 text-sm">Self-pickup selected. No address required.</div>
            @endif
        </div>
    @endauth

    <!-- Order Summary -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 md:p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
        @php $lines = $this->cartLines; $totals = $this->cartTotals; @endphp
        @if(empty($lines))
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <p class="mt-2 text-gray-600 text-sm">Your cart is empty.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($lines as $line)
                    <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4 shadow-sm">
                        <!-- Item Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-base font-semibold text-gray-900 truncate">{{ $line['item']->name }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 flex-shrink-0">
                                        ×{{ $line['qty'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">
                                        RM{{ number_format($line['unit_price'], 2) }} each
                                    </span>
                                    <span class="text-lg font-bold text-gray-900">
                                        RM{{ number_format($line['line_total'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selections/Addons -->
                        @if(!empty($line['selections']))
                            @php
                                $selections = $line['selections'];
                            @endphp
                            
                            @if(!empty($selections['options']) || !empty($selections['addons']))
                                <div class="space-y-3 pt-3 border-t border-gray-100">
                                    @if(!empty($selections['options']))
                                        @foreach($selections['options'] as $optionGroup)
                                            @if(!empty($optionGroup['options']))
                                                <div class="space-y-2">
                                                    <h5 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                        {{ $optionGroup['name'] }}
                                                    </h5>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($optionGroup['options'] as $option)
                                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">
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
                                                <div class="space-y-2">
                                                    <h5 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                                        {{ $addonGroup['name'] }}
                                                    </h5>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($addonGroup['options'] as $addon)
                                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                                {{ $addon['name'] }}
                                                                @if(isset($addon['price']) && $addon['price'] > 0)
                                                                    <span class="ml-1 font-semibold">(+RM {{ number_format($addon['price'], 2) }})</span>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
                
                <!-- Order Totals -->
                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4 mt-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Order Total</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-medium text-gray-900">RM {{ number_format($totals['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-600">Tax (8%)</span>
                            <span class="text-sm font-medium text-gray-900">RM {{ number_format($totals['tax'], 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-indigo-600">RM {{ number_format($totals['total'], 2) }}</span>
                            </div>
                        </div>
                        @if($deliver)
                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-xs text-yellow-800">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Delivery fee will be confirmed by admin
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Notes -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 md:p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Special Instructions</h2>
        <textarea 
            wire:model.lazy="notes" 
            rows="3" 
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none text-sm"
            placeholder="Any special instructions or delivery notes..."
        ></textarea>
        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Example: Please ring the bell, no chili, extra sauce, etc.
        </p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mt-6 space-y-3">
            @foreach($errors->all() as $error)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Order Error</h3>
                            <p class="text-sm text-red-700 mt-1">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Submit Order Button -->
    <div class="mt-8 space-y-4">
        <a 
            href="{{ route('menu.store.cart', ['store' => $store->slug]) }}" 
            class="flex items-center justify-center gap-2 bg-white px-6 py-3 rounded-xl text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors font-medium border border-gray-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Cart
        </a>
        
        <button 
            wire:click="submitOrder" 
            wire:loading.attr="disabled"
            wire:target="submitOrder"
            class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 font-semibold flex items-center justify-center gap-3 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
        >
            <svg wire:loading.remove wire:target="submitOrder" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg wire:loading wire:target="submitOrder" class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span wire:loading.remove wire:target="submitOrder">Place Order</span>
            <span wire:loading wire:target="submitOrder">Processing...</span>
        </button>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    </div>
</div>


