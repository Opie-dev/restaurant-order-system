<div class="max-w-5xl mx-auto px-6 py-8">
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
                <a href="{{ route('addresses') }}" class="text-sm text-purple-600 hover:text-purple-700">Manage addresses</a>
            </div>
            @if($deliver)
                @php $default = auth()->user()->defaultAddress; @endphp
                @if(!$default)
                    <div class="text-gray-600 text-sm">No default address yet. <a href="{{ route('addresses') }}" class="text-purple-600">Add one</a> to proceed.</div>
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
            <div class="space-y-4">
                @foreach($lines as $line)
                    @php
                        $src = null;
                        if (!empty($line['item']->image_path)) {
                            $src = Storage::url($line['item']->image_path);
                        }
                    @endphp
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-md overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($src)
                                    <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $line['item']->name }}" />
                                @else
                                    <div class="w-full h-full grid place-content-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-gray-900 font-medium">{{ $line['item']->name }}</div>
                                <div class="text-gray-500 text-xs mt-0.5">Qty: {{ $line['qty'] }} × RM {{ number_format($line['unit_price'], 2) }}</div>
                            </div>
                        </div>
                        <div class="text-gray-900 font-semibold">RM {{ number_format($line['line_total'], 2) }}</div>
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
</div>


