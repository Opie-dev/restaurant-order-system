<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Orders</h1>
    </div>

    <!-- Filters toolbar -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="o_search" class="block text-sm font-medium text-gray-700 mb-1">Search by order #</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </div>
                    <input id="o_search" type="text" placeholder="e.g. ABC123" wire:model.live.debounce.400ms="search" class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-500 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5" />
                </div>
            </div>
            <div class="w-full sm:w-64">
                <label for="o_status" class="block text-sm font-medium text-gray-700 mb-1">Payment status</label>
                <select id="o_status" wire:model.live="status" class="w-full rounded-lg border-2 border-gray-300 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-500 py-2.5">
                    @foreach($this->statuses as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($status !== 'all' || strlen($search) > 0)
            <div class="mt-3 text-right">
                <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition px-3 py-2 rounded-lg shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear filters
                </button>
            </div>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Items</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Subtotal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tax</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Payment</th>
                </tr>
            </thead>
            @foreach($this->orders as $order)
                <tbody x-data="{ open: false }" class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td class="px-2 py-3 align-top">
                            <button @click="open = !open" class="p-1 rounded hover:bg-gray-100">
                                <svg x-cloak x-show="!open" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                </svg>
                                <svg x-cloak x-show="open" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                </svg>
                            </button>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $order->code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user?->name ?? 'Guest' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->items->sum('qty') }}</td>
                        <td class="px-4 py-3 text-gray-900">RM{{ number_format($order->subtotal, 2) }}</td>
                        <td class="px-4 py-3 text-gray-900">RM{{ number_format($order->tax, 2) }}</td>
                        <td class="px-4 py-3 text-gray-900 font-semibold">RM{{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                @elseif($order->payment_status === 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->payment_status === 'refunded') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    <tr x-cloak x-show="open">
                        <td colspan="9" class="px-6 pb-4">
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="text-sm font-medium text-gray-700 mb-2">Order Items</div>
                                <div class="divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                        <div class="py-2 flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm text-gray-900">{{ $item->name_snapshot }}</div>
                                                <div class="text-xs text-gray-500">Qty: {{ $item->qty }} × RM{{ number_format($item->unit_price, 2) }}</div>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">RM{{ number_format($item->line_total, 2) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($order->notes)
                                    <div class="mt-3 text-sm text-gray-700"><span class="font-medium">Notes:</span> {{ $order->notes }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>

        @if($this->orders->hasPages())
            <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between">
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

        @if($this->orders->count() === 0)
            <div class="p-6 text-center text-gray-500">No orders yet.</div>
        @endif
    </div>
</div>
