<div class="p-6 space-y-6" x-data>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Your Cart</h1>
        <a href="{{ route('menu') }}" class="text-sm underline">Continue order</a>
    </div>

    @if(empty($this->lines))
        <div class="text-gray-500">Your cart is empty.</div>
    @else
        <div class="bg-white border rounded overflow-x-auto">
            <table class="min-w-[720px] w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-4 py-2">Item</th>
                        <th class="px-4 py-2 w-40 text-center">Quantity</th>
                        <th class="px-4 py-2 w-32 text-right">Price</th>
                        <th class="px-4 py-2 w-28 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($this->lines as $line)
                    <tr class="align-middle">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @php
                                    $src = Str::startsWith($line['image_path'] ?? '', ['http://','https://']) ? $line['image_path'] : (($line['image_path'] ?? null) ? asset('storage/' . $line['image_path']) : null);
                                @endphp
                                <div class="w-20 h-14 bg-gray-100 rounded overflow-hidden">
                                    @if($src)
                                        <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $line['name'] }}" />
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium">{{ $line['name'] }}</div>
                                    <div class="text-sm text-gray-500">RM{{ number_format($line['price'], 2) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="decrement({{ $line['id'] }})" class="px-2 py-1 border rounded">-</button>
                                <div class="w-10 text-center">{{ $line['qty'] }}</div>
                                <button wire:click="increment({{ $line['id'] }})" class="px-2 py-1 border rounded">+</button>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">RM{{ number_format($line['price'] * $line['qty'], 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="remove({{ $line['id'] }})" class="text-red-600 text-sm">Remove</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="">
            <div class="text-sm text-gray-600 text-right ml-auto">
                <div>
                    Subtotal: <span class="font-medium text-gray-900">RM {{ number_format($this->totals['subtotal'], 2) }}</span>
                </div>
                <div>
                    Tax: <span class="font-medium text-gray-900">RM {{ number_format($this->totals['tax'], 2) }}</span>
                </div>
                ----------------
                <div class="text-lg font-semibold pl-6 ">Total: <span class="font-medium text-gray-900">RM{{ number_format($this->totals['total'], 2) }}</span>  </div>
            </div>
            
        </div>

        <div class="flex items-center justify-end gap-3">
            <button @click.prevent="if (confirm('Clear all items from your cart?')) { $wire.clear() }" class="px-4 py-2 border rounded cursor-pointer">Clear</button>
            <a href="{{ route('checkout') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Checkout</a>
        </div>
    @endif
</div>


