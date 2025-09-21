<div class="p-6 space-y-6">
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

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Pending Orders</h1>
        <div class="flex items-center space-x-4">
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
    </div>


    <!-- Orders Grid -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        @if($this->pendingOrders->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                @foreach($this->pendingOrders as $order)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:bg-gray-100 transition-colors flex flex-col">
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
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>{{ $order->user?->name ?? 'Guest' }}</span>
                                    <span>•</span>
                                    <span>{{ $order->created_at->format('M j, Y g:i A') }}</span>
                                    <span>•</span>
                                    <span>{{ $order->items->sum('qty') }} items</span>
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
                                                <button wire:click="updateOrderStatus({{ $order->id }}, '{{ $transition }}')" class="block w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    Mark as {{ ucfirst($transition) }}
                                                </button>
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
                                                                @foreach($item->getSelectionsArray() as $type => $selections)
                                                                    @if(is_array($selections) && !empty($selections))
                                                                        <div class="flex items-start space-x-2">
                                                                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide min-w-0 flex-shrink-0">
                                                                                {{ ucfirst($type) }}:
                                                                            </span>
                                                                            <div class="flex flex-wrap gap-1">
                                                                                @foreach($selections as $selection)
                                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                                        {{ $selection }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending orders</h3>
                <p class="mt-1 text-sm text-gray-500">All orders are completed or cancelled.</p>
            </div>
        @endif
    </div>
</div>
