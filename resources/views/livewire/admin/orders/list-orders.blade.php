<div class="p-6 space-y-6" x-data="{ 
    showCancelModal: false, 
    orderId: null, 
    cancellationRemarks: '',
    openRowId: null
}">
    <!-- Cancellation Modal -->
    <div x-show="showCancelModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCancelModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Order</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Please provide a reason for cancelling this order.</p>
                            </div>
                            <div class="mt-4">
                                <label for="cancellation-remarks" class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                                <textarea 
                                    x-model="cancellationRemarks"
                                    id="cancellation-remarks"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                    placeholder="e.g., Out of stock, Customer request, Payment failed..."
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        @click="
                            if (cancellationRemarks.trim()) {
                                $wire.updateOrderStatus(orderId, 'cancelled', cancellationRemarks);
                                showCancelModal = false;
                                cancellationRemarks = '';
                            }
                        "
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel Order
                    </button>
                    <button 
                        @click="showCancelModal = false; cancellationRemarks = ''"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Keep Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">All Orders</h1>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.pending') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Kitchen
            </a>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Status Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            @foreach($this->orderStatusCounts as $status => $count)
                <div class="text-center p-4 rounded-lg {{ $status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : ($status === 'preparing' ? 'bg-blue-50 border border-blue-200' : ($status === 'delivering' ? 'bg-indigo-50 border border-indigo-200' : ($status === 'completed' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'))) }}">
                    <div class="text-2xl font-bold {{ $status === 'pending' ? 'text-yellow-600' : ($status === 'preparing' ? 'text-blue-600' : ($status === 'delivering' ? 'text-indigo-600' : ($status === 'completed' ? 'text-green-600' : 'text-red-600'))) }}">{{ $count }}</div>
                    <div class="text-sm font-medium {{ $status === 'pending' ? 'text-yellow-700' : ($status === 'preparing' ? 'text-blue-700' : ($status === 'delivering' ? 'text-indigo-700' : ($status === 'completed' ? 'text-green-700' : 'text-red-700'))) }} capitalize">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search by order code or table..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="all">All Statuses</option>
                    @foreach($this->statuses as $statusOption)
                        <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Order Type</label>
                <select wire:model.live="orderType" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="all">All Types</option>
                    @foreach($this->orderTypes as $orderTypeOption)
                        <option value="{{ $orderTypeOption }}">{{ ucfirst($orderTypeOption) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        @if($this->orders->count() > 0)
            <div class="">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Fee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->orders as $order)
                            <tr class="hover:bg-gray-50 cursor-pointer"
                                @click="openRowId = openRowId === {{ $order->id }} ? null : {{ $order->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->user?->name ?? 'Guest' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->table)
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">Table {{ $order->table_number }}</div>
                                            @if($order->table->location_description)
                                                <div class="text-xs text-gray-500">{{ $order->table->location_description }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->order_type_color_class }}">
                                        {{ $order->order_type_display }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusColorClass() }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->items->sum('qty') }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($order->delivery_fee)
                                            RM{{ number_format($order->delivery_fee, 2) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">RM{{ number_format($order->total, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $order->created_at->format('M j, Y g:i A') }}</div>
                                </td>
                            </tr>
                            
                            <!-- Expandable Order Details -->
                            <tr id="details-{{ $order->id }}" x-show="openRowId === {{ $order->id }}" class="bg-gray-50">
                                <td colspan="10" class="px-6 py-4">
                                    <div class="space-y-4">
                                        <!-- Order Information -->
                                        @if($order->table)
                                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-blue-800">Table Order:</span>
                                                        <div class="text-sm text-blue-700 mt-1">
                                                            <div>Table {{ $order->table_number }} - {{ $order->table->location_description ?? 'Main Area' }}</div>
                                                            <div class="text-xs text-blue-600 mt-1">Capacity: {{ $order->table->capacity }} people</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Order Items -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 mb-3">Order Items</h4>
                                            <div class="space-y-2">
                                                @foreach($order->items as $item)
                                                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                                                        <div class="flex items-start justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-2">
                                                                    <h5 class="text-sm font-medium text-gray-900">{{ $item->name_snapshot }}</h5>
                                                                    <span class="text-xs text-gray-500">×{{ $item->qty }}</span>
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    RM{{ number_format($item->unit_price, 2) }} each
                                                                </div>
                                                                
                                                                @if($item->hasSelections())
                                                                    <div class="mt-2 space-y-1">
                                                                        @if(!empty($item->selections['options']))
                                                                            @foreach($item->selections['options'] as $optionGroup)
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
                                                                        
                                                                        @if(!empty($item->selections['addons']))
                                                                            @foreach($item->selections['addons'] as $addonGroup)
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
                                                            </div>
                                                            <div class="text-right ml-4">
                                                                <div class="text-sm font-semibold text-gray-900">
                                                                    RM{{ number_format($item->line_total, 2) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <!-- Order Notes -->
                                        @if($order->notes)
                                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 6l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-blue-800">Order Notes:</span>
                                                        <p class="text-sm text-blue-700 mt-1">{{ $order->notes }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Tracking Information -->
                                        @if($order->status === 'delivering' && ($order->tracking_url || $order->delivery_fee))
                                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <span class="text-sm font-medium text-green-800">Delivery Information:</span>
                                                        <div class="mt-1 space-y-1">
                                                            @if($order->tracking_url)
                                                                <div class="text-sm text-green-700">
                                                                    <span class="font-medium">Tracking:</span> 
                                                                    <a href="{{ $order->tracking_url }}" target="_blank" class="text-green-600 hover:text-green-800 underline">
                                                                        {{ $order->tracking_url }}
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            @if($order->delivery_fee)
                                                                <div class="text-sm text-green-700">
                                                                    <span class="font-medium">Delivery Fee:</span> RM{{ number_format($order->delivery_fee, 2) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Cancellation Remarks -->
                                        @if($order->status === 'cancelled' && $order->cancellation_remarks)
                                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-red-800">Cancellation Reason:</span>
                                                        <p class="text-sm text-red-700 mt-1">{{ $order->cancellation_remarks }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($this->orders->hasPages())
                <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        @if($this->orders->total() > 0)
                            Showing {{ $this->orders->firstItem() }} to {{ $this->orders->lastItem() }} of {{ $this->orders->total() }} results
                        @else
                            Showing 0 results
                        @endif
                    </div>
                    <div>
                        {{ $this->orders->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                <p class="mt-1 text-sm text-gray-500">No orders match your current filters.</p>
            </div>
        @endif
    </div>
</div>