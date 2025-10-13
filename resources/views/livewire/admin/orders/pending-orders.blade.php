<div class="p-6 space-y-6" 
     x-data="{ 
        showCancelModal: false, 
        orderId: null, 
        cancellationRemarks: '',
        cancellationError: '',
        showNotification: false,
        notificationMessage: '',
        notificationCount: 0
     }"
     x-init="
        // Check for new orders every 5 seconds
        setInterval(() => {
            $wire.checkForNewOrders();
        }, 5000);
        
        // Listen for new order notifications
        $wire.on('new-orders-notification', (event) => {
            notificationMessage = event.message;
            notificationCount = event.count;
            showNotification = true;
            
            // Auto-hide notification after 5 seconds
            setTimeout(() => {
                showNotification = false;
            }, 5000);
        });
     "
     wire:poll.5s="checkForNewOrders">
    <!-- Real-time Notification -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3"
         style="display: none;">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 13h6V7H4v6zM4 7h6V1H4v6z"></path>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-medium" x-text="notificationMessage"></p>
        </div>
        <button @click="showNotification = false; $wire.markOrdersAsSeen()" class="flex-shrink-0 text-green-200 hover:text-white">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancellation Modal -->
    <div x-show="showCancelModal" x-cloak @keydown.escape.window="showCancelModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 transition-opacity z-40" @click="showCancelModal = false; cancellationRemarks = ''"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
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
                                    @input="cancellationError = ''"
                                    id="cancellation-remarks"
                                    rows="3"
                                    class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                    placeholder="e.g., Out of stock, Customer request, Payment failed..."
                                ></textarea>
                                <p x-show="cancellationError" x-text="cancellationError" class="mt-1 text-sm text-red-600"></p>
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
                                cancellationError = '';
                            } else {
                                cancellationError = 'Please enter a cancellation reason.';
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
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-semibold">Active Orders</h1>
            @if($newOrderCount > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                    {{ $newOrderCount }} new
                </span>
            @endif
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span>Live updates</span>
            </div>
            <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                All Orders
            </a>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-2 gap-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-5 h-5 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-2 w-0 flex-1">
                        <dl>
                            <dt class="text-base font-medium text-gray-500 truncate">New Orders</dt>
                            <dd class="text-base font-medium text-gray-900">{{ $this->pendingCount }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-5 h-5 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-2 w-0 flex-1">
                        <dl>
                            <dt class="text-base font-medium text-gray-500 truncate">Preparing Orders</dt>
                            <dd class="text-base font-medium text-gray-900">{{ $this->preparingCount }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-2 w-0 flex-1">
                        <dl>
                            <dt class="text-base font-medium text-gray-500 truncate">Delivering Orders</dt>
                            <dd class="text-base font-medium text-gray-900">{{ $this->deliveringCount }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="flex items-center justify-between">
        <div class="flex-1 max-w-lg">
            <label for="search" class="sr-only">Search orders</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" 
                        id="search" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" 
                        placeholder="Search by order code or table..." 
                        type="search">
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    @if($this->pendingOrders->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($this->pendingOrders as $order)
                 <div class="bg-white border border-gray-200 rounded-lg p-6 hover:bg-gray-100 transition-colors flex flex-col">
                    <!-- Order Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $order->code }}
                                </h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusColorClass() }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->order_type_color_class }}">
                                    {{ $order->order_type_display }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>{{ $order->user?->name ?? 'Guest' }}</span>
                                <span>•</span>
                                <span>{{ $order->created_at->format('M j, Y g:i A') }}</span>
                                <span>•</span>
                                <span>{{ $order->items->sum('qty') }} items</span>
                                @if($order->table)
                                    <span>•</span>
                                    <span class="text-blue-600 font-medium">Table {{ $order->table_number }}</span>
                                @endif
                                @if($order->delivery_fee)
                                    <span>•</span>
                                    <span class="text-green-600 font-medium">Delivery: RM{{ number_format($order->delivery_fee, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Update Status Button -->
                        @if(!$order->isCompleted())
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                    <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                    <div class="py-2">
                                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                            Change Status To:
                                        </div>
                                        @foreach($order->getValidTransitions() as $transition)
                                            @if($transition === 'cancelled')
                                            <button 
                                                @click="orderId = {{ $order->id }}; showCancelModal = true; open = false; cancellationRemarks=''; cancellationError=''" 
                                                    class="block w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50 transition-colors"
                                                >
                                                    Cancel Order
                                                </button>
                                            @elseif($transition === 'delivering')
                                                <button 
                                                    @click="orderId = {{ $order->id }}; open = false; $dispatch('open-tracking-modal', { id: {{ $order->id }} })" 
                                                    class="block w-full text-left px-4 py-3 text-sm text-purple-700 hover:bg-purple-50 transition-colors"
                                                >
                                                    Mark as Delivering
                                                </button>
                                            @else
                                                <button 
                                                    wire:click="updateOrderStatus({{ $order->id }}, '{{ $transition }}')" 
                                                    class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                                >
                                                    Mark as {{ ucfirst($transition) }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Order Complete
                            </div>
                        @endif
                    </div>
                    
                    <!-- Order Items -->
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
                                        <div class="text-sm font-medium text-gray-900">
                                            RM{{ number_format($item->line_total, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($order->notes)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
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
                    
                    <!-- Cancellation Remarks -->
                    @if($order->status === 'cancelled' && $order->cancellation_remarks)
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
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
                    
                    <!-- Tracking Information -->
                    @if($order->status === 'delivering' && ($order->tracking_url || $order->delivery_fee))
                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
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
                    
                    <!-- Order Total -->
                    <div class="mt-auto flex justify-end">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">RM{{ number_format($order->total, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($this->pendingOrders->hasPages())
            <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    @if($this->pendingOrders->total() > 0)
                        Showing {{ $this->pendingOrders->firstItem() }} to {{ $this->pendingOrders->lastItem() }} of {{ $this->pendingOrders->total() }} results
                    @else
                        Showing 0 results
                    @endif
                </div>
                <div>
                    {{ $this->pendingOrders->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No active orders</h3>
            <p class="mt-1 text-sm text-gray-500">All orders are completed or cancelled.</p>
        </div>
    @endif

    <!-- Tracking URL Modal (embedded to keep single root) -->
    <div x-data="{ open: false }" 
         x-on:open-tracking-modal.window="open=true; $wire.trackingOrderId=$event.detail.id; $wire.trackingUrl=''; $wire.deliveryFee=''; $wire.clearTrackingValidation()" 
         x-on:close-tracking-modal.window="open=false"
         x-show="open" x-cloak @keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 transition-opacity z-40" @click="open = false"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Set Tracking URL</h3>
                    <p class="mt-1 text-sm text-gray-600">Provide a tracking link for this delivery.</p>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Tracking URL</label>
                        <input type="url" wire:model.live.debounce.300ms="trackingUrl" placeholder="https://..." class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" />
                        @error('trackingUrl')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Delivery Fee</label>
                        <input type="number" wire:model.live.debounce.300ms="deliveryFee" placeholder="0.00" step="0.01" min="0" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" />
                        @error('deliveryFee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="confirmDelivering" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">Confirm</button>
                    <button @click="open=false; $wire.trackingUrl=''; $wire.deliveryFee=''; $wire.clearTrackingValidation()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>