<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-4xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Select Store</h2>
            <p class="mt-2 text-sm text-gray-600">Choose a store to manage</p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Create Store Form -->
        @if($showCreateForm)
            <div class="mb-8">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Store</h3>
                        <p class="text-sm text-gray-600">Fill in the details to create a new store</p>
                    </div>
                    
                    <form wire:submit="createStore" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Store Name *
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name" 
                                    id="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                                    placeholder="Enter store name"
                                    required
                                >
                                @error('name') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Store URL Slug *
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        {{ config('app.url') }}/menu/
                                    </span>
                                    <input 
                                        type="text" 
                                        wire:model="slug" 
                                        id="slug"
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="store-slug"
                                        required
                                    >
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens allowed. This will be your store's unique URL.</p>
                                @error('slug') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="description" 
                                    id="description"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Brief description of your restaurant"
                                ></textarea>
                                @error('description') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Logo Upload Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
                                <div class="space-y-4">
                                    <!-- Logo Upload Input -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                        <input type="file" wire:model="logo" accept="image/*" class="hidden" id="logo-upload">
                                        <label for="logo-upload" class="cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="mt-2">
                                                <span class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                                    Upload a logo
                                                </span>
                                                <span class="text-sm text-gray-500"> or drag and drop</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                        </label>
                                    </div>

                                    <!-- New Logo Preview -->
                                    @if($logo)
                                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $logo->temporaryUrl() }}" alt="New logo preview" class="h-16 w-16 object-contain border border-gray-300 rounded-lg">
                                                </div>
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900">New logo preview</div>
                                                    <div class="text-sm text-gray-600">{{ $logo->getClientOriginalName() }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">This will be your store logo</div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <button type="button" wire:click="$set('logo', null)" class="text-red-600 hover:text-red-800 text-sm">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('logo') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address Line 1 *
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="address_line1" 
                                    id="address_line1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Street address, building number"
                                    required
                                >
                                @error('address_line1') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div>
                                <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address Line 2 (optional)
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="address_line2" 
                                    id="address_line2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Apartment, suite, unit, building, floor, etc."
                                >
                                @error('address_line2') 
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        City *
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="city" 
                                        id="city"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="City"
                                        required
                                    >
                                    @error('city') 
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                        State *
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="state" 
                                        id="state"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="State"
                                        required
                                    >
                                    @error('state') 
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Postal Code *
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="postal_code" 
                                        id="postal_code"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Postal Code"
                                        required
                                    >
                                    @error('postal_code') 
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone *
                                    </label>
                                    <input 
                                        type="tel" 
                                        wire:model="phone" 
                                        id="phone"
                                        pattern="[0-9+\-\s()]+"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="e.g. +60 12-345-6789"
                                        required
                                    >
                                    @error('phone') 
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email *
                                    </label>
                                    <input 
                                        type="email" 
                                        wire:model="email" 
                                        id="email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="store@example.com"
                                        required
                                    >
                                    @error('email') 
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                wire:click="$set('showCreateForm', false)"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Create Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        
        <div class="mt-6 text-right">
            <button 
                wire:click="$set('showCreateForm', true)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Store
            </button>
        </div>
        @if($stores->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stores as $store)
                    <div 
                        class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 cursor-pointer border-2 {{ $selectedStoreId == $store->id ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300' }}"
                        wire:click="$set('selectedStoreId', {{ $store->id }})"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $store->name }}</h3>
                                    <p class="text-xs text-gray-500">/menu/{{ $store->slug }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($selectedStoreId == $store->id)
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <button 
                                        wire:click.stop="deleteStore({{ $store->id }})"
                                        wire:confirm="Are you sure you want to delete '{{ $store->name }}'? This will permanently remove the store and all its associated data including menu items, categories, and orders. This action cannot be undone."
                                        class="p-1 text-red-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 rounded"
                                        title="Delete store"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            @if($store->description)
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($store->description, 100) }}</p>
                            @endif
                            
                            <div class="space-y-2 text-sm text-gray-500">
                                @if($store->address_line1 || $store->city)
                                    <div class="flex items-start">
                                        <svg class="h-4 w-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div>
                                            @if($store->address_line1)
                                                <div>{{ $store->address_line1 }}</div>
                                            @endif
                                            @if($store->address_line2)
                                                <div>{{ $store->address_line2 }}</div>
                                            @endif
                                            @if($store->city || $store->state || $store->postal_code)
                                                <div>
                                                    {{ trim(collect([$store->city, $store->state, $store->postal_code])->filter()->join(', ')) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($store->phone)
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $store->phone }}
                                    </div>
                                @endif
                                
                                @if($store->email)
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $store->email }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>Created {{ $store->created_at->format('M j, Y') }}</span>
                                    <span class="px-2 py-1 rounded-full {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $store->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                @if($selectedStoreId)
                    <button 
                        wire:click="selectStore"
                        class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Continue to Dashboard
                    </button>
                @else
                    <p class="text-gray-500 text-sm">Please select a store to continue</p>
                @endif
            </div>
        @else
            <div class="text-center">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No stores found</h3>
                    <p class="mt-2 text-sm text-gray-600">You don't have any stores yet. Create your first store to get started.</p>
                    <div class="mt-6">
                        <button 
                            wire:click="$set('showCreateForm', true)"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Your First Store
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>