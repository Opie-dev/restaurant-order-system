<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Menu Items</h1>
        <a href="{{ route('admin.menu.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create Item</a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search items..." class="w-full border rounded px-3 py-2" />
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between">
            <div class="w-full sm:flex-1 overflow-x-auto pb-2 sm:pb-0">
                @include('livewire.admin.menu._category-chips', ['categories' => $this->categories, 'categoryId' => $categoryId, 'limit' => 5])
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                <select wire:model.live="active" class="border rounded px-3 py-2 text-sm">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select wire:model.live="sort" class="border rounded px-3 py-2 text-sm">
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
            $itemsByCategory = $this->items->groupBy(function($item) {
                return $item->category?->name ?? 'Uncategorized';
            });
        @endphp
        
        @forelse($itemsByCategory as $categoryName => $items)
            <div class="col-span-full">
                <h3 class="text-lg font-semibold mb-4">{{ $categoryName }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                @foreach($items as $item)
                    <div class="border rounded p-3 flex flex-col gap-2">
                        <div class="aspect-video bg-gray-100 rounded overflow-hidden relative">
                            @php
                                $src = Str::startsWith($item->image_path, ['http://','https://']) ? $item->image_path : ($item->image_path ? asset('storage/' . $item->image_path) : null);
                            @endphp
                            @if($src)
                                <img src="{{ $src }}" class="w-full h-full object-cover" alt="{{ $item->name }}" />
                            @else
                                <div class="w-full h-full grid place-content-center text-gray-400">No image</div>
                            @endif

                            @if($item->tag)
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs font-semibold text-white rounded-full bg-purple-600">{{ ucfirst($item->tag) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->category?->name ?? 'Uncategorized' }}</div>
                                <div class="text-xs text-gray-500">Stock: {{ (int)($item->stock ?? 0) }}</div>
                                @if($item->type)
                                    <div class="text-xs text-blue-600">{{ ucfirst($item->type) }}</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="font-semibold">RM {{ number_format($item->price, 2) }}</div>
                                @if($item->base_price && $item->type === 'set')
                                    <div class="text-xs text-gray-500">Base: RM {{ number_format($item->base_price, 2) }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="px-2 py-1 rounded {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="text-gray-700 hover:text-gray-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                    <a href="{{ route('admin.menu.edit', $item) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                    <button wire:click="toggleActive({{ $item->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ $item->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button @click="if(confirm('Delete this item?')) $wire.deleteItem({{ $item->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500">No items found.</div>
        @endforelse
    </div>
</div>
