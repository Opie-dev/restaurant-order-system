<div class="w-full px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Store Details</h1>

    <form wire:submit.prevent="save" class="space-y-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
            <input type="text" wire:model.blur="store_name" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
            @error('store_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea wire:model.blur="description" rows="3" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="Brief description of your restaurant"></textarea>
            @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <!-- Logo Upload Section -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
            <div class="space-y-4">
                <!-- Current Logo Display -->
                @if($logo_path)
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img src="{{ Storage::url($logo_path) }}" alt="Current logo" class="h-16 w-16 object-contain border border-gray-300 rounded-lg">
                        </div>
                        <div class="text-sm text-gray-600">
                            Current logo
                        </div>
                    </div>
                @endif

                <!-- Logo Upload Input -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file" wire:model="logo" accept="image/*" class="hidden" id="logo-upload">
                    <label for="logo-upload" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="mt-2">
                            <span class="text-sm font-medium text-purple-600 hover:text-purple-500">
                                {{ $logo_path ? 'Change logo' : 'Upload a logo' }}
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
                                <div class="text-xs text-gray-500 mt-1">This will replace your current logo when you save changes</div>
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
            @error('logo')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
            <input type="text" wire:model.blur="address_line1" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="Street address, building number" />
            @error('address_line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2 (optional)</label>
            <input type="text" wire:model.blur="address_line2" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="Apartment, suite, unit, building, floor, etc." />
            @error('address_line2')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <input type="text" wire:model.blur="city" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('city')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                <input type="text" wire:model.blur="state" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('state')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                <input type="text" wire:model.blur="postal_code" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('postal_code')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="tel" wire:model.blur="phone" pattern="[0-9+\-\s()]+" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="e.g. +60 12-345-6789" />
                @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" wire:model.blur="email" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    These details will be used across your restaurant system
                </div>
                <div class="flex space-x-3">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
