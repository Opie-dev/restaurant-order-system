<div class="mx-auto px-6 py-8">
    <div class="fixed top-0 left-0 right-0 z-10 overflow-y-auto">
        @include('livewire.customer._baner')
    </div>
    <div class="mt-[16rem] lg:mt-[20rem]">
        <h1 class="text-3xl font-bold text-gray-900">Order History</h1>
        <p class="text-gray-600 mt-2">View all your past orders</p>
    </div>

    <!-- Filters: separate container -->
    <div class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:items-end">
            <!-- Search by order code -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by order #</label>
                <div class="relative">
                    <input id="search" type="text" placeholder="e.g. ABC123" wire:model.live.debounce.400ms="search" class="w-full p-2 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-500 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                </div>
            </div>

            <!-- Payment status filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by payment status</label>
                <div class="flex items-center gap-2">
                    <select id="status" wire:model.live="status" class="rounded-lg p-2 border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        @foreach($this->statuses as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @if($this->status !== 'all' || strlen($this->search) > 0)
                        <button type="button" wire:click="$set('status','all'); $set('search','')" class="text-sm text-gray-600 hover:text-gray-800 underline">Reset</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($this->orders->count() > 0)
        <div class="space-y-6">
            @foreach($this->orders as $order)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <!-- Order Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Order #{{ $order->code }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <div class="mt-3 sm:mt-0 flex flex-col sm:items-end">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->status === 'delivering')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6h3l3 4v6m-6 0H6" />
                                            </svg>
                                            On the way
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                        @elseif($order->payment_status === 'processing') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status === 'refunded') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="px-6 py-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Items Ordered</h4>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $item->name_snapshot }}</h4>
                                                <span class="text-xs text-gray-500">×{{ $item->qty }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                RM{{ number_format($item->unit_price, 2) }} each
                                            </div>
                                            
                                            @if($item->hasSelections())
                                                @php
                                                    $selections = $item->getSelectionsArray();
                                                @endphp
                                                
                                                @if(!empty($selections['options']) || !empty($selections['addons']))
                                                    <div class="mt-2 space-y-1">
                                                        @if(!empty($selections['options']))
                                                            @foreach($selections['options'] as $optionGroup)
                                                                @if(!empty($optionGroup['options']))
                                                                    <div class="flex items-start space-x-2">
                                                                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide min-w-0 flex-shrink-0">
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
                                                                        <span class="text-xs font-medium text-gray-600 uppercase tracking-wide min-w-0 flex-shrink-0">
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
                                                RM{{ number_format($item->line_total, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($order->notes)
                            <div class="mt-4 py-4 border-y border-gray-200">
                                <h5 class="text-sm font-medium text-gray-900 mb-1">Notes:</h5>
                                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                            </div>
                        @endif

                        <!-- Tracking Information -->
                        @if($order->status === 'delivering' && ($order->tracking_url || $order->delivery_fee))
                            <div class="mt-4 py-4 border-y border-gray-200">
                                <h5 class="text-sm font-medium text-gray-900 mb-3">Delivery Information</h5>
                                <div class="space-y-2">
                                    @if($order->tracking_url)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Tracking:</span>
                                            <a href="{{ $order->tracking_url }}" target="_blank" class="text-sm text-purple-600 hover:text-purple-800 underline">
                                                Track your order
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Order Summary -->
                        <div class="mt-4 pt-2">
                            <div class="flex justify-end gap-2 text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900">RM{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->delivery_fee)
                                <div class="flex justify-end gap-2 text-sm mt-1">
                                    <span class="text-gray-600">Delivery Fee:</span>
                                    <span class="text-gray-900">RM{{ number_format($order->delivery_fee, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-end gap-2 text-sm mt-1">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900">RM{{ number_format($order->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-end gap-2 text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">RM{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>

                       
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet. Start by browsing our menu!</p>
            <a href="{{ route('menu.store.index', ['store' => $store->slug]) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Browse Menu
            </a>
        </div>
    @endif
</div>
