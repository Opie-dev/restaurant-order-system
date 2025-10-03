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

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3" x-data="{saved:false}" x-on:address-saved.window="saved=true; setTimeout(()=> saved=false, 1200)">
                <div x-show="saved" x-cloak class="text-sm text-green-700 bg-green-100 px-3 py-1 rounded inline-flex items-center gap-2"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1">
                    <svg class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Saved
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Save Address</button>
            </div>
        </div>
    </form>
</div>


