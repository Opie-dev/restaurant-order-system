<div class="w-full px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Store Address</h1>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                <input type="text" wire:model.blur="address_line1" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('address_line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2 (optional)</label>
                <input type="text" wire:model.blur="address_line2" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
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

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700" wire:loading.attr="disabled" wire:target="save">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4z"/>
                    </svg>
                    <span>Save</span>
                </button>
            </div>
        </div>
    </form>
</div>


