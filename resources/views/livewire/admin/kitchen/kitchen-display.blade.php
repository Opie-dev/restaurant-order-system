<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Kitchen Display</h1>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">
                Last updated: {{ now()->format('g:i A') }}
            </div>
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Order Counts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-yellow-600">{{ $this->orderCounts['pending'] }}</div>
                    <div class="text-sm font-medium text-yellow-700">Pending Orders</div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-blue-600">{{ $this->orderCounts['preparing'] }}</div>
                    <div class="text-sm font-medium text-blue-700">Preparing Orders</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-600">{{ $this->orderCounts['total'] }}</div>
                    <div class="text-sm font-medium text-gray-700">Total Active</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders by Group -->
    @if($this->pendingOrders->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($this->pendingOrders as $groupKey => $orders)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if(str_starts_with($groupKey, 'table_'))
                                    @php
                                        $tableId = str_replace('table_', '', $groupKey);
                                        $table = $orders->first()->table;
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Table {{ $table->table_number }}
                                    </div>
                                    @if($table->location_description)
                                        <div class="text-sm text-gray-500">{{ $table->location_description }}</div>
                                    @endif
                                @elseif($groupKey === 'delivery')
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                        Delivery Orders
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Pickup Orders
                                    </div>
                                @endif
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $orders->count() }} orders
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        @foreach($orders as $order)
                            <div class="border border-gray-200 rounded-lg p-3 {{ $order->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200' }}">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $order->code }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $order->created_at->format('g:i A') }} • {{ $order->items->sum('qty') }} items
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="space-y-1 mb-3">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-700">{{ $item->name_snapshot }} ×{{ $item->qty }}</span>
                                            <span class="text-gray-500">RM{{ number_format($item->line_total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Notes -->
                                @if($order->notes)
                                    <div class="mb-3 p-2 bg-white border border-gray-200 rounded text-xs">
                                        <span class="font-medium text-gray-600">Note:</span> {{ $order->notes }}
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2">
                                    @if($order->status === 'pending')
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Start Preparing
                                        </button>
                                    @elseif($order->status === 'preparing')
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'delivering')"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Mark Ready
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending orders</h3>
            <p class="mt-1 text-sm text-gray-500">All orders are completed or there are no new orders.</p>
        </div>
    @endif
</div>

<script>
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
</script>