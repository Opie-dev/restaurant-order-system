<div class="w-full p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ $isEdit ? 'Edit' : 'Create' }} Menu Item</h1>
        <a href="{{ route('admin.menu.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Menu
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sticky Preview -->
        <aside class="order-2 lg:order-2 lg:col-span-1">
            <div x-data="{ showListPreview: true, showBasketPreview: true }" class="sticky top-20 space-y-4">
                <!-- Menu List Card Preview -->
                <div class="px-1">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-semibold text-gray-700">Menu List preview</div>
                        <button type="button" @click="showListPreview = !showListPreview" class="text-xs px-2 py-1 rounded border hover:bg-gray-50">
                            <span x-show="showListPreview">Hide</span>
                            <span x-show="!showListPreview">Show</span>
                        </button>
                    </div>
                </div>
                <div x-show="showListPreview" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-video bg-gray-100 overflow-hidden relative">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview" />
                        @elseif($isEdit && $menuItem && $menuItem->image_path)
                            <img src="{{ asset('storage/' . $menuItem->image_path) }}" class="w-full h-full object-cover" alt="Preview" />
                        @else
                            <div class="w-full h-full grid place-content-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($tag)
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs font-semibold text-white rounded-full bg-purple-600">{{ ucfirst($tag) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-800 text-lg truncate">{{ $name ?: 'Item name' }}</h3>
                            <span class="text-lg font-bold text-purple-600">RM {{ number_format((float) ($type === 'set' ? ($base_price ?? 0) : ($price ?? 0)), 2) }}</span>
                        </div>
                        <p class="text-gray-600 text-sm line-clamp-2">{{ $description ?: 'Item description will appear here' }}</p>
                        <div class="mt-3">
                            <button type="button" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium">Add to Order</button>
                        </div>
                    </div>
                </div>

                <!-- Add to Basket Modal Preview -->
                <div class="px-1 mt-2">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-semibold text-gray-700">Add to Basket preview</div>
                        <button type="button" @click="showBasketPreview = !showBasketPreview" class="text-xs px-2 py-1 rounded border hover:bg-gray-50">
                            <span x-show="showBasketPreview">Hide</span>
                            <span x-show="!showBasketPreview">Show</span>
                        </button>
                    </div>
                </div>
                <div x-show="showBasketPreview" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-video bg-gray-100 overflow-hidden relative">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview" />
                        @elseif($isEdit && $menuItem && $menuItem->image_path)
                            <img src="{{ asset('storage/' . $menuItem->image_path) }}" class="w-full h-full object-cover" alt="Preview" />
                        @else
                            <div class="w-full h-full grid place-content-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($tag)
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs font-semibold text-white rounded-full bg-purple-600">{{ ucfirst($tag) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-lg truncate">{{ $name ?: 'Item name' }}</h3>
                                @if($description)
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-3">{{ $description }}</p>
                                @else
                                    <p class="text-gray-400 text-sm mt-1">Item description will appear here</p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-lg font-bold text-gray-900">RM {{ number_format((float) ($type === 'set' ? ($base_price ?? 0) : ($price ?? 0)), 2) }}</div>
                                <div class="text-xs text-gray-500">Base price</div>
                            </div>
                        </div>

                        <!-- Options Section (matches customer modal) -->
                        @if(!empty($options))
                            <div class="space-y-4">
                                @foreach(($options ?? []) as $gIndex => $group)
                                    @if(true)
                                        <div class="border rounded-lg p-3 {{ ($group['enabled'] ?? true) ? '' : 'opacity-60' }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">{{ $group['name'] ?: 'Option Group' }}</h4>
                                                    @if(($group['rules'][0] ?? 'required') === 'optional')
                                                        <div class="text-xs text-gray-500 mt-0.5">optional</div>
                                                    @endif
                                                </div>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    Pick {{ ($group['rules'][1] ?? 'one') === 'multiple' ? 'Multiple' : '1' }}
                                                </span>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach(($group['options'] ?? []) as $oIndex => $opt)
                                                    <label wire:key="preview-opt-{{ $gIndex }}-{{ $oIndex }}" class="flex items-center gap-3 p-2 border rounded {{ !($opt['enabled'] ?? true) || !($group['enabled'] ?? true) ? 'opacity-60 bg-gray-50 text-gray-400 cursor-not-allowed' : 'hover:bg-gray-50' }}">
                                                        @php $multiple = ($group['rules'][1] ?? 'one') === 'multiple'; @endphp
                                                        @if($multiple)
                                                            <input type="checkbox" class="w-4 h-4 text-purple-600" @disabled(!($opt['enabled'] ?? true) || !($group['enabled'] ?? true)) />
                                                        @else
                                                            <input type="radio" name="preview-opt-{{ $gIndex }}" class="w-4 h-4 text-purple-600" @disabled(!($opt['enabled'] ?? true) || !($group['enabled'] ?? true)) />
                                                        @endif
                                                        <span class="flex-1 font-medium text-sm">{{ $opt['name'] ?? '' }}</span>
                                                    </label>
                                                @endforeach
                                                @if(empty($group['options']))
                                                    <div class="text-xs text-gray-500">No options added yet.</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- Addons Section (matches customer modal) -->
                        @if(!empty($addons))
                            <div class="space-y-4 mt-4">
                                @foreach(($addons ?? []) as $gIndex => $group)
                                    @if(true)
                                        <div class="border rounded-lg p-3 {{ ($group['enabled'] ?? true) ? '' : 'opacity-60' }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">{{ $group['name'] ?: 'Addon Group' }}</h4>
                                                    @if(($group['rules'][0] ?? 'required') === 'optional')
                                                        <div class="text-xs text-gray-500 mt-0.5">optional</div>
                                                    @endif
                                                </div>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    Pick {{ ($group['rules'][1] ?? 'one') === 'multiple' ? 'Multiple' : '1' }}
                                                </span>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach(($group['options'] ?? []) as $oIndex => $opt)
                                                    @php if (empty(trim($opt['name'] ?? ''))) { continue; } @endphp
                                                    <label wire:key="preview-addon-{{ $gIndex }}-{{ $oIndex }}" class="flex items-center gap-3 p-2 border rounded {{ !($opt['enabled'] ?? true) || !($group['enabled'] ?? true) ? 'opacity-60 bg-gray-50 text-gray-400 cursor-not-allowed' : 'hover:bg-gray-50' }}">
                                                        @php $multiple = ($group['rules'][1] ?? 'one') === 'multiple'; @endphp
                                                        @if($multiple)
                                                            <input type="checkbox" class="w-4 h-4 text-purple-600" @disabled(!($opt['enabled'] ?? true) || !($group['enabled'] ?? true)) />
                                                        @else
                                                            <input type="radio" name="preview-addon-{{ $gIndex }}" class="w-4 h-4 text-purple-600" @disabled(!($opt['enabled'] ?? true) || !($group['enabled'] ?? true)) />
                                                        @endif
                                                        <span class="flex-1 font-medium text-sm">{{ $opt['name'] ?? '' }}</span>
                                                        @if(isset($opt['price']))
                                                            <span class="text-sm font-semibold text-gray-900">+RM {{ number_format((float) ($opt['price'] ?? 0), 2) }}</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                                @php $nonEmpty = collect($group['options'] ?? [])->filter(fn($x) => !empty(trim($x['name'] ?? ''))); @endphp
                                                @if($nonEmpty->isEmpty())
                                                    <div class="text-xs text-gray-500">No addon options added yet.</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </aside>

        <!-- Form -->
        <div class="order-1 lg:order-1 lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" wire:model.blur="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model.blur="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                        @error('description') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($type === 'set')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base Price</label>
                                <input type="number" step="0.01" min="0" wire:model.blur="base_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                @error('base_price') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                        @elseif($type === 'ala_carte')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                                <input type="number" step="0.01" min="0" wire:model.blur="price" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                @error('price') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <input type="number" step="1" min="0" wire:model.blur="stock" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('stock') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select wire:model.blur="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select wire:model.live="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="ala_carte">Ala Carte</option>
                                <option value="set">Set</option>
                            </select>
                            @error('type') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tag</label>
                            <select wire:model.blur="tag" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">None</option>
                                <option value="popular">Popular</option>
                                <option value="bestseller">Bestseller</option>
                            </select>
                            @error('tag') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-8">
                        <div class="flex items-center gap-3">
                            <input id="is_active" type="checkbox" wire:model.live="is_active" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                            <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <input id="enabled" type="checkbox" wire:model.live="enabled" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                            <label for="enabled" class="text-sm font-medium text-gray-700">Enabled</label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <input type="file" wire:model="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('image') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($image)
                                <div>
                                    <div class="text-sm font-medium text-gray-700 mb-2">Preview</div>
                                    <img src="{{ $image->temporaryUrl() }}" class="rounded-lg border border-gray-200 w-full h-32 object-cover" />
                                </div>
                            @endif
                            @if($isEdit && $menuItem && $menuItem->image_path)
                                <div>
                                    <div class="text-sm font-medium text-gray-700 mb-2">Current</div>
                                    <img src="{{ asset('storage/' . $menuItem->image_path) }}" class="rounded-lg border border-gray-200 w-full h-32 object-cover" />
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Options Section (for all items) -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Option Groups</h3>
                        <div class="space-y-4">
                            @foreach($options as $groupIndex => $group)
                                <div class="border border-gray-200 rounded-lg p-4" wire:key="opt-group-{{ $groupIndex }}">
                                    <div class="flex items-center gap-3 mb-3">
                                        <input type="text" wire:model.live="options.{{ $groupIndex }}.name" placeholder="Group name" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                        <select wire:model.live="options.{{ $groupIndex }}.rules.0" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="required">Required</option>
                                            <option value="optional">Optional</option>
                                        </select>
                                        <select wire:model.live="options.{{ $groupIndex }}.rules.1" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="one">One</option>
                                            <option value="multiple">Multiple</option>
                                        </select>
                                        <button type="button" wire:click="removeOptionGroup({{ $groupIndex }})" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Remove Group">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('options.' . $groupIndex . '.name')
                                        <div class="text-sm text-red-600 mb-2">{{ $message }}</div>
                                    @enderror
                                    <div class="space-y-3">
                                        @foreach($group['options'] ?? [] as $optionIndex => $option)
                                            <div class="flex items-center gap-3" wire:key="opt-{{ $groupIndex }}-{{ $optionIndex }}">
                                                <input type="text" 
                                                       wire:model.live="options.{{ $groupIndex }}.options.{{ $optionIndex }}.name" 
                                                       placeholder="Option name" 
                                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" 
                                                           wire:model.live="options.{{ $groupIndex }}.options.{{ $optionIndex }}.enabled" 
                                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                                                    <span class="text-sm text-gray-600">Enabled</span>
                                                </div>
                                                <button type="button" 
                                                        wire:click="removeOptionOption({{ $groupIndex }}, {{ $optionIndex }})" 
                                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            @error('options.' . $groupIndex . '.options.' . $optionIndex . '.name')
                                                <div class="text-sm text-red-600">{{ $message }}</div>
                                            @enderror
                                        @endforeach
                                        @error('options.' . $groupIndex . '.options')
                                            <div class="text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                            <button type="button" 
                                                    wire:click="addOptionOption({{ $groupIndex }})" 
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium">+ Add Option</button>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" 
                                                       wire:click="toggleOptionGroupEnabled({{ $groupIndex }})" 
                                                       @checked($group['enabled'] ?? true)
                                                       class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                                                <span class="text-sm font-medium {{ ($group['enabled'] ?? true) ? 'text-gray-700' : 'text-gray-500' }}">
                                                    Enable this option group
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addOptionGroup" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-indigo-600 hover:border-indigo-400 hover:bg-indigo-50 transition-colors font-medium">+ Add Option Group</button>
                        </div>
                    </div>

                    <!-- Addons Section (for all items) -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Addon Groups</h3>
                        <div class="space-y-4">
                            @foreach($addons as $groupIndex => $group)
                                <div class="border border-gray-200 rounded-lg p-4" wire:key="addon-group-{{ $groupIndex }}">
                                    <div class="flex items-center gap-3 mb-3">
                                        <input type="text" wire:model.live="addons.{{ $groupIndex }}.name" placeholder="Group name" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                        <select wire:model.live="addons.{{ $groupIndex }}.rules.0" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="required">Required</option>
                                            <option value="optional">Optional</option>
                                        </select>
                                        <select wire:model.live="addons.{{ $groupIndex }}.rules.1" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="one">One</option>
                                            <option value="multiple">Multiple</option>
                                        </select>
                                        <button type="button" wire:click="removeAddonGroup({{ $groupIndex }})" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Remove Group">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('addons.' . $groupIndex . '.name')
                                        <div class="text-sm text-red-600 mb-2">{{ $message }}</div>
                                    @enderror
                                    <div class="space-y-3">
                                        @foreach($group['options'] ?? [] as $optionIndex => $option)
                                            <div class="flex items-center gap-3" wire:key="addon-{{ $groupIndex }}-{{ $optionIndex }}">
                                                <input type="text" 
                                                       wire:model.live="addons.{{ $groupIndex }}.options.{{ $optionIndex }}.name" 
                                                       placeholder="Option name" 
                                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                                <input type="number" 
                                                       step="0.01" 
                                                       wire:model.live="addons.{{ $groupIndex }}.options.{{ $optionIndex }}.price" 
                                                       placeholder="Price" 
                                                       class="w-24 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" 
                                                           wire:model.live="addons.{{ $groupIndex }}.options.{{ $optionIndex }}.enabled" 
                                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                                                    <span class="text-sm text-gray-600">Enabled</span>
                                                </div>
                                                <button type="button" 
                                                        wire:click="removeAddonOption({{ $groupIndex }}, {{ $optionIndex }})" 
                                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            @if($errors->has('addons.' . $groupIndex . '.options.' . $optionIndex . '.name') || $errors->has('addons.' . $groupIndex . '.options.' . $optionIndex . '.price'))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    @error('addons.' . $groupIndex . '.options.' . $optionIndex . '.name')
                                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                                    @enderror
                                                    @error('addons.' . $groupIndex . '.options.' . $optionIndex . '.price')
                                                        <div class="text-sm text-red-600">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif
                                        @endforeach
                                        @error('addons.' . $groupIndex . '.options')
                                            <div class="text-sm text-red-600">{{ $message }}</div>
                                        @enderror
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                            <button type="button" 
                                                    wire:click="addAddonOption({{ $groupIndex }})" 
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium">+ Add Option</button>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" 
                                                       wire:click="toggleAddonGroupEnabled({{ $groupIndex }})" 
                                                       @checked($group['enabled'] ?? true)
                                                       class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" />
                                                <span class="text-sm font-medium {{ ($group['enabled'] ?? true) ? 'text-gray-700' : 'text-gray-500' }}">
                                                    Enable this addon group
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addAddonGroup" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-indigo-600 hover:border-indigo-400 hover:bg-indigo-50 transition-colors font-medium">+ Add Addon Group</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
