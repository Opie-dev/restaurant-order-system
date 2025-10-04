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
                @php $default = auth()->user()->defaultAddress; @endphp
                @if(!$default)
                    <div class="text-gray-600 text-sm">No default address yet. <a href="{{ route('menu.store.addresses', ['store' => $store->slug]) }}" class="text-purple-600">Add one</a> to proceed.</div>
                @else
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
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
        @php $lines = $this->cartLines; $totals = $this->cartTotals; @endphp
        @if(empty($lines))
            <p class="text-gray-600 text-sm">Your cart is empty.</p>
        @else
            <div class="space-y-3">
                @foreach($lines as $line)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $line['item']->name }}</h4>
                                    <span class="text-xs text-gray-500">×{{ $line['qty'] }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    RM{{ number_format($line['unit_price'], 2) }} each
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
                                                            <span class="text-xs font-medium text-gray-600  tracking-wide min-w-0 flex-shrink-0">
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
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    RM{{ number_format($line['line_total'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">RM {{ number_format($totals['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax (8%)</span>
                        <span class="text-gray-900">RM {{ number_format($totals['tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">RM {{ number_format($totals['total'], 2) }}</span>
                    </div>
                    @if($deliver)
                        <p class="text-xs text-gray-500">Delivery fee will be confirmed by admin if delivery selected.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Notes -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Notes</h2>
        <textarea wire:model.lazy="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Any special instructions or delivery notes..."></textarea>
        <p class="text-xs text-gray-500 mt-2">Example: Please ring the bell, no chili, etc.</p>
    </div>

    <!-- Submit Order Button -->
    <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('menu.store.cart', ['store' => $store->slug]) }}" class="text-gray-600 hover:text-gray-800 transition-colors font-medium">
            ← Back to Cart
        </a>
        
        <button 
            wire:click="submitOrder" 
            wire:loading.attr="disabled"
            wire:target="submitOrder"
            class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium flex items-center gap-2"
        >
            <svg wire:loading.remove wire:target="submitOrder" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg wire:loading wire:target="submitOrder" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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


